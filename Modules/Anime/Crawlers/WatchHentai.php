<?php

namespace Modules\Anime\Crawlers;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Pool;
use Modules\Anime\Entities\Anime;
use Modules\Anime\Entities\Episode;


use Modules\Anime\Services\AnimeCrawler;
use Symfony\Component\DomCrawler\Crawler;
use GuzzleHttp\Promise\Utils;
use Illuminate\Support\Facades\Storage;

class WatchHentai extends AnimeCrawler
{

    public $baseURL = 'https://watchhentai.net';
    public $maxPage = 40;

    public $pageFormat = 'https://watchhentai.net/series/page/%s';

    public function __construct($maxPage = 40)
    {
        $this->maxPage = $maxPage;

        // set default options for laravel http client
        Http::withOptions([
            'verify' => false,
            'timeout' => 10,
            'headers' => [
                'referer' => $this->baseURL,
                'user-agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.88 Safari/537.36',
            ]
        ]);
    }

    public function start()
    {
        // set default options
        for ($i = 1; $i <= $this->maxPage; $i++) {
            $this->writeln(sprintf('Crawling page %s', $i));

            Http::async()->get(sprintf($this->pageFormat, $i))->then(function ($response) use ($i) {
                // parse response get all urls add to queue
                // new dom crawler
                $body = $response->body();
                $crawler = new \Symfony\Component\DomCrawler\Crawler($body);

                // get all anime urls
                $crawler->filter('.module .content .items .item .data h3 a')->each(function ($node) {
                    // get url and add to queue
                    $this->info($node->attr('href'))->wait();
                });
            })->wait();
        }
    }

    public function info($url)
    {
        return Http::async()->get($url)->then(function ($response) use ($url) {
            $this->writeln(sprintf('Crawling %s', $url));
            $body = $response->body();

            $crawler = new \Symfony\Component\DomCrawler\Crawler($body);

            // get anime info
            $name = $crawler->filter('.sheader .data h1')->text();

            if (empty($name)) {
                $this->writeln(sprintf('Anime not found %s', $url));
                return;
            }

            $slug = \Illuminate\Support\Str::slug($name);
            $anime = Anime::firstOrNew([
                'slug' => $slug,
            ]);

            if ($crawler->filter('.sbox .wp-content p')->count() > 0) {
                $anime->description = $crawler->filter('.sbox .wp-content p')->eq(0)->text();
            }

            $anime->name = $name;
            $anime->save();

            if ($anime->wasRecentlyCreated) {
                $cover = $crawler->filter('.poster img')->attr('data-src');
                $this->saveCover($cover, $slug, Http::getOptions());
            }

            $metas = [
                'status' => Anime::STATUS_ONGOING,
                'type' => 'tv',
                'episodes' => 0,
                'country' => 'Japan',
            ];

            $crawler->filter("#single .content .sbox .custom_fields")->each(function (Crawler $node) use (&$metas, $anime) {
                $b = $node->filter('b')->text();
                if (str_contains($b, 'Alternative title')) {
                    $metas['alternative'] = $node->filter('.valor')->text();
                }

                if (str_contains($b, 'Average Duration')) {
                    $metas['duration'] = $node->filter('.valor')->text();
                }

                if (str_contains($b, 'First air date')) {
                    $metas['premiered'] = $node->filter('.valor')->text();
                    // format date with carbon
                    $metas['premiered'] = \Carbon\Carbon::parse($metas['premiered'])->format('Y-m-d');
                }

                if (str_contains($b, 'Last air date')) {
                    $metas['date_aired'] = $node->filter('.valor')->text();
                    $metas['date_aired'] = \Carbon\Carbon::parse($metas['date_aired'])->format('Y-m-d');

                    // check year
                    $year = \Carbon\Carbon::parse($metas['date_aired'])->year;
                    if ($year < 2021) {
                        $metas['status'] = Anime::STATUS_COMPLETED;
                    }
                }

                if (str_contains($b, 'Studio')) {
                    $studios = $node->filter('a')->each(function ($node) {
                        return $node->text();
                    });

                    if (!empty($studios)) {
                        $this->studiosInsert($anime, $studios);
                    }
                }
            });

            $genres = $crawler->filter('.sgeneros a')->each(function ($node) {
                if ($node->text() != '2022') {
                    return $node->text();
                }
            });

            $genres = array_filter($genres);
            $genres = array_unique($genres);

            if (!empty($genres)) {
                $this->genresInsert($anime, $genres);
            }

            $anime->setManyMeta($metas);

            // get episodes
            $crawler->filter('.se-a .episodios li')->each(function ($node) use ($anime) {

                if ($node->filter('img')->count() > 0) {
                    $thumnail = $node->filter('img')->attr('data-src');
                }

                $name = $node->filter('.episodiotitle a')->text();
                $name = trim($name);



                preg_match('/Episode\s(\d+)/', $name, $matches);
                if (!isset($matches[1])) {
                    return;
                }

                $name = $matches[1];
                $url = $node->filter('.episodiotitle a')->attr('href');


                // check episode exists
                $episode = Episode::firstOrNew([
                    'anime_id' => $anime->id,
                    'name' => $name,
                ]);

                if ($episode->exists) {
                    return;
                }

                $episode->anime_id = $anime->id;
                $episode->name = $name;
                $episode->slug = \Illuminate\Support\Str::slug($name);
                $episode->number = $name;
                $episode->subbed = true;

                if (isset($thumnail)) {
                    // save thumbnail sync sink
                    $thumnail_data = Http::get($thumnail, Http::getOptions())->body();
                    // Save to storage

                    $path = sprintf('public/thumbnail/%s/episodes/%s.jpg', $anime->slug, $episode->slug);
                    Storage::put($path, $thumnail_data);

                    $episode->thumbnail = sprintf('/storage/thumbnail/%s/episodes/%s.jpg', $anime->slug, $episode->slug);
                }


                $episode->save();

                // get episode video
                $this->episode($episode, $url)->wait();

                echo sprintf('New Episode %s', $episode->name) . PHP_EOL;
            });
        });
    }

    public function episode($episode, $url)
    {
        return Http::async()->get($url)->then(function ($response) use ($episode) {
            $body = $response->body();

            //  data-src='https://watchhentai.net/jwplayer/?source=https%3A%2F%2Fxupload.org%2Ffiles%2FW124H413%2FM%2Fmama-katsu%2Fmama-katsu-1.mp4&id=4613&type=mp4'

            preg_match('/jwplayer\/\?source=(.*?)&id=/', $body, $matches);
            $video = urldecode($matches[1]);

            $episode->video()->create([
                'episode_id' => $episode->id,
                'url' => $video,
                'type' => 'mp4',
                'subbed' => 1,
                'server' => 'Xupload'
            ]);
        });
    }
}
