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
        $anime = \Modules\Anime\Entities\Anime::where('id', $id)->with('episodes', 'tags', 'genres', 'studio', 'country')->first();

        if ($anime->slug != $slug) {
            // if debug mode is on, we will not redirect
            if (env('APP_DEBUG', false) == false) {
                return redirect($anime->url(), 301);
            }

            throw new \Exception('Slug is not correct');
        }

        $this->seo()
            ->setTitle($anime->name)
            ->setDescription($anime->description)
            ->setCanonical(
                $anime->url()
            );


        $metas = $anime->getAllMeta();

        $tags = $anime->tags;
        $genres = $anime->genres;
        $studio = $anime->studio;
        $country = $anime->country;

        $episodes = $anime->episodes;
        $episode = null;
        if ($episode_slug) {
            $episode = $episodes->where('slug', $episode_slug)->where('anime_id', $anime->id)->first();
            if ($episode) {
                $this->seo()
                    ->setTitle($anime->name . ' - ' . $episode->name)
                    ->setDescription($anime->description)
                    ->setCanonical(
                        $episode->url()
                    );
            }
        }

        return view('anime::pages.watch', compact('anime', 'episodes', 'episode', 'metas', 'tags', 'genres',  'studio', 'country'));
    }
}
