<?php

namespace Modules\Anime\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Studio extends Model
{
    use HasFactory;

    protected $fillable = [];

    protected static function newFactory()
    {
        // return \Modules\Anime\Database\factories\StudioFactory::new();
    }

    public function animes()
    {
        return $this->belongsToMany(Anime::class, 'anime_studios', 'studio_id', 'anime_id');
    }
}
