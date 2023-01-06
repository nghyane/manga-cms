<?php

namespace Modules\Anime\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Video extends Model
{
    use HasFactory;

    protected $table = 'video';

    protected $fillable = [
        'episode_id',
        'type',
        'url',
        'subtitles',
        'language',
        'dubbed',
        'subbed',
        'server'
    ];

    protected static function newFactory()
    {
        // return \Modules\Anime\Database\factories\VideoFactory::new();
    }

    public function episode()
    {
        return $this->belongsTo(Episode::class);
    }
}
