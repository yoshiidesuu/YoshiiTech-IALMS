// Service Worker for Student Information Management System (SIMS)
const CACHE_NAME = 'pwa-cache-v1.3.3';
const STATIC_CACHE = 'static-cache-v1.3.3';
const DYNAMIC_CACHE = 'dynamic-cache-v1.3.3';
const RUNTIME_CACHE = 'runtime-cache-v1.3.3';

// Core routes to cache
const CORE_ROUTES = [
    '/',
    '/dashboard',
    '/admin',
    '/admin/users',
    '/admin/roles', 
    '/admin/permissions',
    '/admin/configurations',
    '/admin/theme',
    '/admin/file-security',
    '/profile',
    '/profile/show',
    '/offline.html'
];

// Static assets to cache
const STATIC_ASSETS = [
    @if(app()->environment('production'))
        '{{ Vite::asset('resources/css/app.scss') }}',
        '{{ Vite::asset('resources/js/app.js') }}',
    @else
        '/build/assets/app-KGyycx-Z.css',
        '/build/assets/app-1KTkosIZ.js',
    @endif
    '/tinymce/tinymce.min.js',
    '/tinymce/themes/silver/theme.min.js',
    '/tinymce/plugins/lists/plugin.min.js',
    '/tinymce/plugins/link/plugin.min.js',
    '/tinymce/plugins/image/plugin.min.js',
    '/tinymce/plugins/table/plugin.min.js',
    '/tinymce/plugins/code/plugin.min.js',
    '/tinymce/plugins/wordcount/plugin.min.js',
    '/icons/icon-192x192.png',
    '/icons/icon-512x512.png',
    '/favicon.ico'
];

// All cacheable resources
const ALL_CACHE_RESOURCES = [...CORE_ROUTES, ...STATIC_ASSETS];

// Dynamic routes that should be cached
const DYNAMIC_ROUTES = [
    '/api/users',
    '/api/roles',
    '/api/permissions',
    '/api/configurations',
    '/api/dashboard-stats',
    '/api/user/profile',
    '/admin/theme/css',
    '/admin/branding/settings',
    '/admin/smtp/settings',
    '/admin/two-factor/status',
    '/admin/email-templates/get',
    '/admin/maintenance/status',
    '/admin/file-security/status'
];

// Network-first routes (always try network first)
const NETWORK_FIRST_ROUTES = [
    '/api/auth/',
    '/api/logout',
    '/api/login'
];

// Offline file upload queue
let offlineUploads = [];

// Background sync for file uploads
self.addEventListener('sync', (event) => {
    if (event.tag === 'file-upload-sync') {
        event.waitUntil(syncOfflineUploads());
    }
});

// Function to sync offline uploads when back online
async function syncOfflineUploads() {
    console.log('Service Worker: Syncing offline uploads...');
    
    const uploads = await getOfflineUploads();
    
    for (const upload of uploads) {
        try {
            const response = await fetch(upload.url, {
                method: upload.method,
                body: upload.formData,
                headers: upload.headers
            });
            
            if (response.ok) {
                console.log('Service Worker: Successfully synced upload:', upload.id);
                await removeOfflineUpload(upload.id);
            } else {
                console.error('Service Worker: Failed to sync upload:', upload.id, response.status);
            }
        } catch (error) {
            console.error('Service Worker: Error syncing upload:', upload.id, error);
        }
    }
}

// Store offline upload
async function storeOfflineUpload(request, formData) {
    const upload = {
        id: Date.now() + Math.random(),
        url: request.url,
        method: request.method,
        formData: formData,
        headers: Object.fromEntries(request.headers.entries()),
        timestamp: Date.now()
    };
    
    const uploads = await getOfflineUploads();
    uploads.push(upload);
    
    return new Promise((resolve) => {
        const request = indexedDB.open('sims-offline-uploads', 1);
        
        request.onupgradeneeded = () => {
            const db = request.result;
            if (!db.objectStoreNames.contains('uploads')) {
                db.createObjectStore('uploads', { keyPath: 'id' });
            }
        };
        
        request.onsuccess = () => {
            const db = request.result;
            const transaction = db.transaction(['uploads'], 'readwrite');
            const store = transaction.objectStore('uploads');
            store.put(upload);
            
            transaction.oncomplete = () => {
                resolve(upload.id);
            };
        };
    });
}

// Get offline uploads from IndexedDB
async function getOfflineUploads() {
    return new Promise((resolve) => {
        const request = indexedDB.open('sims-offline-uploads', 1);
        
        request.onupgradeneeded = () => {
            const db = request.result;
            if (!db.objectStoreNames.contains('uploads')) {
                db.createObjectStore('uploads', { keyPath: 'id' });
            }
        };
        
        request.onsuccess = () => {
            const db = request.result;
            const transaction = db.transaction(['uploads'], 'readonly');
            const store = transaction.objectStore('uploads');
            const getAllRequest = store.getAll();
            
            getAllRequest.onsuccess = () => {
                resolve(getAllRequest.result || []);
            };
        };
    });
}

