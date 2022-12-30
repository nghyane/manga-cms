<?php

namespace Modules\Anime\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class UploadBlogger implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $episode_id;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($episode_id)
    {
        $this->episode_id = $episode_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $episode_path = storage_path('app/public/episodes/' . $this->episode_id);
        if(!is_dir($episode_path)) {
            return;
        }

       $files = glob($episode_path . '/ts/*.ts');

       $blogger = new \Modules\Storage\Services\Blogger();



    }
}
