// Service Worker for Student Information Management System (SIMS)
const CACHE_NAME = 'sims-v1.3.5';
const STATIC_CACHE = 'sims-static-v1.3.5';
const DYNAMIC_CACHE = 'sims-dynamic-v1.3.5';
const API_CACHE = 'sims-api-v1.3.5';

// Static assets to cache on install
const STATIC_ASSETS = [
    '/',
    '/dashboard',
    '/login',
    '/register',
    '/admin',
    '/admin/users',
    '/admin/users/create',
    '/admin/roles',
    '/admin/roles/create',
    '/admin/permissions',
    '/admin/permissions/create',
    '/admin/configurations',
    '/admin/configurations/create',
    '/admin/theme',
    '/admin/branding',
    '/admin/smtp',
    '/admin/two-factor',
    '/admin/email-templates',
    '/admin/maintenance',
    '/admin/file-security',
    '/profile',
    '/offline.html',
    '/pwa-test.html',
    '/manifest.json',
    '/build/assets/app-KGyycx-Z.css',
    '/build/assets/app-DXNAQ1Ev.js',
    '/tinymce/tinymce.min.js',
    '/images/icons/icon.svg',
    '/images/icons/icon-72x72.svg',
    '/images/icons/icon-96x96.png',
    '/images/icons/icon-192x192.svg',
    '/images/icons/icon-384x384.png',
    '/images/icons/icon-512x512.svg',
    '/images/offline-placeholder.svg',
    'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css',
    'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js',
    'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css',
    'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css',
    'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/webfonts/fa-brands-400.woff2',
    'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/webfonts/fa-brands-400.ttf',
    'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/webfonts/fa-regular-400.woff2',
    'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/webfonts/fa-regular-400.ttf',
    'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/webfonts/fa-solid-900.woff2',
    'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/webfonts/fa-solid-900.ttf',
    'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/webfonts/fa-v4compatibility.woff2',
    'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/webfonts/fa-v4compatibility.ttf',
    '/navigation/offline-routes.json'
];

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
            if (!db.objectStoreNames.contains('uploads')) {
                resolve([]);
                return;
            }
            const transaction = db.transaction(['uploads'], 'readonly');
            const store = transaction.objectStore('uploads');
            const getAllRequest = store.getAll();
            
            getAllRequest.onsuccess = () => {
                resolve(getAllRequest.result || []);
            };
        };
        
        request.onerror = () => {
            resolve([]);
        };
    });
}

// Remove synced upload from IndexedDB
async function removeOfflineUpload(uploadId) {
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
            if (!db.objectStoreNames.contains('uploads')) {
                resolve();
                return;
            }
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
                // Convert relative URLs to absolute URLs based on current origin
                const absoluteAssets = STATIC_ASSETS.map(url => {
                    if (url.startsWith('http')) {
                        return url; // Already absolute
                    }
                    return new URL(url, self.location.origin).href;
                });
                return cache.addAll(absoluteAssets.map(url => {
                    return new Request(url, { cache: 'reload' });
                }));
            }),
            caches.open(DYNAMIC_CACHE),
            caches.open(API_CACHE)
        ])
        .then(() => {
            console.log('Service Worker: All caches initialized');
            return self.skipWaiting();
        })
        .catch((error) => {
            console.error('Service Worker: Error during installation:', error);
        })
    );
});

// Activate event - clean up old caches
self.addEventListener('activate', (event) => {
    console.log('Service Worker: Activating v1.3.3...');
    
    const expectedCaches = [STATIC_CACHE, DYNAMIC_CACHE, API_CACHE];
    
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
                console.log('Service Worker: Cleanup completed, claiming clients');
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
        const response = await fetch(request.clone());
        return response;
    } catch (error) {
        // If offline, store for later sync
        console.log('Service Worker: Storing file upload for offline sync');
        
        const formData = await request.formData();
        const uploadId = await storeOfflineUpload(request, formData);
        
        // Register for background sync
        if ('serviceWorker' in navigator && 'sync' in window.ServiceWorkerRegistration.prototype) {
            self.registration.sync.register('file-upload-sync');
        }
        
        // Return success response to user
        return new Response(
            JSON.stringify({
                success: true,
                message: 'File upload queued for sync when online',
                uploadId: uploadId,
                offline: true
            }),
            {
                status: 202,
                headers: { 'Content-Type': 'application/json' }
            }
        );
    }
}

