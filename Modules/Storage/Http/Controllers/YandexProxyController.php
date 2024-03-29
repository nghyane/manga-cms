<?php

namespace Modules\Storage\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Cache;

class YandexProxyController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index($path)
    {
        $key_master = md5($path);

        if (Cache::has($key_master)) {
            return response(Cache::get($key_master), 200, ['Content-Type' => 'application/vnd.apple.mpegurl',]);
        }

        $file_path = storage_path($path);

        // create worker genproxy
        $content = file_get_contents($file_path);
        preg_match_all('/#EXTINF:(.*)\n(.*)/', $content, $matches);

        $urls = $matches[2];

        $yandex = new \Modules\Storage\Services\Yandex();
        $new_urls = $yandex->getProxys($urls);

        $content = str_replace($urls, $new_urls, $content);

        Cache::put($key_master, $content, 60); // 1 hour
        return response($content, 200, [
            'Content-Type' => 'application/vnd.apple.mpegurl',
        ]);
    }

    public function getProxy($urls)
    {
        $url = "https://mail.yandex.ru/web-api/models/liza1?_m=do-sanitize";

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $headers = array(
            "Cookie: yuidss=7988020541636788093; yandexuid=7988020541636788093; _ym_uid=1664167154348419930; _ym_d=1664167155; is_gdpr=0; is_gdpr_b=CIyaHxCxlAEoAg==; my=YycCAAMA; L=QzBHclpxZANWWl5sXWJ+dH9cbmAIYGdsGD8lLQYEMFgVXBAFKxs6FQ==.1667977057.15156.338425.c31d59089b6bbe6142b1ea62f903de78; yandex_login=hoangvananhnghia; skid=2455671331668005686; gdpr=0; ymex=1703803048.yrts.1672267048; yp=1670865349.csc.1#1683954940.szm.3:1280x800:1280x649; i=2UmKyCpF0vdtjOZVwkcBuSIIee0TAFdWDAmYlKV2cvoWI/W2f28IYqetY//fBFgI1j8fxpautn7CC34eUnMezX9pkWI=; bltsr=1; KIykI=1; font_loaded=YSv1; _yasc=DiZosfQMGutAJWzVcVjd8QiFNkqxEzfyVPSjZbVxb3H6a4uNSEfdTCgeLpGi7cFg; Session_id=3:1673291141.5.0.1667976456178:vvProWF54fWb9CoDgJ0CJA:3e.1.2:1|1711147561.601.2.0:3.1:327036949.2:601|3:10263855.571921.vWIPJf0Mw0tyQbbuBmq5ncdRWPo; sessionid2=3:1673291141.5.0.1667976456178:vvProWF54fWb9CoDgJ0CJA:3e.1.2:1|1711147561.601.2.0:3.1:327036949.2:601|3:10263855.571921.fakesign0000000000000000000; sessguard=1.1673291141.1667976456178:vvProWF54fWb9CoDgJ0CJA:3e..3.500:31634.GVLGDc9O.09tdpZW4HqzH5sgGMEOQ29-_5LY; mda2_beacon=1673291141507; lah=2:1736363141.10019756.xyTTuYaoB0j4qqOE.LhL3tsQi-WQiKoARGm-GWCE-AX-mJFniqGBW.cubaklT8v9OmZsHsNjhB1A",
            "Content-Type: application/json",
        );
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

        $content = "";

        foreach ($urls as $url) {
            $content .= '<img style=\"max-width: 100%\" src=\"' . $url . '\">';
        }

        $data = '{"models":[{"name":"do-sanitize","params":{"content":"' . $content . '"},"meta":{"requestAttempt":1}}],"_ckey":"LX+ug5wuVaNCdLeY5E7d+bHdJdY=!lcqls6y6","_uid":"1711147561","_locale":"en","_timestamp":1673291408496,"_product":"RUS","_connection_id":"LIZA-79406497-1673291141877","_exp":"500420,0,-1;613430,0,-1;625365,0,-1;674943,0,-1;605931,0,-1;605934,0,-1;605938,0,-1;605942,0,-1;610121,0,-1;621025,0,-1;621027,0,-1;641461,0,-1;675045,0,-1;675046,0,-1;675052,0,-1;684490,0,-1;636881,0,36;675148,0,73;675546,0,92;693521,0,58;380963,0,35;261661,0,26;134385,0,0;188837,0,29;694736,0,51;692086,0,69;674421,0,47;645926,0,92","_eexp":"500420,0,-1;613430,0,-1;625365,0,-1;674943,0,-1;605931,0,-1;605934,0,-1;605938,0,-1;610121,0,-1;621027,0,-1;641461,0,-1;675045,0,-1;684490,0,-1;636881,0,36;675148,0,73;675546,0,92;380963,0,35;261661,0,26;134385,0,0;188837,0,29;694736,0,51","_service":"LIZA","_version":"95.1.0","_messages_per_page":"30"}';

        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

        //for debug only!
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        $resp = curl_exec($curl);
        curl_close($curl);

        $resp = json_decode($resp);

        $html = $resp->models[0]
            ->data->result;

        $html = str_replace('amp;', '', $html);


        // get urls img
        preg_match_all("/src=\"(.*?)\"/", $html, $matches);
        $urls = $matches[1];

        return $urls;
    }
}
