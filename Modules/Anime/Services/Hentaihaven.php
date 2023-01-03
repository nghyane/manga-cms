<?php

namespace Modules\Anime\Services;

use Modules\Anime\Entities\Anime;

class Hentaihaven extends AnimeCrawler
{

    protected $base_url;
    protected $client;

    public function __construct()
    {

        $this->base_url = 'https://wibulord.com/proxy.html?url=https://hentaihaven.xxx';

        // gluzze client no ssl verify
        $this->client = new \GuzzleHttp\Client([
            'headers' => [
                'Referer' => "https://hentaihaven.xxx",
                'User-Agent' => "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.132 Safari/537.36",
                'Origin' => "https://hentaihaven.xxx",
            ],

            'verify' => false,
            'curl' => [
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_SSL_VERIFYPEER => false,
            ],
        ]);
    }


    public function crawl()
    {
        $urls = $this->getUrls(1);
        $promises = [];

        foreach ($urls as $url) {
            $promises[] = $this->client->requestAsync('GET', $url)->then(function ($response) use ($url) {
                $body = $response->getBody()->getContents();
                $crawler = new \Symfony\Component\DomCrawler\Crawler($body);

                $title = $crawler->filter('.profile-manga .post-title h1')->text();
                $description = $crawler->filter('.summary__content.show-more')->text();
                // laravel slug generator
                $slug = \Illuminate\Support\Str::slug($title);

                $anime = Anime::firstOrNew([
                    'slug' => $slug,
                ]);

                $anime->name = $title;
                $anime->description = $description;

                $anime->save();

                if(empty($anime)){
                    return;
                }

                $metas = [];

                if($anime->wasRecentlyCreated){
                    $tag_formats = [
                        '%s Hentai Uncensored',
                        'Uncensored %s Hentai',
                        'Hentai Haven %s',
                        '%s Uncensored',
                    ];

                    $tags = array_map(function ($tag_format) use ($title) {
                        return sprintf($tag_format, $title);
                    }, $tag_formats);

                    $tags = $this->tagsInsert($tags);
                    $anime->tags()->attach($tags);
                }


                // meta Release
                $crawler->filter('.post-content_item')->each(function ($node) use (&$metas, $anime) {
                    $HEADING = $node->filter('.summary-heading')->text();


                    if(str_contains($HEADING, 'Release')){
                        $metas['release'] = $node->filter('.summary-content')->text();
                        return;
                    }

                    if(str_contains($HEADING, 'Alternative')){
                        $metas['alternative'] = $node->filter('.summary-content')->text();

                        $tags = explode(',', $metas['alternative']);
                        $tags = array_map(fn($tag) => trim($tag) . ' Uncensored', $tags);

                        $tags = $this->tagsInsert($tags);
                        $anime->tags()->attach($tags);
                        return;
                    }

                    if(str_contains($HEADING, 'Genre')){
                        $genres = $node->filter('.summary-content a')->each(function ($node) {
                            return trim($node->text());
                        });

                        $genres = $this->genresInsert($genres);
                        $anime->genres()->attach($genres);
                        return;
                    }
                });


                // insert meta anime
                $anime->setManyMeta($metas);

                // insert episode

                $crawler->filter('.listing-chapters_wrap .wp-manga-chapter ')->each(function ($node) use ($anime, &$wating_get_video) {
                    $a = $node->filter('a')->eq(0);
                    // regex get number or episode name
                    preg_match('/\d+/', $a->text(), $matches);

                    // if not found number
                    if(empty($matches)){
                        return;
                    }

                    $episode_name = $matches[0];

                    //check episode exist
                    if($anime->episodes()->where('name', $episode_name)->exists()){
                        return;
                    }

                    $episode_url = $this->base_url . parse_url($a->attr('href'), PHP_URL_PATH) . '/';

                    $episode = $anime->episodes()->create([
                        'name' => $a->text(),
                        'slug' => \Illuminate\Support\Str::slug($a->text()),
                    ]);

                    $episode->queue()->create([
                        'url' => $episode_url,
                        'source' => self::class,
                        'status' => 'waiting'
                    ]);
                });


                $this->writeln("Success: $anime->name");
            })->otherwise(function ($exception) {
                $this->writeln("Error: $exception");
                die;
            });
        }

        \GuzzleHttp\Promise\Utils::settle($promises)->wait();
    }

    public function getVideo($url)
    {

        $response = $this->client->request('GET', $url);
        $body = $response->getBody()->getContents();

        $iframe = preg_match('/<iframe.*?src="(.*?)".*?<\/iframe>/', $body, $matches);
        if(empty($iframe)){
            return;
        }

        // get data from data= from url param
        $iframe_url = $matches[1];
        $query = parse_url($iframe_url, PHP_URL_QUERY);

        parse_str($query, $query);

        $data = base64_decode($query['data']);
        $data = explode(':|::|:', $data);

        $en = $data[0];
        $iv = $data[1];
        $iv = base64_encode($iv);

        $response = $this->client->request('POST', "$this->base_url/wp-content/plugins/player-logic/api.php", [
            'form_params' => [
                'action' => 'zarat_get_data_player_ajax',
                'a' => $en,
                'b' => $iv,
            ],
            'headers' => [
                'content-type' => 'application/x-www-form-urlencoded',
            ],
        ]);


        $body = $response->getBody()->getContents();
        $body = json_decode($body);

        $master_url = $body->data->sources[0]->src;

        $response = $this->client->request('GET', $master_url);
        $body = $response->getBody()->getContents();

        // get all resolution and get the highest
        preg_match_all('/RESOLUTION=(\d+x\d+)/', $body, $matches);
        $resolutions = $matches[1];

        $resolution = collect($resolutions)->sortByDesc(function ($resolution) {
            $resolution = explode('x', $resolution);
            return $resolution[0];
        })->first();

        // get url video
        preg_match("/$resolution.*?\n(.*?)\n/", $body, $matches);
        $video_url = $matches[1];

        // get url path from master_url
        $path = parse_url($master_url, PHP_URL_PATH);
        $path = explode('/', $path);

        // remove last element
        array_pop($path);

        // join path
        $path = implode('/', $path);

        // get url host from master_url
        $host = parse_url($master_url, PHP_URL_HOST);

        // get url scheme from master_url
        $scheme = parse_url($master_url, PHP_URL_SCHEME);

        // get full url
        $video_url = "$scheme://$host$path/$video_url";

        return $video_url;
    }

    function getUrls($page){
        $url = $this->base_url . ($page <= 1 ? "/series/uncensored/" : "/series/uncensored/page/$page/");

        $response = $this->client->request('GET', $url);
        $body = $response->getBody()->getContents();

        $crawler = new \Symfony\Component\DomCrawler\Crawler($body);

        $urls = $crawler->filter('.page-listing-item .page-item-detail .item-summary .post-title.font-title a')->each(function ($node) {
            $path = $node->attr('href');
            $path = parse_url($path, PHP_URL_PATH);

            return $this->base_url . $path;
        });

        return $urls;
    }
}