// Remove offline upload from IndexedDB
async function removeOfflineUpload(uploadId) {
    return new Promise((resolve) => {
        const request = indexedDB.open('sims-offline-uploads', 1);
        
        request.onsuccess = () => {
            const db = request.result;
            const transaction = db.transaction(['uploads'], 'readwrite');
            const store = transaction.objectStore('uploads');
            store.delete(uploadId);
            
            transaction.oncomplete = () => {
                resolve();
            };
        };
    });
}

// Install event - cache static assets
self.addEventListener('install', (event) => {
    console.log('Service Worker: Installing v1.3.3...');
    
    event.waitUntil(
        Promise.all([
            caches.open(STATIC_CACHE).then((cache) => {
                console.log('Service Worker: Caching static assets');
                return cache.addAll(STATIC_ASSETS);
            }),
            caches.open(DYNAMIC_CACHE).then((cache) => {
                console.log('Service Worker: Caching core routes');
                return cache.addAll(CORE_ROUTES);
            })
        ])
        .then(() => {
            console.log('Service Worker: All assets cached successfully');
            return self.skipWaiting();
        })
        .catch((error) => {
            console.error('Service Worker: Failed to cache assets:', error);
        })
    );
});

// Activate event - clean up old caches
self.addEventListener('activate', (event) => {
    console.log('Service Worker: Activating v1.3.3...');
    
    const expectedCaches = [STATIC_CACHE, DYNAMIC_CACHE, RUNTIME_CACHE];
    
    event.waitUntil(
        caches.keys()
            .then((cacheNames) => {
                return Promise.all(
                    cacheNames.map((cacheName) => {
                        if (!expectedCaches.includes(cacheName)) {
                            console.log('Service Worker: Deleting old cache:', cacheName);
                            return caches.delete(cacheName);
                        }
                    })
                );
            })
            .then(() => {
                console.log('Service Worker: Activated successfully');
                return self.clients.claim();
            })
            .catch((error) => {
                console.error('Service Worker: Error during activation:', error);
            })
    );
});

// Fetch event - serve from cache or network with different strategies
self.addEventListener('fetch', (event) => {
    const { request } = event;
    const url = new URL(request.url);
    
    // Handle file uploads when offline
    if (request.method === 'POST' && isFileUpload(request)) {
        event.respondWith(handleFileUpload(request));
        return;
    }
    
    // Skip non-GET requests (except file uploads handled above)
    if (request.method !== 'GET') {
        return;
    }
    
    // Skip chrome-extension and other non-http requests
    if (!request.url.startsWith('http')) {
        return;
    }
    
    // Handle different request types with appropriate strategies
    if (isStaticAsset(request)) {
        // Cache-first strategy for static assets
        event.respondWith(cacheFirstStrategy(request, STATIC_CACHE));
    } else if (isApiRequest(request)) {
        // Network-first strategy for API requests
        event.respondWith(networkFirstStrategy(request, API_CACHE));
    } else if (isNavigationRequest(request)) {
        // Stale-while-revalidate for navigation
        event.respondWith(staleWhileRevalidateStrategy(request, DYNAMIC_CACHE));
    } else {
        // Default cache-first for other requests
        event.respondWith(cacheFirstStrategy(request, DYNAMIC_CACHE));
    }
});

// Check if request is a file upload
function isFileUpload(request) {
    const contentType = request.headers.get('content-type');
    return contentType && contentType.includes('multipart/form-data');
}

// Handle file upload requests
async function handleFileUpload(request) {
    try {
        // Try network first
        const response = await fetch(request);
        return response;
    } catch (error) {
        console.log('Service Worker: File upload failed, storing for later sync', error);
        
        // Store for background sync
        const formData = await request.formData();
        const uploadId = await storeOfflineUpload(request, formData);
        
        // Register background sync
        if ('serviceWorker' in navigator && 'sync' in window.ServiceWorkerRegistration.prototype) {
            const registration = await self.registration;
            await registration.sync.register('file-upload-sync');
        }
        
        return new Response(JSON.stringify({
            success: false,
            message: 'Upload queued for when connection is restored',
            uploadId: uploadId
        }), {
            status: 202,
            statusText: 'Accepted',
            headers: { 'Content-Type': 'application/json' }
        });
    }
}

// Check if request is for static assets
function isStaticAsset(request) {
    const url = new URL(request.url);
    return url.pathname.includes('/build/') || 
           url.pathname.includes('/css/') || 
           url.pathname.includes('/js/') || 
           url.pathname.includes('/images/') || 
           url.pathname.includes('/fonts/') || 
           url.pathname.includes('/tinymce/') ||
           url.pathname.endsWith('.css') ||
           url.pathname.endsWith('.js') ||
           url.pathname.endsWith('.png') ||
           url.pathname.endsWith('.jpg') ||
           url.pathname.endsWith('.jpeg') ||
           url.pathname.endsWith('.gif') ||
           url.pathname.endsWith('.svg') ||
           url.pathname.endsWith('.ico') ||
           url.pathname.endsWith('.woff') ||
           url.pathname.endsWith('.woff2') ||
           url.pathname.endsWith('.ttf') ||
           url.pathname.endsWith('.eot');
}

