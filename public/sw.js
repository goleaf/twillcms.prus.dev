// Service Worker for TwillCMS News Portal
// Version: 1.0.0

const CACHE_NAME = 'twillcms-news-v1.0.0';
const STATIC_CACHE_NAME = 'twillcms-static-v1.0.0';
const DYNAMIC_CACHE_NAME = 'twillcms-dynamic-v1.0.0';
const IMAGE_CACHE_NAME = 'twillcms-images-v1.0.0';

// Define cache strategies
const CACHE_STRATEGIES = {
  CACHE_FIRST: 'cache-first',
  NETWORK_FIRST: 'network-first',
  STALE_WHILE_REVALIDATE: 'stale-while-revalidate',
  NETWORK_ONLY: 'network-only',
  CACHE_ONLY: 'cache-only'
};

// Resources to cache immediately
const STATIC_RESOURCES = [
  '/',
  '/build/manifest.json',
  '/offline.html',
  '/favicon.ico',
];

// API endpoints to cache
const API_CACHE_PATTERNS = [
  /\/api\/v1\/posts(\?.*)?$/,
  /\/api\/v1\/categories(\?.*)?$/,
  /\/api\/v1\/site\/config$/,
];

// Image patterns to cache
const IMAGE_PATTERNS = [
  /\.(?:png|jpg|jpeg|svg|gif|webp|avif)$/,
  /\/images\//,
  /\/storage\/uploads\//,
];

// Dynamic content patterns
const DYNAMIC_PATTERNS = [
  /\/api\/v1\/posts\/[^\/]+$/,
  /\/api\/v1\/categories\/[^\/]+$/,
  /\/api\/v1\/search/,
];

// Performance monitoring
let performanceMetrics = {
  cacheLookups: 0,
  networkRequests: 0,
  cacheHits: 0,
  cacheMisses: 0,
  errors: 0,
  responseTime: [],
};

// Install event - Cache static resources
self.addEventListener('install', (event) => {
  console.log('Service Worker: Installing...');
  
  event.waitUntil(
    Promise.all([
      caches.open(STATIC_CACHE_NAME).then((cache) => {
        console.log('Service Worker: Caching static resources');
        return cache.addAll(STATIC_RESOURCES);
      }),
      self.skipWaiting()
    ])
  );
});

// Activate event - Clean up old caches
self.addEventListener('activate', (event) => {
  console.log('Service Worker: Activating...');
  
  event.waitUntil(
    Promise.all([
      caches.keys().then((cacheNames) => {
        return Promise.all(
          cacheNames.map((cacheName) => {
            if (cacheName !== CACHE_NAME && 
                cacheName !== STATIC_CACHE_NAME && 
                cacheName !== DYNAMIC_CACHE_NAME && 
                cacheName !== IMAGE_CACHE_NAME) {
              console.log('Service Worker: Deleting old cache:', cacheName);
              return caches.delete(cacheName);
            }
          })
        );
      }),
      self.clients.claim()
    ])
  );
});

// Fetch event - Implement caching strategies
self.addEventListener('fetch', (event) => {
  if (event.request.method !== 'GET') return;
  
  const requestUrl = new URL(event.request.url);
  
  // Skip cross-origin requests
  if (requestUrl.origin !== location.origin) {
    return;
  }
  
  performanceMetrics.networkRequests++;
  
  event.respondWith(
    handleFetchRequest(event.request)
  );
});

