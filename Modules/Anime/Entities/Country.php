<?php

namespace Modules\Anime\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Country extends Model
{
    use HasFactory;

    protected $table = 'countries';

    protected $fillable = [];

    protected static function newFactory()
    {
        // return \Modules\Anime\Database\factories\CountryFactory::new();
    }

    public function animes()
    {
        return $this->belongsToMany(Anime::class, 'anime_countries', 'country_id', 'anime_id');
    }
}
