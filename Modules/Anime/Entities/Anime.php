<?php

namespace Modules\Anime\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use Alex433\LaravelEloquentCache\Cachable;
use Plank\Metable\Metable;

class Anime extends Model
{
    use HasFactory, Cachable, Metable;


    const STATUS_ONGOING = 1;
    const STATUS_COMPLETED = 2;

    protected $fillable = [
        'name',
        'slug',
        'description',
    ];

    protected static function newFactory()
    {
        return \Modules\Anime\Database\factories\AnimeFactory::new();
    }

    public function episodes()
    {
        return $this->hasMany(Episode::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'anime_tags', 'anime_id', 'tag_id');
    }

    public function genres()
    {
        return $this->belongsToMany(Genres::class, 'anime_genres', 'anime_id', 'genre_id');
    }

}
