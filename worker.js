addEventListener('fetch', event => {
    event.respondWith(handleRequest(event))
})

async function handleRequest(event) {
    const request = event.request;

    // Get upload_id from request first path segment
    const upload_id = request.url.split('/')[3];
    if (!upload_id) {
        // nginx default 404 page
        return new Response('404 page not found', {
            status: 404,
            headers: {
                'content-type': 'text/plain',
            }
        });
    }

    cache = caches.default;
    const url = new URL(request.url); // making an url object from the request
    url.pathname = '/cache/' + upload_id; // changing the pathname

    let cacheKey = new Request(url.toString(), request);
    let cachedResponse = await cache.match(cacheKey);


    if (cachedResponse) {
        body = await cachedResponse.text();
        return Response.redirect(body, 302);
    }

    let API_URL = `https://docs.google.com/upload/photos/resumable?upload_id=${upload_id}`
    let response = await fetch(API_URL, {
        method: 'GET',
    });

    // json decode response
    let json = await response.json();

    // get photo url
    let INFO = json.sessionStatus.additionalInfo['uploader_service.GoogleRupioAdditionalInfo'].completionInfo.customerSpecificInfo;

    // get authKey from params in url (photoPageUrl)
    let authKey = INFO.photoPageUrl.split('?')[1].split('=')[1];

    // get photo url use string format %s/album/%s/%s?authKey=%s";
    let PHOTO_URL = `https://get.google.com/albumarchive/${INFO.username}/album/${INFO.albumMediaKey}/${INFO.photoMediaKey}?authKey=${authKey}`;


    response = await fetch(PHOTO_URL, {
        method: 'GET',
    });

    // extract data-dlu from response with phôtMediaKey data-mk="${INFO.photoMediaKey}".*data-dlu="(.*?)
    let pattern = new RegExp(`data-mk="${INFO.photoMediaKey}".*data-dlu="(.*?)"`, 'g');
    let match = pattern.exec(await response.text());


    let PHOTO_URL_DOWNLOAD = match[1];
    response = Response.redirect(PHOTO_URL_DOWNLOAD, 302);

    event.waitUntil(cache.put(cacheKey, new Response(PHOTO_URL_DOWNLOAD, {
        headers: {
            'content-type': 'text/plain',
        }
    }), { cacheTtl: 3600, cacheEverything: true, cacheMode: 'force-cache' }));


    return response;
}
