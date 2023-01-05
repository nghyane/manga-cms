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
}
