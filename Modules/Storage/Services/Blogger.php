<?php

namespace Modules\Storage\Services;

class Blogger
{

    /**
     * @var \GluzzleHttp\Client $client
     */

    public $client;

    protected $cookie;

    protected $chunk_size = 8; // chuck of each upload prallel request

    protected $sid;

    protected $resumable_index = 0;

    protected $files;

    protected $putInfos;



    public $bypass = "iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAIAAACQd1PeAAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAAJcEhZcwAADsMAAA7DAcdvqGQAAAAMSURBVBhXY/j//z8ABf4C/qc1gYQAAAAASUVORK5CYIIAlWQMYkxPYg==";

    /**
     * Blogger constructor.
     */

    public function __construct()
    {
        $this->cookie = config('storage.blogger.cookie');

        $dateUtc = time();
        $sapiSid = $this->getSid();
        $origin = "https://voice.google.com";
        $sidHash = sha1("{$dateUtc} {$sapiSid} {$origin}");

        $this->client = new \GuzzleHttp\Client([
            'headers' => [
                'cookie' => $this->cookie,
                'authorization' => "SAPISIDHASH {$dateUtc}_{$sidHash}",
                'origin' => $origin,
                'content-type' => 'application/json+protobuf',
            ],
            'verify' => false,
            'on_stats' => function (\GuzzleHttp\TransferStats $stats) {
                // show total in one line cli progress bar
                echo "\rRunning time:" . $stats->getTransferTime() . "s";
            }
        ]);


        $this->bypass = base64_decode($this->bypass);
    }

    /**
     * @param $url
     * @return mixed
     */

    public function multiUploadFile($files)
    {
        // chuck files
        $files = array_chunk($files, $this->chunk_size);
        $getReturnUrls = [];

        foreach ($files as $file) {
            $this->files = $file;
            $this->putInfos = [];

            // upload id
            $this->putInfos = $this->getPutInfoUrls();

            // upload and try put again if fail
            $this->uploadFiles();

            $getReturnUrls  = array_merge($getReturnUrls, $this->getReturnUrls());
        }


        echo PHP_EOL . count($getReturnUrls) . " files uploaded" . PHP_EOL;

        return  $getReturnUrls;
    }

    private function getReturnUrls(){
        $returnUrls = [];
        ksort($this->putInfos);

        foreach ($this->putInfos as $putInfo) {
            $returnUrls[] = $putInfo['upload_id'];
        }

        return $returnUrls;
    }

    private function uploadFiles()
    {
        $promises = [];

        foreach ($this->putInfos as $putInfo) {
            $promises[] = $this->client->requestAsync('PUT', $putInfo['url'], [
                'headers' => [
                    'content-length' => $putInfo['file_size'],
                    'cookie' => $this->cookie,
                    'Content-Type' => 'application/octet-stream',
                ], 'body' => $this->bypass . file_get_contents($putInfo['file'])
            ])->then(function ($response) use ($putInfo) {
                $content = $response->getBody()->getContents();
                $this->putInfos[$putInfo['index']]['status'] = 'fail';

                if (strpos($content, 'FINALIZED') !== false) {
                    $this->putInfos[$putInfo['index']]['status'] = 'success';

                    preg_match('/"upload_id":"(.*?)"/', $content, $matches);
                    $this->putInfos[$putInfo['index']]['upload_id'] = $matches[1];
                }
            }, function ($reason) use ($putInfo) {
                if ($reason->getCode() >= 500) {
                    $this->putInfos[$putInfo['index']]['status'] = 'queue';
                }
            });
        }

        $promises = \GuzzleHttp\Promise\Utils::settle($promises)->wait();

        // try again if fail
        do {
            $queues = [];
            foreach ($this->putInfos as $putInfo) {
                if ($putInfo['status'] == 'queue') {
                    $queues[] = $putInfo;
                }
            }

            if (count($queues) == 0) {
                break;
            }

            $promises = [];

            $client = new \GuzzleHttp\Client([
                'verify' => false,
                'headers' => [
                    'Content-Range' => 'bytes */*',
                ],
            ]);

            foreach ($queues as $putInfo) {
                $promises[] = $client->putAsync($putInfo['url'])->then(function ($response) use ($putInfo) {
                    $this->putInfos[$putInfo['index']]['status'] = 'fail';

                    $content = $response->getBody()->getContents();
                    if (strpos($content, 'FINALIZED') !== false) {
                        $this->putInfos[$putInfo['index']]['status'] = 'success';

                        // uploadID
                        preg_match('/"upload_id":"(.*?)"/', $content, $matches);
                        $this->putInfos[$putInfo['index']]['upload_id'] = $matches[1];

                        $this->putInfos[$putInfo['index']]['status'] = 'success';
                    }
                }, function ($reason) use ($putInfo) {
                    // if 503 error, try again
                    if ($reason->getCode() >= 500) {
                        $this->putInfos[$putInfo['index']]['status'] = 'queue';
                    } else {
                        $this->putInfos[$putInfo['index']]['status'] = 'fail';
                    }
                });
            }

            $promises = \GuzzleHttp\Promise\Utils::settle($promises)->wait();
        } while (count($queues) > 0);
    }


