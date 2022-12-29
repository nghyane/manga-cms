<?php

namespace Modules\Anime\Console;

use Illuminate\Console\Command;
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
        // get episodes queue

        $episodes = \Modules\Anime\Entities\EpisodeQueue::where('status', 'waiting')->get();

        $episodes->each(function ($episode) {
            $episode->status = 'downloading';
            $episode->save();

            $source = new $episode->source;
            $m3u8_url = $source->getVideo($episode->url);

            // add job to download m3u8
            \Modules\Anime\Jobs\DownloadM3u8::dispatch($m3u8_url, $episode->episode_id);

            $this->info('Added job to download episode ' . $episode->episode_id);
        });
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
