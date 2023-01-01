<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use \Symfony\Component\Process\Process;

class TestDownload extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:download';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test download';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        $ep = 1;
        $episode_path = storage_path('app/public/episodes/' . $ep);
        $download_tmp_path = storage_path('app/public/episodes/' . $ep . '/download_tmp');

        if (!is_dir($download_tmp_path)) {
            mkdir($download_tmp_path, 0777, true);
        }

        $source = new \Modules\Anime\Services\Hentaihaven();
        $m3u8_url = $source->getVideo('https://hentaihaven.xxx/watch/mama-katsu-midareru-mama-tachi-no-himitsu/episode-1/');
        // $m3u8_url = "http://storage.googleapiscdn.com/playlist/635024ff2b71af406651da6f/playlist.m3u8";

        // // parse m3u8 file get segments url or path
        $m3u8_content = file_get_contents($m3u8_url);
        preg_match_all('/EXTINF:.+?,\s*(.+?)\s*$/m', $m3u8_content, $matches);

        $segments = $matches[1];
        $segments = array_map(
            function ($url) {
                return "file $url";
            },
            $segments
        );


        $segments = implode(PHP_EOL, $segments);
        $iem_concat = tempnam(sys_get_temp_dir(), 'iem_concat') . '.txt';
        file_put_contents($iem_concat, $segments);
        chmod($iem_concat, 0777);

        // use ffmpeg concat segments to hls file render
        $command = sprintf('ffmpeg -protocol_whitelist file,http,https,tcp,tls,crypto -f concat -safe 0 -threads 10 -i %s -c copy %s/master.m3u8', $iem_concat, $episode_path);

        Process::fromShellCommandline($command)->setTimeout(0)->run(
            function ($type, $buffer) {
                echo $buffer;
            }
        );


        return Command::SUCCESS;
    }

    public function singleDownload($client, $segments)
    {
    }
}