    public function getSid()
    {
        $cookie = $this->cookie;
        preg_match('/SAPISID=(.*?);/', $cookie, $matches);
        $this->sid = $matches[1];

        return $this->sid;
    }

    function getResumable()
    {
        $randomList = [
            "https://plus.google.com/_/upload/photos/resumable",
            "https://photos.google.com/_/upload/photos/resumable",
            "https://docs.google.com/upload/photos/resumable",
            "https://mail.google.com/upload/photos/resumable",
            "https://drive.google.com/upload/photos/resumable",
        ];

        return $randomList[$this->resumable_index++ % count($randomList)];
    }

    public function getPutInfoUrls()
    {
        $promises = [];
        $file_index = 0;

        foreach ($this->files as $file) {
            $file_size = filesize($file) + strlen($this->bypass);

            $getResumable = $this->getResumable();

            $promises[] = $this->client->requestAsync('POST', $getResumable, [
                'body' => $this->getBody($file_size),
            ])->then(function ($response) use ($file_index, $file, $file_size) {
                $this->putInfos[$file_index] = [
                    'status' => 'start',
                    'index' => $file_index,
                    'file' => $file,
                    'file_size' => $file_size,
                    'url' => $response->getHeader('Location')[0]
                ];

            }, function ($reason) {
                dd($reason->getMessage());
            });

            $file_index++;
        }

        $promises = \GuzzleHttp\Promise\Utils::settle($promises)->wait();

        return $this->putInfos;
    }

    function getBody($file_size = 0)
    {
        $dateUtc = intval(microtime(true) * 1000);

        return sprintf('{"protocolVersion":"0.8","createSessionRequest":{"fields":[{"external":{"name":"file","filename":"unnamed.png","put":{},"size":%d}},{"inlined":{"name":"streamid","content":"google-voice","contentType":"text/plain"}},{"inlined":{"name":"disable_asbe_notification","content":"true","contentType":"text/plain"}},{"inlined":{"name":"silo_id","content":"26","contentType":"text/plain"}},{"inlined":{"name":"title","content":"unnamed.png","contentType":"text/plain"}},{"inlined":{"name":"addtime","content":"%s","contentType":"text/plain"}},{"inlined":{"name":"onepick_host_id","content":"google-voice","contentType":"text/plain"}},{"inlined":{"name":"onepick_version","content":"v1","contentType":"text/plain"}},{"inlined":{"name":"c189022504","content":"true","contentType":"text/plain"}},{"inlined":{"name":"batchid","content":"%s","contentType":"text/plain"}},{"inlined":{"name":"album_id","content":"7180155099811226945","contentType":"text/plain"}},{"inlined":{"name":"album_abs_position","content":"0","contentType":"text/plain"}},{"inlined":{"name":"client","content":"google-voice","contentType":"text/plain"}}]}}', $file_size, $dateUtc, $dateUtc);
    }



    public function getDownloadUrl($albumarchive)
    {
        $response = $this->client->request('GET', $albumarchive, [
            'headers' => [
                'cookie' => $this->cookie,
            ],
        ]);

        $content = $response->getBody()->getContents();

        $photoMediaKey = basename(parse_url($albumarchive, PHP_URL_PATH));

        $pattern = "data-mk=\"{$photoMediaKey}\".*data-dlu=\"(.*?)\".*";
        preg_match("/{$pattern}/", $content, $matches);

        return $matches[1];
    }
}
