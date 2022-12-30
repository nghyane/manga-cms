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

            ],
            'verify' => false,
        ]);

        $this->bypass = base64_decode($this->bypass);
    }

    /**
     * @param $url
     * @return mixed
     */

    public function multiUploadFile($files)
    {

        // make chunk of files
        $putInfos = $this->getPutInfoUrls($files);
        $putInfos = array_chunk($putInfos, $this->chunk_size);
        $files = array_chunk($files, $this->chunk_size);


        $chunk_uploaded = [];

        foreach ($files as $index => $files_chuck) {
            // merge putInfos to uploaded
            $chunk_uploaded[] = $this->putImages($files_chuck, $putInfos[$index]);
        }

        return array_merge(...$chunk_uploaded);
    }


    public function putImages($files, $putInfos)
    {
        $promises = [];
        $photoUrls = [];

        foreach ($files as $index => $file) {
            $file_content = $this->bypass . file_get_contents($file);

            $promises[] = $this->client->requestAsync('PUT', $putInfos[$index]['url'], [
                'headers' => [
                    'cookie' => $this->cookie,
                    'Content-Type' => 'application/octet-stream',
                    'Content-Length' => filesize($file) + strlen($this->bypass),
                ],
                'body' => $file_content
            ]);
        }

        $eachPromise = new \GuzzleHttp\Promise\EachPromise($promises, [
            'fulfilled' => function ($response, $index) use (&$photoUrls) {
                $content = $response->getBody()->getContents();
                $content = json_decode($content, true);

                $info = $content['sessionStatus']['additionalInfo']['uploader_service.GoogleRupioAdditionalInfo']['completionInfo']['customerSpecificInfo'];

                $photoPageUrl = $info['photoPageUrl'];
                preg_match('/authkey=(.*)#/', $photoPageUrl, $matches);
                $authKey = $matches[1];


                $albumarchive_url = sprintf("https://get.google.com/albumarchive/%s/album/%s/%s?authKey=%s",
                    $info['username'],
                    $info['albumMediaKey'],
                    $info['photoMediaKey'],
                    $authKey
                );

                $photoUrls[$index] = $albumarchive_url;

                echo $index. PHP_EOL;
            },
            'rejected' => function ($reason, $index) {
                die($reason);
            },
        ]);

        $eachPromise->promise()->wait();
        ksort($photoUrls);

        return $photoUrls;
    }


    public function getSid()
    {
        $cookie = $this->cookie;
        preg_match('/SAPISID=(.*?);/', $cookie, $matches);
        $this->sid = $matches[1];

        return $this->sid;
    }

    function getResumable(){
        $randomList = [
            "https://plus.google.com/_/upload/photos/resumable",
            "https://docs.google.com/upload/photos/resumable",
            "https://photos.google.com/_/upload/photos/resumable",
            "https://mail.google.com/upload/photos/resumable",
            "https://drive.google.com/upload/photos/resumable",
        ];

        return $randomList[$this->resumable_index++ % count($randomList)];
    }

    public function getPutInfoUrls($files)
    {
        $promises = [];
        $data = [];


        foreach ($files as $file) {
            $file_size = filesize($file) + strlen($this->bypass);
            $getResumable = $this->getResumable();

            $promises[] = $this->client->requestAsync('POST', $getResumable, [
                'body' => $this->getBody($file_size),
            ]);
        }

        $eachPromise = new \GuzzleHttp\Promise\EachPromise($promises, [
            'fulfilled' => function ($response, $index) use (&$data) {

                $content = $response->getBody()->getContents();
                $content = json_decode($content, true);

                $status = $content['sessionStatus']['state'];
                if ($status != 'OPEN') {
                    die('Error');
                    return;
                }


                $data[$index] = [
                    'url' => $content['sessionStatus']['externalFieldTransfers'][0]['putInfo']['url'],
                    'upload_id' => $content['sessionStatus']['upload_id'],
                ];
            },
            'rejected' => function ($reason, $index) {
                // this is delivered each failed request
            },
        ]);

        $eachPromise->promise()->wait();
        ksort($data);

        return  $data;
    }

    function getBody($file_size = 0)
    {
        $dateUtc = intval(microtime(true) * 1000);

        return sprintf('{"protocolVersion":"0.8","createSessionRequest":{"fields":[{"external":{"name":"file","filename":"unnamed.png","put":{},"size":%d}},{"inlined":{"name":"streamid","content":"google-voice","contentType":"text/plain"}},{"inlined":{"name":"disable_asbe_notification","content":"true","contentType":"text/plain"}},{"inlined":{"name":"silo_id","content":"26","contentType":"text/plain"}},{"inlined":{"name":"title","content":"unnamed.png","contentType":"text/plain"}},{"inlined":{"name":"addtime","content":"%s","contentType":"text/plain"}},{"inlined":{"name":"onepick_host_id","content":"google-voice","contentType":"text/plain"}},{"inlined":{"name":"onepick_version","content":"v1","contentType":"text/plain"}},{"inlined":{"name":"c189022504","content":"true","contentType":"text/plain"}},{"inlined":{"name":"batchid","content":"%s","contentType":"text/plain"}},{"inlined":{"name":"album_id","content":"7180155099811226945","contentType":"text/plain"}},{"inlined":{"name":"album_abs_position","content":"0","contentType":"text/plain"}},{"inlined":{"name":"client","content":"google-voice","contentType":"text/plain"}}]}}', $file_size, $dateUtc, $dateUtc);
    }


    public function getDownloadUrl($albumarchive){
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
