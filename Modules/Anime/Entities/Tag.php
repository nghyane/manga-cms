<?php

namespace Modules\Anime\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use Alex433\LaravelEloquentCache\Cachable;

class Tag extends Model
{
    use HasFactory, Cachable;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'meta_data',
    ];

    protected static function newFactory()
    {
        // return \Modules\Anime\Database\factories\TagsFactory::new();
    }

    public function animes()
    {
        return $this->belongsToMany(Anime::class, 'anime_tags', 'tag_id', 'anime_id');
    }
}
