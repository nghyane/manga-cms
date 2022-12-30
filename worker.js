addEventListener('fetch', event => {
    event.respondWith(handleRequest(event.request))
})

async function handleRequest(request) {
    // check cache for response
    let cache = caches.default;

    let cachedResponse = await cache.match(request).catch(err => {
        console.log(err);
    });

    if (cachedResponse) {
        return cachedResponse;
    }

    // Parse the query string of the request
    const url = new URL(request.url);

    // Get the file ID from first path
    const fileId = url.pathname.split('/')[1];

    // If the file ID is not set, return a custom 404 page
    if (!fileId) {
        return new Response('404 Page Not Found', {
            status: 404,
            headers: {
                'Content-Type': 'text/html',

            }
        });
    }

    const cacheUrl = new URL(request.url);
    cacheUrl.pathname = '/cachedAccessToken';

    // Try to get the access token from the cache
    let accessToken = null;
    // Convert to a GET to be able to cache
    const cacheKey = new Request(cacheUrl.toString(), {
        method: 'GET',
    });

    // check cache for response
    let cachedAccessToken = await cache.match(cacheKey);
    if (cachedAccessToken) {
        accessToken = cachedAccessToken.body;
    }


    let responseHeaders = {
        'Content-Type': 'application/octet-stream',
        'Access-Control-Allow-Origin': '*',
        'Access-Control-Allow-Methods': 'GET, HEAD, POST, OPTIONS',
    }

    // If the access token is not in the cache, fetch it from the Google OAuth 2.0 token endpoint
    if (!accessToken) {
        // Replace with your own client ID, client secret, and refresh token
        const clientId = '510852036652-c5o6fg9sbbq1eljvniq79lmdlunabu5b.apps.googleusercontent.com';
        const clientSecret = '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';
        const refreshToken = '1//0gbykenEAgFrDCgYIARAAGBASNwF-L9Ir9d3NLeFzAKkA_EuKOdEUz6GF1RUtCsuCs3Z943Xg_tY1TDvzir540pceptuW8NXt17U';

        // Send a request to the Google OAuth 2.0 token endpoint to get a new access token
        const response = await fetch('https://oauth2.googleapis.com/token', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: `client_id=${clientId}&client_secret=${clientSecret}&refresh_token=${refreshToken}&grant_type=refresh_token`
        });

        const data = await response.json();
        accessToken = data.access_token;

        // Get the TTL value returned by the token endpoint
        const ttl = data.expires_in;

        // Cache the access token
        cache.put(cacheKey, new Response(accessToken, {
            status: 200,
            headers: {
                'Content-Type': 'text/plain',
                'Cache-Control': `public, max-age=${ttl}`
            }
        }));
    }

    // Use the access token to authenticate with the Google Drive API
    const headers = new Headers({
        'Authorization': `Bearer ${accessToken}`
    });

    // Make a request to the Google Drive API to access the file
    const res = await fetch(`https://www.googleapis.com/drive/v3/files/${fileId}?alt=media`, {
        headers
    });

    // cache the response
    cache.put(request, res.clone());

    // Return media file response
    return new Response(res.body, {
        status: res.status,
        headers: responseHeaders
    });
}
