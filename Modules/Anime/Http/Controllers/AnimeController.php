<?php

namespace Modules\Anime\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class AnimeController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function anime($id, $slug) // <--- this is the function that is called when you visit /{id}-f{slug}
    {
        $id = decode_id($id);
        $anime = \Modules\Anime\Entities\Anime::find($id)->first();

        print_r($anime->toArray());

        die;

        if ($anime->slug != $slug) {

            return redirect()->route('anime', ['id' => encode_id($anime->id), 'slug' => $anime->slug]);
        }


        // $anime->views = $anime->views + 1;
        // $anime->save();

        print_r($metas->toArray());

        $episodes = $anime->episodes()->orderBy('name', 'desc')->paginate(20);

        return view('anime::pages.watch', compact('anime', 'episodes'));
    }


}
