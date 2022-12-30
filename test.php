<?php

$url = "https://clients6.google.com/batch/drive/v2internal?%24ct=multipart%2Fmixed%3B%20boundary%3D%22%3D%3D%3D%3D%3D4t9r8ktj5utg%3D%3D%3D%3D%3D%22&key=AIzaSyAw-cTyp9Xotzvu3vNDWhDU3E9NConkKxQ";

$fields = "thumbnailLink,imageMediaMetadata";

$curl = curl_init($url);
curl_setopt($curl, CURLOPT_URL, $url);
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

$headers = array(
   "Content-Type: text/plain",
   "cookie: ANID=AHWqTUnpl6cJdsn_vw7b6rordjkD1rV1XXys6BVtuI9bLElYpcyVtOaJMoliCm4a; HSID=AJ5tLDBF4ga_2pL2x; SSID=AE_yijw7HAb1XGh6U; APISID=bt-rks33ozeZKuaY/ADYOUo3hqL0EwzcfI; SAPISID=ZEs-WUis8DI2XaU7/AegeprkbO6yl9XTj_; __Secure-1PAPISID=ZEs-WUis8DI2XaU7/AegeprkbO6yl9XTj_; __Secure-3PAPISID=ZEs-WUis8DI2XaU7/AegeprkbO6yl9XTj_; SID=RwgVXKi10E1s-6Q-95ZJ0fXM29xE0cmLsWY_MxoLztgVZOqMAam3uBZ_E3tpJUdWGyI34g.; __Secure-1PSID=RwgVXKi10E1s-6Q-95ZJ0fXM29xE0cmLsWY_MxoLztgVZOqMCZht_brNvDWKDMzrHFhhJw.; __Secure-3PSID=RwgVXKi10E1s-6Q-95ZJ0fXM29xE0cmLsWY_MxoLztgVZOqMgLQ3cmBrbcBpMx27TAyzqQ.; SEARCH_SAMESITE=CgQImpcB; __Secure-ENID=9.SE=bfxjf7SjMtnEXUqXJcqUYPNWiAIt9z15aXFPkzvR-CtE3VMhfoPSKeY_a42VtA8_HJIe6-Arht6Zz7RaEsfUksb6yCq_CwMMFGzxCLL5f3OngbdAan0vCDAAy7e_YxqwAHZ_eNCg386xL7yUlxtN49miyvLT22aFN7azkzGMvms7RvLgWL-UipkWw5EgvdbGlWZ7HkYVh0Ns9nyS50Ywla4GaYcBcpTEZwC09c-u3HaFRQsVI46E; AEC=AakniGPDqB17kBXl2f8HSalUG6lo7lRFmdZhYsxZJ3IPGRx3fRlpw0jE960; 1P_JAR=2022-12-29-17; NID=511=t44dDshsxjTNudkXtlcd94O4NESh5bGUOad-dhlWcYKu3JqRa1G4uQlgk48YP-WizejuIh03yhBgmx51UTf6l87VuLvfH2KoZbYeR-zJyeOg0tj09uIWMkvjZlVmB3-e-DHemgi0uTnt84YU5S7Gq5vb5TBUd3Cdoq6CaF89mQb5HC_lFh7Q4pRYefIQ9bx92b-9Dh8zbQF5-rypZ5nG84FzcQsCKBlcZrzQpkXrZG9y96HnZ9vze1mjF88uDqBUAH3ck5Y_ihY3XIHFn9WXr8y1oI-Wcf977w7NKSDTPpmdhAyUCVFoRMcXmO79XYtd-nbuL9Svu4G3UPzpKTFCjfeUeE9oYxJ6_F6oLx8ZHU4BB9LSpLv18MrDiWpojrdid9scaa-xGw5yNTnOGnoZFY3-JK7bh4jklWIQPIw7BSw7OIM7AoVttymQzLA8FBeXPPzykMfqJIg8wldC3svsLJEBVLCbiA5wcMuurjzRvPPe5BMF2V0W; SIDCC=AIKkIs1RCQrG57yD1j_3KqEKtiVcUwKc1fJLb6clKheQhOmwp4lSlbehVUsrJmACGTcdI1hBjWQ; __Secure-1PSIDCC=AIKkIs2YTMCtaWoWisq3jYVQ8hnOCLFNyZXJq1ql5ZMwfYLHKP0EJ1f8pWUnBuUM2IvZ7KtCWDLF; __Secure-3PSIDCC=AIKkIs3FcdRKypzftAsMYofPb6et8vjvIP3Mdnyro_rMYGU4pA7IPzROfiTHxYOrjbTQJs54HHK7",
   "origin: https://docs.google.com",
   "referer: https://docs.google.com/",
);
curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

$data = <<<DATA
--=====4t9r8ktj5utg=====
content-type: application/http
content-transfer-encoding: binary

GET /drive/v2internal/files/1VstHPlUZBDFqH35sCEj33UanMizfxjjL HTTP/1.1
authorization: SAPISIDHASH 1672334376_972dbd7d6b0c3a4ec7630b0935a44f6056a710d4_u
x-goog-authuser: 0


--=====4t9r8ktj5utg=====--
DATA;

curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

//for debug only!
curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

$resp = curl_exec($curl);
curl_close($curl);
var_dump($resp);

?>

