<?php

namespace Modules\Anime\Console;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class AnimeUploader extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'anime:uploader';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // get video mp4 and url domain is xupload.org
        $videos = \Modules\Anime\Entities\Video::where('type', 'mp4')->where('url', 'like', '%xupload.org%')->get();
        foreach ($videos as $video) {
            $this->info("Start download video: " . $video->url);

            // download file to storage/app/tmp/
            $tmp_name = uniqid() . '.mp4';
            $tmp_path = storage_path('app/tmp/' . $tmp_name);

            if (!is_dir(storage_path('app/tmp'))) {
                mkdir(storage_path('app/tmp'), 0777, true);
            }

            $client = new \GuzzleHttp\Client();
            $client->request('GET', $video->url, [
                'sink' => $tmp_path,
                'progress' => function ($downloadTotal, $downloadedBytes) {
                    echo "\rDownloaded: " . round($downloadedBytes / 1024 / 1024, 2) . "MB / " . round($downloadTotal / 1024 / 1024, 2) . "MB";
                }
            ]);

            // FFMPEG convert mp4 to m3u8
            $stream_path = "streaming/" . uniqid();
            $episode_path = storage_path('app/public/' . $stream_path);

            if (!file_exists($episode_path)) {
                mkdir($episode_path, 0777, true);
            }

            $command = "ffmpeg -i $tmp_path -c copy -g 3 -y -keyint_min 3 -hls_list_size 0 -hls_time 10 -hls_segment_filename $episode_path/%03d.ts $episode_path/master.m3u8";
            $process = \Symfony\Component\Process\Process::fromShellCommandline($command);
            $process->setTimeout(0);

            echo PHP_EOL;

            // run with progress bar  no output
            $start = time();
            $process->run(
                function ($type, $buffer) use ($start) {
                    echo "\rRender Time: " . (time() - $start) . "s";
                }
            );

            $this->info("\nStart upload...");
            unlink($tmp_path);

            // parse m3u8 file get segments url or path
            $m3u8_content = file_get_contents($episode_path . '/master.m3u8');
            preg_match_all('/EXTINF:.+?,\s*(.+?)\s*$/m', $m3u8_content, $matches);

            $segments = $matches[1];
            foreach ($segments as $segment) {
                $files[] = $episode_path . '/' . $segment;
            }

            try {
                $storage = new \Modules\Storage\Services\NftStorage();
                $uploaded = $storage->multiUploadFile($files);
            } catch (\Exception $e) {
                $this->error($e->getMessage());
                // delete path use laravel storage
                \Illuminate\Support\Facades\Storage::deleteDirectory($stream_path);
                continue;
            }


            $m3u8_content = str_replace($segments, $uploaded, $m3u8_content);
            \Illuminate\Support\Facades\File::put($episode_path . '/master.m3u8', $m3u8_content);

            $video->url = sprintf("/storage/$stream_path/master.m3u8");

            $video->type = 'hls';
            $video->server = 'IPFS Storage';

            $video->save();

            foreach ($segments as $segment) {
                unlink($episode_path . '/' . $segment);
            }
        }
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            // ['example', InputArgument::REQUIRED, 'An example argument.'],
        ];
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['example', null, InputOption::VALUE_OPTIONAL, 'An example option.', null],
        ];
    }
}
