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

    public function proxy($upload_id)
    {

        // alow origin
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
        header('Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token');

        // check url in redis
        $key = md5($upload_id);
        // cache use laravel cache
        $cacheDriver = \Illuminate\Support\Facades\Cache::store('redis');
        if ($cacheDriver->has($key)) {
            return redirect($cacheDriver->get($key));
        }

        $UPLOAD_URL = "https://docs.google.com/upload/photos/resumable?upload_id=$upload_id";

        $client = new \GuzzleHttp\Client([
            'verify' => false,
            'exceptions' => false,
        ]);

        $content = $client->get($UPLOAD_URL)->getBody()->getContents();
        // regex get photoMediaKey
        preg_match('/"photoMediaKey":"(.*?)"/', $content, $matches);
        $photoMediaKey = $matches[1];

        // regex get albumMediaKey
        preg_match('/"albumMediaKey":"(.*?)"/', $content, $matches);
        $albumMediaKey = $matches[1];

        // regex get username
        preg_match('/"username":"(.*?)"/', $content, $matches);
        $username = $matches[1];

        // photoPageUrl
        preg_match('/"photoPageUrl":"(.*?)"/', $content, $matches);
        $photoPageUrl = $matches[1];

        $authKey = explode('authkey', $photoPageUrl)[1];

        $AlbumURL = "https://get.google.com/albumarchive/$username/album/$albumMediaKey/$photoMediaKey?authKey$authKey";

        $blogger = new \Modules\Storage\Services\Blogger();
        $download_url = $blogger->getDownloadUrl($AlbumURL);

        // save to redis
        $cacheDriver->put($key, $download_url, 60 * 60); // 1 hour

        return redirect($download_url);
    }
}