// Main fetch handler with strategy selection
async function handleFetchRequest(request) {
  const startTime = Date.now();
  
  try {
    const requestUrl = new URL(request.url);
    const pathname = requestUrl.pathname;
    
    let strategy = CACHE_STRATEGIES.NETWORK_FIRST;
    let cacheName = DYNAMIC_CACHE_NAME;
    
    // Determine strategy based on request type
    if (isStaticResource(pathname)) {
      strategy = CACHE_STRATEGIES.CACHE_FIRST;
      cacheName = STATIC_CACHE_NAME;
    } else if (isImageRequest(pathname)) {
      strategy = CACHE_STRATEGIES.CACHE_FIRST;
      cacheName = IMAGE_CACHE_NAME;
    } else if (isApiRequest(pathname)) {
      strategy = CACHE_STRATEGIES.STALE_WHILE_REVALIDATE;
      cacheName = DYNAMIC_CACHE_NAME;
    } else if (isDynamicContent(pathname)) {
      strategy = CACHE_STRATEGIES.NETWORK_FIRST;
      cacheName = DYNAMIC_CACHE_NAME;
    }
    
    const response = await executeStrategy(request, strategy, cacheName);
    
    // Record performance metrics
    const endTime = Date.now();
    performanceMetrics.responseTime.push(endTime - startTime);
    
    // Keep only last 100 response times
    if (performanceMetrics.responseTime.length > 100) {
      performanceMetrics.responseTime.shift();
    }
    
    return response;
    
  } catch (error) {
    console.error('Service Worker: Fetch error:', error);
    performanceMetrics.errors++;
    
    // Return offline page for navigation requests
    if (request.mode === 'navigate') {
      return caches.match('/offline.html') || new Response('Offline', {
        status: 503,
        statusText: 'Service Unavailable'
      });
    }
    
    // Return cached version if available
    return caches.match(request) || new Response('Resource not available', {
      status: 404,
      statusText: 'Not Found'
    });
  }
}

// Execute caching strategy
async function executeStrategy(request, strategy, cacheName) {
  switch (strategy) {
    case CACHE_STRATEGIES.CACHE_FIRST:
      return cacheFirst(request, cacheName);
    
    case CACHE_STRATEGIES.NETWORK_FIRST:
      return networkFirst(request, cacheName);
    
    case CACHE_STRATEGIES.STALE_WHILE_REVALIDATE:
      return staleWhileRevalidate(request, cacheName);
    
    case CACHE_STRATEGIES.NETWORK_ONLY:
      return fetch(request);
    
    case CACHE_STRATEGIES.CACHE_ONLY:
      return caches.match(request);
    
    default:
      return networkFirst(request, cacheName);
  }
}

// Cache-first strategy
async function cacheFirst(request, cacheName) {
  performanceMetrics.cacheLookups++;
  
  const cache = await caches.open(cacheName);
  const cachedResponse = await cache.match(request);
  
  if (cachedResponse) {
    performanceMetrics.cacheHits++;
    return cachedResponse;
  }
  
  performanceMetrics.cacheMisses++;
  
  const networkResponse = await fetch(request);
  
  if (networkResponse && networkResponse.status === 200) {
    const responseToCache = networkResponse.clone();
    
    // Cache with size limit
    await cacheWithSizeLimit(cache, request, responseToCache, cacheName);
  }
  
  return networkResponse;
}

// Network-first strategy
async function networkFirst(request, cacheName) {
  try {
    const networkResponse = await fetch(request);
    
    if (networkResponse && networkResponse.status === 200) {
      const cache = await caches.open(cacheName);
      const responseToCache = networkResponse.clone();
      
      // Cache with TTL
      await cacheWithTTL(cache, request, responseToCache, cacheName);
    }
    
    return networkResponse;
    
  } catch (error) {
    console.log('Service Worker: Network failed, trying cache');
    
    performanceMetrics.cacheLookups++;
    const cachedResponse = await caches.match(request);
    
    if (cachedResponse) {
      performanceMetrics.cacheHits++;
      return cachedResponse;
    }
    
    performanceMetrics.cacheMisses++;
    throw error;
  }
}

// Stale-while-revalidate strategy
async function staleWhileRevalidate(request, cacheName) {
  performanceMetrics.cacheLookups++;
  
  const cache = await caches.open(cacheName);
  const cachedResponse = await cache.match(request);
  
  // Update cache in background
  const fetchPromise = fetch(request).then(async (networkResponse) => {
    if (networkResponse && networkResponse.status === 200) {
      const responseToCache = networkResponse.clone();
      await cacheWithTTL(cache, request, responseToCache, cacheName);
    }
    return networkResponse;
  }).catch((error) => {
    console.log('Service Worker: Background fetch failed:', error);
  });
  
  if (cachedResponse) {
    performanceMetrics.cacheHits++;
    return cachedResponse;
  }
  
  performanceMetrics.cacheMisses++;
  return fetchPromise;
}

