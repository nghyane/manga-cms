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

    public $chunk_size = 50; // chuck of each upload prallel request

    public $bypass = "iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAIAAACQd1PeAAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAAJcEhZcwAADsMAAA7DAcdvqGQAAAAMSURBVBhXY/j//z8ABf4C/qc1gYQAAAAASUVORK5CYIIAlWQMYkxPYg==";

    public $transfer_time = 0;

    public $total_uploaded = 0;

    public $IPFS_FOMAT = "https://%s.ipfs.w3s.link/%s";

    public function __construct()
    {
        $API_KEY = config('storage.nftstorage.api_key');

        $this->client = new \GuzzleHttp\Client([
            'headers' => [
                'Authorization' => "Bearer {$API_KEY}",
            ],
            'verify' => false,
            'base_uri' => NftStorage::API_URL,
            'on_stats' => function (\GuzzleHttp\TransferStats $stats) {
                $this->transfer_time += $stats->getTransferTime();
                // show total in one line cli progress bar
                echo "Total uploaded: " . $this->total_uploaded .PHP_EOL;

                echo "Running time: " . $this->transfer_time . "s" . PHP_EOL;
            }
        ]);

        $this->bypass = base64_decode($this->bypass);
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
        $chunks = array_chunk($this->files, $this->chunk_size);



        foreach ($chunks as $chunk) {

            $response = $this->client->request('POST', '/upload', [
                'multipart' => $this->getMultipart($chunk),
            ]);

            $content = json_decode($response->getBody()->getContents(), true);

            if (isset($content['ok']) && $content['ok'] == true) {
                $cid = $content['value']['cid'];

                foreach ($chunk as $file) {
                    $this->files[$file['file_index']]['cid'] = $cid;
                }
            }
        }


        return $this->getReturnUrls();
    }

    function getMultipart($files)
    {
        $multipart = [];

        foreach ($files as $file) {
            $multipart[] = [
                'name' => 'file',
                'contents' => $this->bypass . file_get_contents($file['file_path']),
                'filename' => $file['file_name'],
            ];
        }

        return $multipart;
    }


    public function getReturnUrls(){
        $returnUrls = [];
        foreach ($this->files as $file){
            $returnUrls[] = sprintf($this->IPFS_FOMAT, $file['cid'], $file['file_name']);
        }

        return $returnUrls;
    }
}
