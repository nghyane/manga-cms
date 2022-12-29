<?php

namespace Modules\Anime\Services;

use Modules\Anime\Entities\Anime;
use Modules\Anime\Entities\Tag;
// cli output
use Symfony\Component\Console\Output\ConsoleOutput;

class AnimeCrawler
{
    public function crawl()
    {
    }

    public function writeln($message)
    {
        if (php_sapi_name() == 'cli') {

            $output = new ConsoleOutput();
            $output->writeln($message);
        } else {
            echo $message . "<br>";
        }
    }

    function tagsInsert($tags)
    {
        $ids = [];
        foreach ($tags as $tag_name) {
            $tag_slug = \Illuminate\Support\Str::slug($tag_name);
            $tag = \Modules\Anime\Entities\Tag::firstOrNew([
                'slug' => $tag_slug,
            ]);

            $tag->name = $tag_name;
            $tag->save();

            $ids[] = $tag->id;
        }

        return $ids;
    }

    function genresInsert($genres)
    {
        $ids = [];

        foreach ($genres as $genre_name) {
            $genre_slug = \Illuminate\Support\Str::slug($genre_name);
            $genre = \Modules\Anime\Entities\Genres::firstOrNew([
                'slug' => $genre_slug,
            ]);

            $genre->name = $genre_name;
            $genre->save();

            $ids[] = $genre->id;
        }

        return $ids;
    }
}
