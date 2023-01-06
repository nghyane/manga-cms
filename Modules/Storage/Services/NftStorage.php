<?php

namespace Modules\Storage\Services;

class NftStorage
{

    const API_URL = 'https://api.nft.storage';

    /**
     * @var \GluzzleHttp\Client $client
     */

    public $client;

    /**
     * @var array $promise \GuzzleHttp\Promise\PromiseInterface
     */
    public $promises = [];

    public $files = [];

    public $chunk_size = 35; // chuck of each upload prallel request

    public $bypass = "iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAIAAACQd1PeAAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAAJcEhZcwAADsMAAA7DAcdvqGQAAAAMSURBVBhXY/j//z8ABf4C/qc1gYQAAAAASUVORK5CYIIAlWQMYkxPYg==";

    public $transfer_time = 0;

    public $total_uploaded = 0;

    public $IPFS_FOMAT = "https://%s.ipfs.w3s.link/%s";

    public function __construct()
    {
        $API_KEY = config('storage.nftstorage.api_key');

        $start_time = time();
        $this->client = new \GuzzleHttp\Client([
            'headers' => [
                'Authorization' => "Bearer {$API_KEY}",
            ],
            'verify' => false,
            'base_uri' => NftStorage::API_URL,
            'on_stats' => function (\GuzzleHttp\TransferStats $stats) use ($start_time) {
                $this->transfer_time += $stats->getTransferTime();
                // show total in one line cli progress bar
                $time = time() - $start_time;
                echo "\rUpload time: " . $time . "s";
            }
        ]);

        $this->bypass = base64_decode($this->bypass);
        $this->bypass = "";
    }


    public function multiUploadFile($files)
    {
        $index = 0;
        foreach ($files as $file) {
            $this->files[] = [
                'file_path' => $file,
                'file_name' => sprintf('%s.png', uniqid()),
                'file_index' => $index++,
            ];
        }

        // chuck of each upload multipart request

        // slip files into chunks by file size max 90MB
        $chunks = [];
        $chunk_size = 0;
        $file_temp = [];


        foreach ($this->files as $file) {
            if (file_exists($file['file_path'])) {
                $file_temp[] = $file;
                $chunk_size += filesize($file['file_path']);
                if ($chunk_size > 90000000) { // 90MB
                    $chunks[] = $file_temp;
                    $file_temp = [];
                    $chunk_size = 0;
                }
            }
        }

        // $chunks = array_chunk($this->files, $this->chunk_size);

        foreach ($chunks as $chunk) {
            $multipart = $this->getMultipart($chunk);

            if (!$multipart) continue;

            $response = $this->client->request('POST', '/upload', [
                'multipart' =>
                $multipart,
            ]);

            $content = json_decode($response->getBody()->getContents(), true);

            if (isset($content['ok']) && $content['ok'] == true) {
                if (empty($content['value']['cid'])) {
                    echo ($response->getBody()->getContents());
                    throw new \Exception('Upload failed');
                }

                $cid = $content['value']['cid'];

                foreach ($chunk as $file) {
                    $this->files[$file['file_index']]['cid'] = $cid;
                }
            }
        }

        echo PHP_EOL;

        return $this->getReturnUrls();
    }

    function getMultipart($files)
    {
        $multipart = [];

        foreach ($files as $file) {

            if (file_exists($file['file_path'])) {
                $multipart[] = [
                    'name' => 'file',
                    'contents' => $this->bypass . file_get_contents($file['file_path']),
                    'filename' => $file['file_name'],
                ];
            }
        }

        return $multipart;
    }


    public function getReturnUrls()
    {
        $returnUrls = [];
        foreach ($this->files as $file) {
            if (isset($file['cid'])) {
                $returnUrls[] = sprintf($this->IPFS_FOMAT, $file['cid'], $file['file_name']);
            }
        }

        return $returnUrls;
    }
}
