<?php

namespace Modules\Anime\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use Alex433\LaravelEloquentCache\Cachable;
use Plank\Metable\Metable;

class Anime extends Model
{
    use HasFactory, Metable, Cachable;


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

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function studio()
    {
        return $this->belongsToMany(Studio::class, 'anime_studios', 'anime_id', 'studio_id');
    }

    public function status()
    {
        switch ($this->status) {
            case self::STATUS_ONGOING:
                return __('ongoing');
                break;
            case self::STATUS_COMPLETED:
                return __('oompleted');
                break;
            default:
                return __('unknown');
                break;
        }
    }

    public function type()
    {
        return config('anime.type')[$this->type] ?? __('N/A');
    }

    public function cover()
    {
        return get_cover($this->slug);
    }

    public function url()
    {
        return get_anime_url($this);
    }
}
