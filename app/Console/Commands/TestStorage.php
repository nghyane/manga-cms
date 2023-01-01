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
    protected $signature = 'test:storage';

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
        $episode_path = storage_path('app/public/episodes/4');

        $files = glob($episode_path . '/ts/*.ts');
        $master_file = $episode_path . '/master.m3u8';

        echo "Files: " . count($files) . PHP_EOL;

        $storage = new \Modules\Storage\Services\NftStorage();
        $uploaded = $storage->multiUploadFile($files);

        $index_uploaded = 0;
        $m3u8_content = explode(PHP_EOL, file_get_contents($master_file));

        foreach ($m3u8_content as $key => $line) {
            if (strpos($line, '.ts') !== false) {
                $m3u8_content[$key] = $uploaded[$index_uploaded++];
            }
        }

        $m3u8_content = implode(PHP_EOL, $m3u8_content);

        file_put_contents($episode_path . '/playlist.m3u8', $m3u8_content);

        return Command::SUCCESS;
    }
}
