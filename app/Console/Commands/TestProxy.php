<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class TestProxy extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:proxy';

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
        $discord = new \Modules\Storage\Services\Discord();
        $discord->getProxys([
            'https://scans-ongoing-2.planeptune.us/manga/My-Home-Hero/0120-001.png',
            'https://scans-ongoing-2.planeptune.us/manga/My-Home-Hero/0120-002.png',
            'https://scans-ongoing-2.planeptune.us/manga/My-Home-Hero/0120-003.png',
            'https://scans-ongoing-2.planeptune.us/manga/My-Home-Hero/0120-004.png',
            'https://scans-ongoing-2.planeptune.us/manga/My-Home-Hero/0120-005.png',
            'https://scans-ongoing-2.planeptune.us/manga/My-Home-Hero/0120-006.png',
        ]);


        return Command::SUCCESS;
    }
}