// Helper functions for request classification
function isStaticAsset(request) {
    const url = new URL(request.url);
    return STATIC_ASSETS.some(asset => {
        if (asset.startsWith('http')) {
            return request.url === asset;
        }
        return url.pathname === asset || url.pathname.startsWith('/css/') || 
               url.pathname.startsWith('/js/') || url.pathname.startsWith('/images/');
    });
}

function isApiRequest(request) {
    const url = new URL(request.url);
    return url.pathname.startsWith('/api/') || 
           DYNAMIC_ROUTES.some(route => url.pathname.startsWith(route));
}

function isNavigationRequest(request) {
    return request.destination === 'document' || 
           request.mode === 'navigate' || 
           (request.method === 'GET' && request.headers.get('accept') && 
            request.headers.get('accept').includes('text/html'));
}

function isNetworkFirstRoute(request) {
    const url = new URL(request.url);
    return NETWORK_FIRST_ROUTES.some(route => url.pathname.startsWith(route));
}

// Cache-first strategy
function cacheFirstStrategy(request, cacheName) {
    return caches.match(request)
        .then(response => {
            if (response) {
                console.log('Service Worker: Cache hit for', request.url);
                return response;
            }
            
            // Try matching by pathname if full URL match fails
            const url = new URL(request.url);
            return caches.match(url.pathname)
                .then(pathResponse => {
                    if (pathResponse) {
                        console.log('Service Worker: Cache hit by pathname for', url.pathname);
                        return pathResponse;
                    }
                    
                    return fetch(request)
                        .then(fetchResponse => {
                            if (!fetchResponse || fetchResponse.status !== 200) {
                                return fetchResponse;
                            }
                            
                            const responseToCache = fetchResponse.clone();
                            caches.open(cacheName)
                                .then(cache => {
                                    cache.put(request, responseToCache);
                                });
                            
                            return fetchResponse;
                        })
                        .catch(error => {
                            console.log('Service Worker: Network failed, checking cache again', error);
                            return handleOfflineRequest(request);
                        });
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
                    
                    // Try matching by pathname if full URL match fails
                    const url = new URL(request.url);
                    return caches.match(url.pathname)
                        .then(pathResponse => {
                            if (pathResponse) {
                                console.log('Service Worker: Cache hit by pathname for API', url.pathname);
                                return pathResponse;
                            }
                            return handleOfflineRequest(request);
                        });
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
            
            // Try matching by pathname if full URL match fails
            const url = new URL(request.url);
            return caches.match(url.pathname)
                .then(pathResponse => {
                    if (pathResponse) {
                        console.log('Service Worker: Cache hit by pathname for navigation', url.pathname);
                        fetchPromise.catch(() => {}); // Prevent unhandled rejection
                        return pathResponse;
                    }
                    
                    // No cache, wait for network
                    return fetchPromise.then(networkResponse => {
                        if (networkResponse) {
                            return networkResponse;
                        }
                        return handleOfflineRequest(request);
                    });
                });
        });
}

// Handle offline requests
function handleOfflineRequest(request) {
    if (isNavigationRequest(request)) {
        console.log('Service Worker: Handling offline navigation request:', request.url);
        
        // First try to find a cached version of the requested page
        return caches.match(request)
            .then(cachedResponse => {
                if (cachedResponse) {
                    console.log('Service Worker: Serving cached page while offline:', request.url);
                    return cachedResponse;
                }
                
                // Try matching by pathname
                const url = new URL(request.url);
                const pathname = url.pathname;
                
                return caches.match(pathname)
                    .then(pathResponse => {
                        if (pathResponse) {
                            console.log('Service Worker: Serving cached page by pathname while offline:', pathname);
                            return pathResponse;
                        }
                        
                        // Try common paths
                        const commonPaths = ['/', '/index.html', '/index.php'];
                        const pathPromises = commonPaths.map(path => caches.match(path));
                        
                        return Promise.all(pathPromises)
                            .then(responses => {
                                const validResponse = responses.find(resp => resp);
                                if (validResponse) {
                                    console.log('Service Worker: Serving cached common path while offline');
                                    return validResponse;
                                }
                                
                                // Finally, serve the offline page
                                console.log('Service Worker: Serving offline page');
                                return caches.match('/offline.html')
                                    .then(offlineResponse => {
                                        if (offlineResponse) {
                                            return offlineResponse;
                                        }
                                        return new Response(`
                                            <!DOCTYPE html>
                                            <html>
                                            <head>
                                                <title>Offline - SIMS</title>
                                                <meta name="viewport" content="width=device-width, initial-scale=1">
                                                <style>
                                                    body { font-family: Arial, sans-serif; text-align: center; padding: 50px; }
                                                    .offline-message { max-width: 400px; margin: 0 auto; }
                                                </style>
                                            </head>
                                            <body>
                                                <div class="offline-message">
                                                    <h1>You're Offline</h1>
                                                    <p>Please check your internet connection and try again.</p>
                                                    <button onclick="window.location.reload()">Retry</button>
                                                </div>
                                            </body>
                                            </html>
                                        `, {
                                            status: 200,
                                            headers: { 'Content-Type': 'text/html' }
                                        });
                                    });
                            });
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
    
    // For image requests, try to serve a placeholder
    if (request.destination === 'image') {
        return caches.match('/images/offline-placeholder.svg')
            .then(response => {
                if (response) {
                    return response;
                }
                // If no placeholder, return a simple SVG
                return new Response('<svg xmlns="http://www.w3.org/2000/svg" width="200" height="200" viewBox="0 0 200 200"><rect width="200" height="200" fill="#f0f0f0"/><text x="50%" y="50%" text-anchor="middle" dominant-baseline="middle" font-family="Arial" font-size="16">Offline</text></svg>', {
                    status: 200,
                    headers: { 'Content-Type': 'image/svg+xml' }
                });
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
        
        // Return offline page for navigation requests
        if (request.mode === 'navigate') {
            return caches.match('/offline.html');
        }
        
        throw error;
    }
}

// Legacy network first strategy - kept for compatibility
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
        console.error('Network first strategy failed:', error);
        
        const cachedResponse = await caches.match(request);
        if (cachedResponse) {
            return cachedResponse;
        }
        
        // Return offline page for navigation requests
        if (request.mode === 'navigate') {
            return caches.match('/offline.html');
        }
        
        throw error;
    }
}

// Check if request is for a static asset
function isStaticAsset(request) {
    const url = new URL(request.url);
    const pathname = url.pathname;
    
    return (
        pathname.endsWith('.css') ||
        pathname.endsWith('.js') ||
        pathname.endsWith('.png') ||
        pathname.endsWith('.jpg') ||
        pathname.endsWith('.jpeg') ||
        pathname.endsWith('.gif') ||
        pathname.endsWith('.svg') ||
        pathname.endsWith('.ico') ||
        pathname.endsWith('.woff') ||
        pathname.endsWith('.woff2') ||
        pathname.endsWith('.ttf') ||
        pathname.endsWith('.eot')
    );
}

// Background sync for offline actions
self.addEventListener('sync', event => {
    console.log('Service Worker: Background sync triggered', event.tag);
    
    if (event.tag === 'background-sync') {
        event.waitUntil(doBackgroundSync());
    }
});

// Handle background sync
async function doBackgroundSync() {
    try {
        // Get pending actions from IndexedDB
        const pendingActions = await getPendingActions();
        
        for (const action of pendingActions) {
            try {
                await fetch(action.url, {
                    method: action.method,
                    headers: action.headers,
                    body: action.body
                });
                
                // Remove successful action
                await removePendingAction(action.id);
            } catch (error) {
                console.error('Failed to sync action:', error);
            }
        }
    } catch (error) {
        console.error('Background sync failed:', error);
    }
}

// Push notification handler
self.addEventListener('push', event => {
    console.log('Service Worker: Push notification received');
    
    const options = {
        body: event.data ? event.data.text() : 'New notification',
        icon: '/images/icons/icon-192x192.png',
        badge: '/images/icons/icon-72x72.png',
        vibrate: [100, 50, 100],
        data: {
            dateOfArrival: Date.now(),
            primaryKey: 1
        },
        actions: [
            {
                action: 'explore',
                title: 'View',
                icon: '/images/icons/checkmark.png'
            },
            {
                action: 'close',
                title: 'Close',
                icon: '/images/icons/xmark.png'
            }
        ]
    };
    
    event.waitUntil(
        self.registration.showNotification('SIMS Notification', options)
    );
});

// Notification click handler
self.addEventListener('notificationclick', event => {
    console.log('Service Worker: Notification clicked');
    
    event.notification.close();
    
    if (event.action === 'explore') {
        event.waitUntil(
            clients.openWindow('/dashboard')
        );
    }
});

// Helper functions for IndexedDB operations
async function getPendingActions() {
    // Implementation would depend on your IndexedDB setup
    return [];
}

async function removePendingAction(id) {
    // Implementation would depend on your IndexedDB setup
    console.log('Removing pending action:', id);
}