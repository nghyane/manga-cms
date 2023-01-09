<?php

namespace Modules\Storage\Services;

use SebastianBergmann\CodeCoverage\Report\PHP;

class Tistory
{
    /**
     * @var \GluzzleHttp\Client $client
     */

    public $client;

    public $cookie = "TSSESSION_KEEP=1; TSSESSION=e2cca1d6710d2fbc2957ff0e613da16f60a99610; __T_=1; _T_ANO=WF+2abxo69HVzNLUbHCSnL7iTPkyXFMubL7hhyiJm+va4BWpt2WnrgyuPb//k0XraROjkL1Fm/iXowEvEom9EvbjGkdFwv36cDvrxjA0n3P1ALdIC3+t0rZLYoqqqYAJUxSo+TLCtLG+qV5y5nLtzPjDwy4UA1PV+iyNg4YqWLe9/TDEAejJ8HYvhy8kxaJyL9SAXLbDy0asPZYfOLqHAVNtsciEVw5qHw3zFyOdMQ+b0mTF7TGutnMfPKwb/v8ZQ14YCS39aRyhekpRD+VdIJ4NKmwlcqHSym1lP6tcixRnyYEos/DE+5t2KRg2zsleWURIhbGfytTaPEXMXOqH9w==";

    public $access_token = "190498a67cba148705269b12b92e3d6c_cfebb421f299749b0f5b215e9887ec8e";

    public $uploadeds = [];

    public $bypass = "iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAIAAACQd1PeAAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAAJcEhZcwAADsMAAA7DAcdvqGQAAAAMSURBVBhXY/j//z8ABf4C/qc1gYQAAAAASUVORK5CYIIAlWQMYkxPYg==";
    // iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII=

    public $transfer_time = 0;

    public $total_uploaded = 0;

    public $index_file = 0;

    public function __construct()
    {
        $this->client = new \GuzzleHttp\Client([
            'verify' => false,
            'headers' => [
                'cookie' => $this->cookie,
                'content-type' => 'multipart/form-data',
            ],
            'on_stats' => function (\GuzzleHttp\TransferStats $stats) {
                $this->transfer_time += $stats->getTransferTime();
                // show total in one line cli progress bar
                echo "\rUpload time: " . $this->transfer_time . "s | Total uploaded: " . $this->total_uploaded++ . " files";
            }
        ]);

        $this->bypass = base64_decode($this->bypass);
    }


    public function multiUploadFile($files)
    {
        $new_fomat = [];
        foreach ($files as $file) {
            $new_fomat[] = [
                'file' => $file,
                'index' => $this->index_file++,
            ];
        }

        // make chunk of 35 files
        $chucks = array_chunk($new_fomat, 20);

        foreach ($chucks as $files) {
            $promises = [];

            foreach ($files as $file) {
                $promises[] = $this->client->postAsync('https://www.tistory.com/apis/post/attach', [
                    'multipart' => [
                        [
                            'name' => 'uploadedfile',
                            'contents' => $this->bypass . file_get_contents($file['file']),
                            'filename' => uniqid() . '.png',
                        ],
                    ],
                    'query' => [
                        'access_token' => $this->access_token,
                        'blogName' => 'anikaka',
                        'output' => 'json',
                    ]
                ])->then(function ($response) use ($file) {
                    $content = json_decode($response->getBody()->getContents(), true);

                    $url = $content['tistory']['url'];
                    // $this->uploadeds[$file['index']] = $url;
                    preg_match("/dn\/(.*)\/img/", $url, $matches);
                    $key = $matches[1];

                    $this->uploadeds[$file['index']] = $url;
                })->otherwise(function ($reason) use ($file) {
                    // try to upload again
                    $this->client->postAsync('https://www.tistory.com/apis/post/attach', [
                        'multipart' => [
                            [
                                'name' => 'uploadedfile',
                                'contents' => $this->bypass . file_get_contents($file['file']),
                                'filename' => uniqid() . '.png',
                            ],
                        ],
                        'query' => [
                            'access_token' => $this->access_token,
                            'blogName' => 'anikaka',
                            'output' => 'json',
                        ]
                    ])->then(function ($response) use ($file) {
                        $content = json_decode($response->getBody()->getContents(), true);

                        $url = $content['tistory']['url'];

                        preg_match("/dn\/(.*)\/img/", $url, $matches);
                        $key = $matches[1];

                        // https://story-img.kakaocdn.net/dn/dvhCNW/btrVI7bkSqX/APdQTHrQMKHGyuMgN4SHQK/img_xl.jpg
                        $this->uploadeds[$file['index']] = "https://dn-img-page.kakao.com/download/resource?kid=" . $key;
                    })->otherwise(function ($reason) {
                        echo $reason->getMessage() . PHP_EOL;
                        die;
                    })->wait();
                });
            }

            \GuzzleHttp\Promise\Utils::unwrap($promises);
        }

        ksort($this->uploadeds);

        return $this->uploadeds;
    }
}
