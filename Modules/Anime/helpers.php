<?php

declare(strict_types=1);

if (!function_exists('decode_id')) {
    function decode_id($id)
    {
        $search = array('ba', 'ed', 'fg', 'hi', 'jk', 'lm', 'no', 'pq', 'rs', 'tu');
        $replace = array('1', '2', '3', '4', '5', '6', '7', '8', '9', '0');

        return str_replace($search, $replace, $id);
    }
}

if (!function_exists('encode_id')) {
    function encode_id($id)
    {
        if (empty($id)) return false;
        $search_strings = ['1', '2', '3', '4', '5', '6', '7', '8', '9', '0'];

        $replace_strings = ['ba', 'ed', 'fg', 'hi', 'jk', 'lm', 'no', 'pq', 'rs', 'tu'];

        return str_replace(
            $search_strings,
            $replace_strings,
            (string)$id
        );
    }
}

if (!function_exists('get_anime_url')) {
    function get_anime_url($anime)
    {
        return route('anime',  [
            'id' => encode_id($anime->id),
            'slug' => $anime->slug
        ]);
    }
}

if (!function_exists('get_cover')) {
    // check config env
    function get_cover($slug)
    {
        return asset("storage/covers/$slug.jpg");
    }
}
