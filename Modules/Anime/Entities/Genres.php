<?php

namespace Modules\Anime\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use Alex433\LaravelEloquentCache\Cachable;

class Genres extends Model
{
    use HasFactory, Cachable;

    protected $fillable = [
        'name',
        'slug',
        'description',
    ];

    protected static function newFactory()
    {
        // return \Modules\Anime\Database\factories\GenresFactory::new();
    }

    public function anime()
    {
        return $this->belongsToMany(Anime::class, 'anime_genres', 'genre_id', 'anime_id');
    }
}
