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
        $studio = $anime->studio;
        $country = $anime->country;

        $episode = null;
        if ($episode_slug) {
            $episode = $anime->episodes()->where('slug', $episode_slug)->first();
            if ($episode) {
                $this->seo()
                    ->setTitle($anime->name . ' - ' . $episode->name)
                    ->setDescription($anime->description)
                    ->setCanonical(
                        $episode->url($anime)
                    );
            }
        }

        $episodes = $anime->episodes()->orderBy('number', 'asc')->get();

        return view('anime::pages.watch', compact('anime', 'episode', 'episodes', 'metas', 'tags', 'genres',  'studio', 'country'));
    }
}
