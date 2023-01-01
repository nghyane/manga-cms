<?php

namespace Modules\Anime\Console;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class HentaiHaven extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */

    protected $name = 'anime:start';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Crawl hentaihaven.';

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
        $this->info('Crawling begin...');

        $crawler = new \Modules\Anime\Services\Hentaihaven();
        $crawler->crawl();
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [

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

        ];
    }
}
