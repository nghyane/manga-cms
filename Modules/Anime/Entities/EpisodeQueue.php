<?php

namespace Modules\Anime\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EpisodeQueue extends Model
{
    use HasFactory;

    protected $table = 'episodes_queue';

    protected $fillable = [
        'episode_id',
        'status',
        'url',
        'source',
    ];

    protected static function newFactory()
    {
        // return \Modules\Anime\Database\factories\EpisodeQueueFactory::new();
    }


    public function episode()
    {
        return $this->belongsTo(Episode::class, 'episode_id', 'id');
    }
}
