<?php

namespace Modules\Anime\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class ApiController extends Controller
{

    public function episodes($anime_id)
    {
        $anime = \Modules\Anime\Entities\Anime::where('id', $anime_id)->first();

        if (!$anime) {
            return response()->json([
                'status' => 'error',
                'message' => 'Anime not found'
            ]);
        }

        $episodes = \Modules\Anime\Entities\Episode::where('anime_id', $anime->id)->orderBy('number', 'asc')->get();

        // get the episodes html rendered
        $episodes = view('anime::partials.episodes', compact('episodes', 'anime'))->render();

        return response()->json([
            'status' => 'success',
            'data' => $episodes
        ]);
    }

    public function home($type)
    {
        $page = request()->get('page', 1);
        $limit = config('anime.home_page_num', 20);

        $offset = ($page - 1) * $limit;

        $anime_module = \Modules\Anime\Entities\Anime::with('meta')->offset($offset)->limit($limit);

        switch ($type) {
            case 'updated':
            case 'updated-all':
                $animes = $anime_module->orderBy('updated_at', 'desc');
                break;
            case 'updated-sub':
                $animes = $anime_module->whereMeta('subbed', 1)->orderBy('updated_at', 'desc');
                break;
            case 'updated-dub':
                $animes = $anime_module->whereMeta('dubbed', 1)->orderBy('updated_at', 'desc');
                break;
            case 'popular':
            case 'trending':
                $animes = $anime_module->orderBy('views', 'desc');
                break;
            case 'newest':
                $animes = $anime_module->orderBy('created_at', 'desc');
                break;
            default:
                // random
                $animes = $anime_module->inRandomOrder();
                break;
        }

        $animes = $anime_module->get();

        // composent itemlist
        $animes = view('anime::components.itemlist', compact('animes'))->render();

        return response()->json([
            'status' => 'success',
            'data' => $animes
        ]);
    }
}
