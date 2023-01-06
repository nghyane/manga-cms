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

        
    }
}
