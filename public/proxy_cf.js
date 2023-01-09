const AllowedOrigins = [
    'https://www.google.com',
    'https://hoimetruyen.com',
    'http://127.0.0.1',
];

async function fetchAndApply(event) {
    const request = event.request;

    // Get the original URL
    let originalURL = new URL(request.url).searchParams.get('url')

    // Check if the URL is valid
    if (!originalURL) {
        return new Response('No URL provided', {
            status: 400
        });
    }

    if (originalURL.includes('kakao')) {
        originalURL = "https://external-content.duckduckgo.com/iu/?u=" + originalURL;
    }

    const cacheKey = new Request(originalURL, request);
    const cache = caches.default;

    // Check if we have it in the cache
    let cachedResponse = await cache.match(cacheKey);
    let response;

    if (cachedResponse) {
        response = cachedResponse;
    } else {
        response = await fetch(originalURL, {
            cf: {
                cacheEverything: true,
                cacheTtl: 31536000,
                cacheTtlByStatus: {
                    '200-299': 31536000,
                    '300-599': 0,
                },
                cacheKey: originalURL,
            },
        });
    }

    // Check if the response is valid

    let { readable, writable } = new TransformStream();

    response.body.pipeTo(writable);

    initHeaders = {
        // CORS
        'Access-Control-Allow-Methods': 'GET, POST, PUT, DELETE, OPTIONS',
        // cache
        'Cache-Control': 'public, max-age=31536000, immutable',
        'Expires': new Date(Date.now() + 31536000 * 1000).toUTCString(),
        'Content-Type': 'text/html; charset=utf-8',
        ...response.headers
    };

    initHeaders['Access-Control-Allow-Origin'] = '*';

    return new Response(readable, {
        headers: initHeaders,
        // cache
        status: response.status,
    });
}

addEventListener('fetch', event => {
    event.respondWith(fetchAndApply(event));
})
