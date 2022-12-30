<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class TestStorage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:blogger';

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


        $files = glob(storage_path('app/public/episodes/1/ts/*.ts'));

        $blogger = new \Modules\Storage\Services\Blogger();
      echo  $blogger->getDownloadUrl("https://get.google.com/albumarchive/111313901715989217946/album/AF1QipN5VANEH6LQYbKs9s5xevXeCatkxOOx51bG1DgF/AF1QipP8IR7F-7MNzd-ymRyrCwWTBVUlLnZveRHSF7hv?authKey=Gv1sRgCMSz0o6vvdzaPw");


        exit;
        $uploaded = $blogger->multiUploadFile($files);


        // get m3u8 file from list url
        $m3u8_content  = "#EXTM3U" . PHP_EOL;
        $m3u8_content .= "#EXT-X-VERSION:3" . PHP_EOL;
        $m3u8_content .= "#EXT-X-TARGETDURATION:10" . PHP_EOL;

        $i = 0;
        foreach($uploaded as $file) {
            $m3u8_content .= "#EXT-X-MEDIA-SEQUENCE:" . $i . PHP_EOL;
            $m3u8_content .= "#EXTINF:10," . PHP_EOL;
            $m3u8_content .= $file . PHP_EOL;
            $i++;
        }

        $m3u8_content .= "#EXT-X-ENDLIST" . PHP_EOL;

        file_put_contents(storage_path('app/public/episodes/1/playlist.m3u8'), $m3u8_content);

        return Command::SUCCESS;
    }
}