// Cache with size limit
async function cacheWithSizeLimit(cache, request, response, cacheName) {
  const MAX_CACHE_SIZE = {
    [STATIC_CACHE_NAME]: 50,
    [DYNAMIC_CACHE_NAME]: 100,
    [IMAGE_CACHE_NAME]: 150,
  };
  
  await cache.put(request, response);
  
  const keys = await cache.keys();
  const maxSize = MAX_CACHE_SIZE[cacheName] || 100;
  
  if (keys.length > maxSize) {
    console.log(`Service Worker: Cache size limit reached for ${cacheName}`);
    await cache.delete(keys[0]);
  }
}

// Cache with TTL (Time To Live)
async function cacheWithTTL(cache, request, response, cacheName) {
  const TTL = {
    [STATIC_CACHE_NAME]: 7 * 24 * 60 * 60 * 1000, // 7 days
    [DYNAMIC_CACHE_NAME]: 1 * 60 * 60 * 1000,      // 1 hour
    [IMAGE_CACHE_NAME]: 30 * 24 * 60 * 60 * 1000,  // 30 days
  };
  
  const ttl = TTL[cacheName] || 1 * 60 * 60 * 1000;
  const expirationTime = Date.now() + ttl;
  
  // Add expiration metadata
  const responseWithMetadata = new Response(response.body, {
    status: response.status,
    statusText: response.statusText,
    headers: {
      ...response.headers,
      'sw-cache-expires': expirationTime.toString()
    }
  });
  
  await cache.put(request, responseWithMetadata);
}

// Helper functions
function isStaticResource(pathname) {
  return pathname.startsWith('/build/') || 
         pathname.startsWith('/assets/') || 
         pathname === '/favicon.ico' ||
         pathname === '/manifest.json' ||
         pathname.endsWith('.css') ||
         pathname.endsWith('.js');
}

function isImageRequest(pathname) {
  return IMAGE_PATTERNS.some(pattern => pattern.test(pathname));
}

function isApiRequest(pathname) {
  return API_CACHE_PATTERNS.some(pattern => pattern.test(pathname));
}

function isDynamicContent(pathname) {
  return DYNAMIC_PATTERNS.some(pattern => pattern.test(pathname));
}

// Message handler for cache management
self.addEventListener('message', (event) => {
  if (event.data && event.data.type === 'SKIP_WAITING') {
    self.skipWaiting();
  }
  
  if (event.data && event.data.type === 'GET_METRICS') {
    const avgResponseTime = performanceMetrics.responseTime.length > 0 
      ? performanceMetrics.responseTime.reduce((a, b) => a + b, 0) / performanceMetrics.responseTime.length 
      : 0;
    
    event.ports[0].postMessage({
      ...performanceMetrics,
      averageResponseTime: avgResponseTime,
      cacheHitRate: performanceMetrics.cacheLookups > 0 
        ? (performanceMetrics.cacheHits / performanceMetrics.cacheLookups) * 100 
        : 0
    });
  }
  
  if (event.data && event.data.type === 'CLEAR_CACHE') {
    event.waitUntil(
      caches.keys().then((cacheNames) => {
        return Promise.all(
          cacheNames.map((cacheName) => caches.delete(cacheName))
        );
      })
    );
  }
});

// Periodic cache cleanup
setInterval(async () => {
  const cacheNames = await caches.keys();
  
  for (const cacheName of cacheNames) {
    const cache = await caches.open(cacheName);
    const keys = await cache.keys();
    
    for (const key of keys) {
      const response = await cache.match(key);
      const expires = response.headers.get('sw-cache-expires');
      
      if (expires && Date.now() > parseInt(expires)) {
        console.log('Service Worker: Removing expired cache entry:', key.url);
        await cache.delete(key);
      }
    }
  }
}, 60 * 60 * 1000); // Run every hour

console.log('Service Worker: Loaded successfully'); 