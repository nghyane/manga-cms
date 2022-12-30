<?php

namespace App\Helpers;

class M3u8 {

    public $m3u8_content;
    public $playlist = [];
    public $playlist_max_tsfile = 20;


    public function __construct($episode_id)
    {

        $m3u8_file = storage_path('app/public/episodes/' . $episode_id . '/master.m3u8');


    }









}
