<?php

namespace Modules\Anime\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

use Artesaos\SEOTools\Traits\SEOTools as SEOToolsTrait;

class AnimeController extends Controller
{
    use SEOToolsTrait;
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function anime($id, $slug, $episode_slug = null) // <--- this is the function that is called when you visit /{id}-f{slug}
    {
        $id = decode_id($id);
        $anime = \Modules\Anime\Entities\Anime::where('id', $id)->with('tags', 'genres', 'studio', 'country')->first();

        if (!$anime) {
            abort(404);
        }


        if ($anime->slug != $slug) {
            // if debug mode is on, we will not redirect
            if (env('APP_DEBUG', false) == false) {
                return redirect($anime->url(), 301);
            }

            throw new \Exception('Slug is not correct');
        }

        // SEO and meta tags
        $this->seo()
            ->setTitle($anime->name)
            ->setDescription($anime->description)
            ->setCanonical(
                $anime->url()
            );


        $metas = $anime->getAllMeta();

        $tags = $anime->tags;
        $genres = $anime->genres;
        $studios = $anime->studio;
        $country = $anime->country;

        $episode = null;
        if ($episode_slug) {
            $episode = $anime->episodes()->where('slug', $episode_slug)->with('video')->first();
            if ($episode) {
                $title_format = config('anime.watch_title_format', '{anime_name} Episode {episode_name}');

                // replace title format regex
                $title = preg_replace('/\{anime_name\}/', $anime->name, $title_format);
                $title = preg_replace('/\{episode_name\}/', $episode->name, $title);

                // fomat description to seo friendly, no html tags, string length 160
                $description =  preg_replace('/\s+/', ' ', $anime->description);
                $description =  strip_tags($description);
                $description =  mb_substr($description, 0, 160);

                if (mb_strlen($description) == 160) {
                    $description .= '...';
                }

                $this->seo()
                    ->setTitle($title)
                    ->setDescription($anime->description)
                    ->setCanonical(
                        $episode->url($anime)
                    );

                // open graph
                $this->seo()->opengraph()->addVideo([
                    'url' => $episode->url($anime),
                    'secure_url' => $episode->url($anime),
                    'type' => 'video/mp4',
                    'width' => 1280,
                    'height' => 720,
                    'title' => $title,
                    'description' => $anime->description,
                    'image' => $episode->poster_url,
                ]);


                // twitter cards
                $this->seo()->twitter()->setUrl($episode->url($anime));
                $this->seo()->twitter()->setTitle($title);
                $this->seo()->twitter()->setDescription($anime->description);
            }
        } else {
            $episode = $anime->episodes()->orderBy('number', 'asc')->with('video')->first();
        }

        $episodes = $anime->episodes()->orderBy('number', 'asc')->get();

        return view('anime::pages.watch', compact('anime', 'episode', 'episodes', 'metas', 'tags', 'genres',  'studios', 'country'));
    }
}
