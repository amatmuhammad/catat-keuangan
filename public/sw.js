const CACHE_NAME = 'catat-keuangan-v5';

const STATIC_ASSETS = [
    '/manifest.json',
    '/assets/css/theme.css',
    '/assets/css/cashflow.css',
    '/assets/js/main.js',
    '/assets/js/pwa-installer.js',
    '/assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js',
    '/assets/libs/simplebar/dist/simplebar.min.css',
    '/assets/libs/simplebar/dist/simplebar.min.js',
    '/assets/libs/@tabler/icons-webfont/tabler-icons.min.css',
    '/icons/icon-192x192.png',
    '/icons/icon-512x512.png',
    '/icons/icon-maskable-512x512.png'
];

self.addEventListener('install', function (event) {
    event.waitUntil(
        caches.open(CACHE_NAME).then(function (cache) {
            return Promise.allSettled(
                STATIC_ASSETS.map(function (asset) {
                    return cache.add(asset);
                })
            );
        }).then(function () {
            return self.skipWaiting();
        })
    );
});

self.addEventListener('activate', function (event) {
    event.waitUntil(
        caches.keys().then(function (keys) {
            return Promise.all(
                keys.filter(function (key) {
                    return key !== CACHE_NAME;
                }).map(function (key) {
                    return caches.delete(key);
                })
            );
        }).then(function () {
            return self.clients.claim();
        })
    );
});

self.addEventListener('fetch', function (event) {
    const request = event.request;

    if (request.method !== 'GET') {
        return;
    }

    const url = new URL(request.url);

    if (url.origin !== self.location.origin) {
        return;
    }

    if (request.mode === 'navigate') {
        event.respondWith(networkFirst(request));
        return;
    }

    if (url.pathname.startsWith('/assets/') || url.pathname.startsWith('/icons/') || url.pathname === '/manifest.json') {
        event.respondWith(staleWhileRevalidate(request));
    }
});

function networkFirst(request) {
    return fetch(request).catch(function () {
        return new Response(
            '<!doctype html><html lang="id"><head><meta charset="utf-8">' +
            '<meta name="viewport" content="width=device-width, initial-scale=1">' +
            '<title>Tidak ada koneksi</title></head>' +
            '<body style="font-family:system-ui;padding:2rem;text-align:center">' +
            '<h1>Koneksi terputus</h1><p>Periksa jaringan Anda lalu muat ulang aplikasi.</p>' +
            '</body></html>',
            { headers: { 'Content-Type': 'text/html; charset=utf-8' } }
        );
    });
}

function staleWhileRevalidate(request) {
    return caches.match(request).then(function (cached) {
        const network = fetch(request).then(function (response) {
            if (response && response.ok) {
                return caches.open(CACHE_NAME).then(function (cache) {
                    cache.put(request, response.clone());
                    return response;
                });
            }

            return response;
        }).catch(function () {
            return cached;
        });

        return cached || network;
    });
}

self.addEventListener('push', function (event) {
    let payload = {};

    try {
        payload = event.data ? event.data.json() : {};
    } catch (error) {
        payload = { body: event.data ? event.data.text() : '' };
    }

    const title = payload.title || 'Perubahan Arus Kas';
    const options = {
        body: payload.body || 'Ada perubahan arus kas baru di Catat Keuangan.',
        icon: payload.icon || '/icons/icon-192x192.png',
        badge: payload.badge || '/icons/icon-192x192.png',
        tag: payload.tag || 'cashflow',
        renotify: true,
        data: payload.data || { url: '/dashboard' },
        vibrate: [200, 100, 200]
    };

    event.waitUntil(self.registration.showNotification(title, options));
});

self.addEventListener('notificationclick', function (event) {
    event.notification.close();

    const target = (event.notification.data && event.notification.data.url) || '/dashboard';

    event.waitUntil(
        self.clients.matchAll({ type: 'window', includeUncontrolled: true }).then(function (clients) {
            for (const client of clients) {
                if ('focus' in client) {
                    client.navigate(target);
                    return client.focus();
                }
            }

            if (self.clients.openWindow) {
                return self.clients.openWindow(target);
            }
        })
    );
});
