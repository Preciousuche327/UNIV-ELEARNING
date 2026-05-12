const CACHE_NAME = 'univ-elearning-v2';
const STATIC_ASSETS = [
  './public/css/style.css',
  './manifest.json',
  './public/images/icons/icon-192.png',
  './public/images/icons/icon-512.png'
];

// Install Event - Cache static assets
self.addEventListener('install', (event) => {
  self.skipWaiting();
  event.waitUntil(
    caches.open(CACHE_NAME).then((cache) => {
      return cache.addAll(STATIC_ASSETS);
    })
  );
});

// Activate Event - Clean up old caches
self.addEventListener('activate', (event) => {
  event.waitUntil(
    caches.keys().then((keys) => {
      return Promise.all(
        keys.filter((key) => key !== CACHE_NAME).map((key) => caches.delete(key))
      );
    })
  );
});

// Fetch Event - Network First for pages, Cache First for statics
self.addEventListener('fetch', (event) => {
  const url = new URL(event.request.url);
  
  // For navigation requests (HTML/PHP), try network first
  if (event.request.mode === 'navigate') {
    event.respondWith(
      fetch(event.request)
        .catch(() => caches.match('./index.php')) // Fallback to cached index if offline
    );
    return;
  }

  // For other requests (CSS, Images), try cache first
  event.respondWith(
    caches.match(event.request).then((response) => {
      return response || fetch(event.request);
    })
  );
});
