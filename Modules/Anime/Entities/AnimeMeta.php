<?php

namespace Modules\Anime\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AnimeMeta extends Model
{
    use HasFactory;

    protected $fillable = [
        'meta_key',
        'meta_value',
        'anime_id'
    ];

    protected static function newFactory()
    {
        // return \Modules\Anime\Database\factories\AnimeMetaFactory::new();
    }

    public function anime()
    {
        return $this->belongsTo(Anime::class);
    }
}
