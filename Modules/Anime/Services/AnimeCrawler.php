<?php

namespace Modules\Anime\Services;

use Symfony\Component\Console\Output\ConsoleOutput;

class AnimeCrawler
{
    public function writeln($message)
    {
        if (php_sapi_name() == 'cli') {

            $output = new ConsoleOutput();
            $output->writeln($message);
        } else {
            echo $message . "<br>";
        }
    }

    /**
     * Create anime tags if not exists
     *
     * @param array $tags anime tags
     * @return array
     */
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


    /**
     * Create anime genres if not exists
     *
     * @param array $genres anime genres
     * @return array
     */
    function genresInsert($anime, $genres)
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

        $attached = $anime->genres()->pluck('genre_id')->toArray();
        $ids = array_diff($ids, $attached);

        // attach if not exists
        $anime->genres()->attach($ids);

        return $ids;
    }

    /**
     * Create studios if not exists
     *
     * @param array $studio anime studio
     * @return array
     */
    function studiosInsert($anime, $studios)
    {
        $ids = [];

        foreach ($studios as $studio_name) {
            $studio_slug = \Illuminate\Support\Str::slug($studio_name);
            $studio = \Modules\Anime\Entities\Studio::firstOrNew([
                'slug' => $studio_slug,
            ]);

            $studio->name = $studio_name;
            $studio->save();

            $ids[] = $studio->id;
        }

        // attachUnique
        $attached = $anime->studio()->pluck('studio_id')->toArray();
        $ids = array_diff($ids, $attached);

        $anime->studio()->attach($ids);

        return $ids;
    }


    /**
     * Save anime cover image
     *
     * @param string $url cover image url
     * @param string $slug anime slug
     * @param array $options guzzle options
     * @return void
     */

    function saveCover($url, $slug, $options = [])
    {
        $cover_path = storage_path('app/public/covers');

        if (!file_exists($cover_path)) {
            mkdir($cover_path, 0777, true);
        }

        // use gluzzle to download image and save it auto referer and user agent
        $client = new \GuzzleHttp\Client([
            'verify' => false,
            'curl' => [
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_SSL_VERIFYPEER => false,
            ],
            'headers' => [
                'Referer' => $url,
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.132 Safari/537.36',
            ],
        ]);

        $response = $client->request('GET', $url, [
            'sink' => sprintf('%s/%s.jpg', $cover_path, $slug)
        ], $options);

        return $response;
    }

    /**
     * Check if cover image exist
     * @param string $slug anime slug
     * @return boolean
     */
    function coverExist($slug)
    {
        return file_exists(storage_path('app/public/covers/' . $slug . '.jpg'));
    }
}
