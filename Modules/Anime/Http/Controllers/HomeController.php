<?php

namespace Modules\Anime\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

use Modules\Anime\Entities\Anime;
use Artesaos\SEOTools\Traits\SEOTools as SEOToolsTrait;

class HomeController extends Controller
{
    use SEOToolsTrait;
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        return view('anime::index');
    }

    public function home()
    {
        $this->seo()->setTitle('Watch HENTAI Online Free');

        // SEOtools Meta
        $jsonLdMulti = $this->seo()->jsonLdMulti();


        return view('anime::pages.home');
    }

    public function ongoing()
    {
        return view('anime::pages.ongoing');
    }

    public function completed()
    {
        return view('anime::pages.completed');
    }

    public function updated()
    {
        return view('anime::pages.updated');
    }
}
