<?php

namespace Modules\Anime\Http\Controllers\Hls;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class BloggerController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index($episode_id)
    {
        $episode_path = storage_path('app/public/episodes/' . $episode_id);
        $playlist = $episode_path . '/playlist.m3u8';

        // check cache in redis laravel
        $key = md5($playlist);
        $redis = \Illuminate\Support\Facades\Redis::connection();

    }

    public function proxy($url){
        // check url in redis
        $key = md5($url);
        $redis = \Illuminate\Support\Facades\Redis::connection();
        if($redis->exists($key)){
            return redirect($redis->get($key));
        }

        $blogger = new \Modules\Storage\Services\Blogger();
        $download_url = $blogger->getDownloadUrl($url);

        // save to redis
        $redis->set($key, $download_url);
        $redis->expire($key, 60 * 60 * 2);

        return redirect($download_url);
    }


}
