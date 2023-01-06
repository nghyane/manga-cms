<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class TestDownloadMp4 extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:download-mp4';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        
        $ep = 6;

        $episode_path = storage_path('app/public/episodes/' . $ep);
        $download_tmp_path = storage_path('app/public/episodes/' . $ep . '/download_tmp');

        if (!file_exists($download_tmp_path)) {
            mkdir ($download_tmp_path, 0777, true);
        }

        // fast mp4 download gluzzle
        $mp4_url = 'https://xupload.org/files/W124H413/K/kemonokko-tsuushin-the-animation-ushi-musume-bell/kemonokko-tsuushin-the-animation-ushi-musume-bell-1.mp4';

        $mp4_file = $download_tmp_path . '/video.mp4';

        $client = new \GuzzleHttp\Client();
        $client->request('GET', $mp4_url, [
            'sink' => $mp4_file,
            'progress' => function ($downloadTotal, $downloadedBytes) {
                echo "Downloaded: " . round($downloadedBytes / 1024 / 1024, 2) . "MB / " . round($downloadTotal / 1024 / 1024, 2) . "MB" . PHP_EOL;
            }
        ]);


        $command = "ffmpeg -i $mp4_file -c copy -g 3 -keyint_min 3 -hls_list_size 0 -hls_time 10 -hls_segment_filename $episode_path/%03d.ts $episode_path/master.m3u8";
        $process = \Symfony\Component\Process\Process::fromShellCommandline($command);
        $process->setTimeout(0);
        $process->run(
            function ($type, $buffer) {
                if (\Symfony\Component\Process\Process::ERR === $type) {
                    echo 'ERR > ' . $buffer;
                } else {
                    echo 'OUT > ' . $buffer;
                }
            }
        );

        // parse m3u8 file get segments url or path
        $m3u8_content = file_get_contents($episode_path . '/master.m3u8');
        preg_match_all('/EXTINF:.+?,\s*(.+?)\s*$/m', $m3u8_content, $matches);

        $segments = $matches[1];

        // fomat segments
        foreach ($segments as $segment) {
            $files[] = $episode_path . '/' . $segment;
        }

        $storage = new \Modules\Storage\Services\NftStorage();
        $uploaded = $storage->multiUploadFile($files);

        // update segments
        $m3u8_content = str_replace($segments, $uploaded, $m3u8_content);
        file_put_contents($episode_path . '/playlist.m3u8', $m3u8_content);

        return Command::SUCCESS;
    }
}
