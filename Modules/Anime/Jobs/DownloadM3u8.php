<?php

namespace Modules\Anime\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class DownloadM3u8 implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $m3u8_url;
    protected $episode_id;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($m3u8_url, $episode_id)
    {
        $this->m3u8_url = $m3u8_url;
        $this->episode_id = $episode_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        echo 'Downloading episode ' . $this->episode_id . '...' . PHP_EOL;
        // storage m3u8 to public folder
        $m3u8_path = storage_path('app/public/episodes/' . $this->episode_id);
        if(!is_dir($m3u8_path)) {
            mkdir($m3u8_path, 0777, true);
        }

        // check resolution exist
        $m3u8_file = $m3u8_path . '/master.m3u8';

        $client = new \GuzzleHttp\Client(
            [
                'on_stats' => function (\GuzzleHttp\TransferStats $stats) {
                    echo $stats->getEffectiveUri() . PHP_EOL;
                },
            ]
        );
        $response = $client->request('GET', $this->m3u8_url);

        $m3u8_content = $response->getBody()->getContents();

        // download all ts file
        if(!is_dir($m3u8_path . '/ts')) {
            mkdir($m3u8_path . '/ts', 0777, true);
        }

        $promises = [];

        // get all url EXTINF from m3u8 file
        preg_match_all('/EXTINF:(.*),\s*(.*)/', $m3u8_content, $matches);
        $ts_files = $matches[2];

        foreach ($ts_files as $ts_file) {
            $promises[] = $client->getAsync($ts_file);
        }

        // get promise sorted by key
        $uploaded_files = [];

        $eachPromise = new \GuzzleHttp\Promise\EachPromise($promises, [
            'concurrency' => 10,
            'fulfilled' => function ($response, $index) use ($m3u8_path, &$uploaded_files) {
                $ts_file = $m3u8_path . '/ts/' . $index . '.ts';
                file_put_contents($ts_file, $response->getBody()->getContents());
                $uploaded_files[$index] = $ts_file;
            },
            'rejected' => function ($reason, $index) {
                throw new \Exception($reason);
            },
        ]);

        $eachPromise->promise()->wait();

        ksort($uploaded_files);
        // merge all ts file m3u8

        $m3u8_content = str_replace($ts_files, $uploaded_files, $m3u8_content);

        file_put_contents($m3u8_file, $m3u8_content);
    }
}
