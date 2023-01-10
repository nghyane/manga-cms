<?php

namespace Modules\Storage\Services;

class Discord
{
    public $client;

    public $chanel_id = '1054471584070512643';

    public $proxies = [];

    function __construct()
    {
        $this->client = new \GuzzleHttp\Client([
            "verify" => false,
            'headers' => [
                'Authorization' => 'Nzc2NTk3MDE2MTQyNTQ0OTM2.GiZKr6.MJsn69pNCoLtuK0l_emATvceBvGbISIFDVZV1k'
            ]
        ]);
    }

    public function getProxys($urls)
    {
        $index = 0;
        foreach ($urls as $url) {
            $this->proxies[] = [
                'index' => $index++,
                'url' => $url
            ];
        }

        $chunks = array_chunk($this->proxies, 1);

        foreach ($chunks as $chunk_urls) {
            $promises = [];
            foreach ($chunk_urls as $item) {
                $promises[] = $this->getPromises($item);
            }

            \GuzzleHttp\Promise\Utils::unwrap($promises);
        }

        $promises = [];
        foreach ($this->proxies as $proxy) {
            if (!isset($proxy['proxy'])) {
                $promises[] = $this->getPromises($proxy);
            }
        }

        if (count($promises) > 0) {
            \GuzzleHttp\Promise\Utils::unwrap($promises);
        }

        $proxies = [];
        foreach ($this->proxies as $proxy) {
            $proxies[$proxy['index']] = $proxy['proxy'];
        }

        ksort($proxies);

        return $proxies;
    }

    function getPromises($item)
    {
        return $this->client->postAsync('https://discordapp.com/api/channels/' . $this->chanel_id . '/messages', [
            'json' => [
                'content' => $item['url'],
            ]
        ])->then(function ($response) use ($item) {
            $data = json_decode($response->getBody()->getContents(), true);
            if (isset($data['embeds'][0]['thumbnail']['proxy_url'])) {
                $this->proxies[$item['index']]['proxy'] =  $data['embeds'][0]['thumbnail']['proxy_url'];
            }
        })->otherwise(function ($e) {
            echo $e->getMessage();
        });
    }
}
