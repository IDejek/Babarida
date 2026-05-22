/**
 * Service Worker — PWA Support
 *
 * @package Babarida_Dive_Center
 * @version 1.0.0
 */

const BABARIDA_CACHE = 'babarida-v1';
const STATIC_ASSETS = [
    '/',
    '/wp-content/themes/babarida-dive-center/style.css',
    '/wp-content/themes/babarida-dive-center/src/js/app.js',
];

// Install
self.addEventListener('install', function(event) {
    event.waitUntil(
        caches.open(BABARIDA_CACHE).then(function(cache) {
            return cache.addAll(STATIC_ASSETS);
        })
    );
    self.skipWaiting();
});

// Activate
self.addEventListener('activate', function(event) {
    event.waitUntil(
        caches.keys().then(function(keys) {
            return Promise.all(
                keys.filter(function(key) {
                    return key !== BABARIDA_CACHE;
                }).map(function(key) {
                    return caches.delete(key);
                })
            );
        })
    );
    self.clients.claim();
});

// Fetch — cache first, network fallback
self.addEventListener('fetch', function(event) {
    // Skip non-GET and admin requests
    if (event.request.method !== 'GET') return;
    if (event.request.url.indexOf('/wp-admin/') !== -1) return;
    if (event.request.url.indexOf('/wp-login.php') !== -1) return;

    event.respondWith(
        caches.match(event.request).then(function(response) {
            if (response) {
                // Return cache, update in background
                fetch(event.request).then(function(networkResponse) {
                    if (networkResponse && networkResponse.ok) {
                        caches.open(BABARIDA_CACHE).then(function(cache) {
                            cache.put(event.request, networkResponse);
                        });
                    }
                }).catch(function() {});
                return response;
            }

            return fetch(event.request).then(function(networkResponse) {
                if (networkResponse && networkResponse.ok) {
                    var responseClone = networkResponse.clone();
                    caches.open(BABARIDA_CACHE).then(function(cache) {
                        cache.put(event.request, responseClone);
                    });
                }
                return networkResponse;
            }).catch(function() {
                return new Response('Offline', {
                    status: 503,
                    statusText: 'Service Unavailable'
                });
            });
        })
    );
});
