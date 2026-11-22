/* Scrapdaddy service worker.
 * - Static assets (css/js/images/fonts): stale-while-revalidate, so the app opens fast.
 * - Pages: always from the network (they are per-user and behind login, never cached);
 *   if the phone is offline we show /offline.html.
 * - Everything that is not a plain GET (payments, forms, API) is never touched.
 * Bump VERSION to force clients to drop old caches.
 */
const VERSION = 'sd-v1';
const STATIC_CACHE = 'sd-static-' + VERSION;
const PRECACHE = ['/offline.html', '/images/pwa/icon-192.png', '/images/sd_logo_mark.png'];

self.addEventListener('install', (event) => {
    event.waitUntil(caches.open(STATIC_CACHE).then((c) => c.addAll(PRECACHE)).then(() => self.skipWaiting()));
});

self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys()
            .then((keys) => Promise.all(keys.filter((k) => k.startsWith('sd-static-') && k !== STATIC_CACHE).map((k) => caches.delete(k))))
            .then(() => self.clients.claim())
    );
});

const isStaticAsset = (url) =>
    /\.(?:css|js|png|jpe?g|gif|svg|webp|ico|woff2?|ttf)$/i.test(url.pathname) &&
    !url.pathname.startsWith('/storage/') && !url.pathname.startsWith('/uploads/');

self.addEventListener('fetch', (event) => {
    const req = event.request;
    if (req.method !== 'GET') return;

    const url = new URL(req.url);

    // page navigations: network first, offline page if the network is down
    if (req.mode === 'navigate') {
        event.respondWith(fetch(req).catch(() => caches.match('/offline.html')));
        return;
    }

    // never cache admin or API calls
    if (url.origin === self.location.origin && (url.pathname.startsWith('/admin') || url.pathname.startsWith('/api'))) return;

    // static files (own site + the CDNs we use): stale-while-revalidate
    const cdn = /(cdn\.jsdelivr\.net|cdnjs\.cloudflare\.com|fonts\.googleapis\.com|fonts\.gstatic\.com)$/.test(url.hostname);
    if ((url.origin === self.location.origin && isStaticAsset(url)) || cdn) {
        event.respondWith(
            caches.open(STATIC_CACHE).then(async (cache) => {
                const cached = await cache.match(req);
                const network = fetch(req).then((res) => {
                    if (res && (res.status === 200 || res.type === 'opaque')) cache.put(req, res.clone());
                    return res;
                }).catch(() => cached);
                return cached || network;
            })
        );
    }
});
