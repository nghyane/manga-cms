<?php

namespace Modules\Anime\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use Alex433\LaravelEloquentCache\Cachable;

class Episode extends Model
{
    use HasFactory, Cachable;


    protected $fillable = [
        'name',
        'slug',
        'description',
        'anime_id',
    ];

    protected static function newFactory()
    {
        // return \Modules\Anime\Database\factories\EpisodeFactory::new();
    }

    public function anime()
    {
        return $this->belongsTo(Anime::class);
    }

    public function video()
    {
        return $this->hasOne(Video::class);
    }

    public function queue()
    {
        return $this->hasOne(EpisodeQueue::class);
    }

}