// Check if request is for API
function isApiRequest(request) {
    const url = new URL(request.url);
    return url.pathname.startsWith('/api/') || 
           url.pathname.startsWith('/admin/api/') ||
           DYNAMIC_ROUTES.some(route => url.pathname.startsWith(route));
}

// Check if request is navigation
function isNavigationRequest(request) {
    return request.mode === 'navigate' || 
           (request.method === 'GET' && request.headers.get('accept').includes('text/html'));
}

// Cache-first strategy
function cacheFirstStrategy(request, cacheName) {
    return caches.match(request)
        .then(response => {
            if (response) {
                return response;
            }
            return fetch(request)
                .then(networkResponse => {
                    if (networkResponse && networkResponse.status === 200) {
                        const responseToCache = networkResponse.clone();
                        caches.open(cacheName)
                            .then(cache => {
                                cache.put(request, responseToCache);
                            });
                    }
                    return networkResponse;
                })
                .catch(error => {
                    console.log('Service Worker: Network failed, no cache available', error);
                    return handleOfflineRequest(request);
                });
        });
}

// Network-first strategy
function networkFirstStrategy(request, cacheName) {
    return fetch(request)
        .then(response => {
            if (response && response.status === 200) {
                const responseToCache = response.clone();
                caches.open(cacheName)
                    .then(cache => {
                        cache.put(request, responseToCache);
                    });
            }
            return response;
        })
        .catch(error => {
            console.log('Service Worker: Network failed, trying cache', error);
            return caches.match(request)
                .then(response => {
                    if (response) {
                        return response;
                    }
                    return handleOfflineRequest(request);
                });
        });
}

// Stale-while-revalidate strategy
function staleWhileRevalidateStrategy(request, cacheName) {
    const fetchPromise = fetch(request)
        .then(response => {
            if (response && response.status === 200) {
                const responseToCache = response.clone();
                caches.open(cacheName)
                    .then(cache => {
                        cache.put(request, responseToCache);
                    });
            }
            return response;
        })
        .catch(error => {
            console.log('Service Worker: Network failed for navigation', error);
        });
    
    return caches.match(request)
        .then(response => {
            if (response) {
                // Return cached version immediately, update in background
                fetchPromise.catch(() => {}); // Prevent unhandled rejection
                return response;
            }
            // No cache, wait for network
            return fetchPromise.then(networkResponse => {
                if (networkResponse) {
                    return networkResponse;
                }
                return handleOfflineRequest(request);
            });
        });
}

// Handle offline requests
function handleOfflineRequest(request) {
    if (request.destination === 'document' || request.mode === 'navigate') {
        return caches.match('/offline.html')
            .then(response => {
                if (response) {
                    return response;
                }
                return new Response('Offline - Please check your connection', {
                    status: 503,
                    statusText: 'Service Unavailable',
                    headers: { 'Content-Type': 'text/html' }
                });
            });
    }
    
    // For API requests, return JSON error
    if (isApiRequest(request)) {
        return new Response(JSON.stringify({
            error: 'Offline',
            message: 'No internet connection available'
        }), {
            status: 503,
            statusText: 'Service Unavailable',
            headers: { 'Content-Type': 'application/json' }
        });
    }
    
    // Generic offline response
    return new Response('Offline', {
        status: 503,
        statusText: 'Service Unavailable'
    });
}

// Legacy cache first strategy - kept for compatibility
async function cacheFirst(request) {
    try {
        const cachedResponse = await caches.match(request);
        if (cachedResponse) {
            return cachedResponse;
        }
        
        const networkResponse = await fetch(request);
        
        // Cache successful responses
        if (networkResponse.status === 200) {
            const cache = await caches.open(STATIC_CACHE);
            cache.put(request, networkResponse.clone());
        }
        
        return networkResponse;
    } catch (error) {
        console.error('Cache first strategy failed:', error);
        return handleOfflineRequest(request);
    }
}

// Network first strategy - kept for compatibility
async function networkFirst(request) {
    try {
        const networkResponse = await fetch(request);
        
        // Cache successful responses
        if (networkResponse.status === 200) {
            const cache = await caches.open(DYNAMIC_CACHE);
            cache.put(request, networkResponse.clone());
        }
        
        return networkResponse;
    } catch (error) {
        console.log('Network first failed, trying cache:', error);
        
        const cachedResponse = await caches.match(request);
        if (cachedResponse) {
            return cachedResponse;
        }
        
        return handleOfflineRequest(request);
    }
}