<?php

namespace Modules\Storage\Services;

class Yandex
{
    public $client;

    public function __construct()
    {
        $this->client = new \GuzzleHttp\Client([
            "verify" => false,
            "headers" => [
                "cookie" => "yuidss=7988020541636788093; yandexuid=7988020541636788093; _ym_uid=1664167154348419930; _ym_d=1664167155; is_gdpr=0; is_gdpr_b=CIyaHxCxlAEoAg==; my=YycCAAMA; L=QzBHclpxZANWWl5sXWJ+dH9cbmAIYGdsGD8lLQYEMFgVXBAFKxs6FQ==.1667977057.15156.338425.c31d59089b6bbe6142b1ea62f903de78; yandex_login=hoangvananhnghia; skid=2455671331668005686; gdpr=0; ymex=1703803048.yrts.1672267048; yp=1670865349.csc.1#1683954940.szm.3:1280x800:1280x649; i=2UmKyCpF0vdtjOZVwkcBuSIIee0TAFdWDAmYlKV2cvoWI/W2f28IYqetY//fBFgI1j8fxpautn7CC34eUnMezX9pkWI=; bltsr=1; KIykI=1; font_loaded=YSv1; _yasc=DiZosfQMGutAJWzVcVjd8QiFNkqxEzfyVPSjZbVxb3H6a4uNSEfdTCgeLpGi7cFg; Session_id=3:1673291141.5.0.1667976456178:vvProWF54fWb9CoDgJ0CJA:3e.1.2:1|1711147561.601.2.0:3.1:327036949.2:601|3:10263855.571921.vWIPJf0Mw0tyQbbuBmq5ncdRWPo; sessionid2=3:1673291141.5.0.1667976456178:vvProWF54fWb9CoDgJ0CJA:3e.1.2:1|1711147561.601.2.0:3.1:327036949.2:601|3:10263855.571921.fakesign0000000000000000000; sessguard=1.1673291141.1667976456178:vvProWF54fWb9CoDgJ0CJA:3e..3.500:31634.GVLGDc9O.09tdpZW4HqzH5sgGMEOQ29-_5LY; mda2_beacon=1673291141507; lah=2:1736363141.10019756.xyTTuYaoB0j4qqOE.LhL3tsQi-WQiKoARGm-GWCE-AX-mJFniqGBW.cubaklT8v9OmZsHsNjhB1A",
                'referer' => 'https://mail.yandex.ru/',
                'origin' => 'https://mail.yandex.ru',
                'user-agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/71.0.3578.98 Safari/537.36',
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

        $images = $content["models"][0]["data"]["result"];

        // remove amp
        $images = preg_replace('/&amp;/', '&', $images);

        // get all img src
        preg_match_all('/src="(.*?)"/', $images, $matches);

        return $matches[1] ?? $urls;
    }

    private function buildPost($urls)
    {
        // array url to html img
        $image_html = implode("", array_map(function ($url) {
            return '<img src="' . $url . '">';
        }, $urls));

        // get "ckey":"
        $ckey = "NC5MIpnqFVSHUeTSOsjucNmJLrc=!lcqpfxyq";

        $connection_id = "LIZA-09301638-1673297288817";


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
            "_uid" => '',
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

        return $data;
    }
}
