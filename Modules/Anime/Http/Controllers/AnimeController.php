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
    public function anime()
    {
        return view('anime::pages.watch');
    }


}
