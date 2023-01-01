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
        ini_set('memory_limit', '6G');//1 GIGABYTE

        echo 'Downloading episode ' . $this->episode_id . '...' . PHP_EOL;
        // storage m3u8 to public folder
        $m3u8_path = storage_path('app/public/episodes/' . $this->episode_id);
        if(!is_dir($m3u8_path)) {
            mkdir($m3u8_path . "/ts", 0777, true);

        }

        // check resolution exist
        $m3u8_file = $m3u8_path . '/master.m3u8';

        // get all EXTINF url from m3u8
        preg_match_all('/EXTINF:(.*),\s*(.*)/', file_get_contents($this->m3u8_url), $matches);

        $URLS = $matches[2];

        // add file 'url' to array $URLS use array_map
        $URLS = array_map(function ($url) {
            return "file $url" . PHP_EOL;
        }, $URLS);

        $iem_concat = tempnam(sys_get_temp_dir(), 'iem_concat') . '.txt';
        file_put_contents($iem_concat, $URLS);
        chmod($iem_concat, 0777);

        $cmd = sprintf('ffmpeg -protocol_whitelist file,http,https,tcp,tls,crypto -f concat -safe 0 -threads 50 -i %s -c copy -hls_list_size 0 -hls_segment_filename %s %s', $iem_concat, $m3u8_path . "/ts/%03d.ts", $m3u8_file);

        echo 'Running command: ' . $cmd . PHP_EOL;

        \Symfony\Component\Process\Process::fromShellCommandline($cmd)->setTimeout(0)->run(
            function ($type, $buffer) {
                echo $buffer;
            }
        );

        // check file exist
        if(!file_exists($m3u8_file)) {
            echo 'Download failed' . PHP_EOL;
            return;
        }

        $m3u8_file_content = file_get_contents($m3u8_file);
        $m3u8_file_content = explode(PHP_EOL, $m3u8_file_content);

        $upload_list = [];

        foreach ($m3u8_file_content as $key => $value) {
            if (strpos($value, '.ts') !== false) {
                $upload_list[] = $m3u8_path . '/' . $value;
            }
        }

        $blogger = new \Modules\Storage\Services\Blogger();

        $news = $blogger->multiUploadFile($upload_list);
        $news_index = 0;

        foreach ($m3u8_file_content as $key => $value) {
            if (strpos($value, '.ts') !== false) {
                $m3u8_file_content[$key] = $news[$news_index];
            }
        }

        // delete file
        foreach ($upload_list as $key => $value) {
            unlink($value);
        }
    }
}
