<?php

namespace Modules\Storage\Services;

class Yarndex
{
    public $client;

    public function __construct()
    {
        $this->client = new \GuzzleHttp\Client([
            "verify" => false,
            "headers" => [
                "content-type" => "application/json",
                "cookie" => "",
            ],
        ]);
    }

    public function getProxys($urls)
    {
        $response = $this->client->post(
            "https://mail.yandex.ru/web-api/models/liza1?_m=do-sanitize",
            [
                "json" => $this->buildPost($urls),
            ]
        );

        $content = $response->getBody()->getContents();
        $content = json_decode($content, true);

        $images = $content["models"][0]["result"]["content"];

        // get all img src
        preg_match_all('/<img src="(.*?)"/', $images, $matches);
        
    }

    private function buildPost($urls)
    {
        // array url to html img
        $image_html = implode("", array_map(function ($url) {
            return '<img src="' . $url . '">';
        }, $urls));


        // get "ckey":"
        $response = $this->client->get("https://mail.yandex.ru");
        $content = $response->getBody()->getContents();

        preg_match('/"ckey":"(.*?)"/', $content, $matches);
        $ckey = $matches[1];

        // get "_uid":"
        preg_match('/"_uid":"(.*?)"/', $content, $matches);
        $uid = $matches[1];


        // "Config":{"connection_id":"
        preg_match('/"Config":{"connection_id":"(.*?)"/', $content, $matches);
        $connection_id = $matches[1];


        $data = [
            "models" => [
                [
                    "name" => "do-sanitize",
                    "params" => [
                        "content" => $image_html,
                    ],
                    "meta" => [
                        "requestAttempt" => 1,
                    ],
                ],
            ],
            "_ckey" => $ckey,
            "_uid" => $uid,
            "_locale" => "en",
            "_timestamp" => time(),
            "_product" => "RUS",
            "_connection_id" => $connection_id,
            "_exp" => "",
            "_eexp" => "",
            "_service" => "LIZA",
            "_version" => "95.1.0",
            "_messages_per_page" => "1000",
        ];
    }
}
