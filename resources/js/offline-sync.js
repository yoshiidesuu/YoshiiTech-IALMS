// Offline Upload Sync Manager
class OfflineSyncManager {
    constructor() {
        this.init();
    }

    init() {
        // Listen for service worker messages
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.addEventListener('message', (event) => {
                this.handleServiceWorkerMessage(event.data);
            });
        }

        // Check for pending uploads on page load
        this.checkPendingUploads();

        // Listen for online/offline events
        window.addEventListener('online', () => {
            this.handleOnlineStatus(true);
        });

        window.addEventListener('offline', () => {
            this.handleOnlineStatus(false);
        });
    }

    // Handle messages from service worker
    handleServiceWorkerMessage(data) {
        if (data.type === 'UPLOAD_SYNCED') {
            this.showSyncNotification('Upload synced successfully!', 'success');
            this.removePendingUpload(data.uploadId);
        } else if (data.type === 'UPLOAD_FAILED') {
            this.showSyncNotification('Upload sync failed. Will retry later.', 'error');
        }
    }

    // Handle online/offline status changes
    handleOnlineStatus(isOnline) {
        const statusElement = document.getElementById('connection-status');
        if (statusElement) {
            statusElement.textContent = isOnline ? 'Online' : 'Offline';
            statusElement.className = isOnline ? 'badge bg-success' : 'badge bg-warning';
        }

        if (isOnline) {
            this.triggerSync();
        }
    }

    // Trigger background sync
    async triggerSync() {
        if ('serviceWorker' in navigator && 'sync' in window.ServiceWorkerRegistration.prototype) {
            try {
                const registration = await navigator.serviceWorker.ready;
                await registration.sync.register('file-upload-sync');
                console.log('Background sync registered');
            } catch (error) {
                console.error('Failed to register background sync:', error);
            }
        }
    }

    // Check for pending uploads in IndexedDB
    async checkPendingUploads() {
        try {
            const uploads = await this.getPendingUploads();
            if (uploads.length > 0) {
                this.showPendingUploadsNotification(uploads.length);
            }
        } catch (error) {
            console.error('Error checking pending uploads:', error);
        }
    }

    // Get pending uploads from IndexedDB
    getPendingUploads() {
        return new Promise((resolve, reject) => {
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
                
                getAllRequest.onerror = () => {
                    reject(getAllRequest.error);
                };
            };
            
            request.onerror = () => {
                reject(request.error);
            };
        });
    }

    // Show notification for pending uploads
    showPendingUploadsNotification(count) {
        const message = `You have ${count} file upload(s) waiting to sync when online.`;
        this.showSyncNotification(message, 'info', true);
    }

    // Show sync notification
    showSyncNotification(message, type = 'info', persistent = false) {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = `alert alert-${type === 'success' ? 'success' : type === 'error' ? 'danger' : 'info'} alert-dismissible fade show position-fixed`;
        notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; max-width: 400px;';
        
        notification.innerHTML = `
            <i class="bi bi-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-triangle' : 'info-circle'}"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        document.body.appendChild(notification);
        
        // Auto-remove after 5 seconds unless persistent
        if (!persistent) {
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.remove();
                }
            }, 5000);
        }
    }

    // Remove pending upload notification
    removePendingUpload(uploadId) {
        // Update UI to reflect successful sync
        console.log(`Upload ${uploadId} synced successfully`);
    }

    // Handle form submissions for offline support
    enhanceFormsForOffline() {
        const forms = document.querySelectorAll('form[enctype="multipart/form-data"]');
        
        forms.forEach(form => {
            form.addEventListener('submit', (event) => {
                if (!navigator.onLine) {
                    // Show offline upload message
                    this.showSyncNotification(
                        'You are offline. This upload will be queued and synced when you reconnect.',
                        'info'
                    );
                }
            });
        });
    }
}

// Initialize offline sync manager when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    const syncManager = new OfflineSyncManager();
    syncManager.enhanceFormsForOffline();
    
    // Add connection status indicator to navbar if it doesn't exist
    const navbar = document.querySelector('.navbar');
    if (navbar && !document.getElementById('connection-status')) {
        const statusContainer = document.createElement('div');
        statusContainer.className = 'ms-auto me-3';
        statusContainer.innerHTML = `
            <span id="connection-status" class="badge ${navigator.onLine ? 'bg-success' : 'bg-warning'}">
                ${navigator.onLine ? 'Online' : 'Offline'}
            </span>
        `;
        navbar.appendChild(statusContainer);
    }
});

// Export for use in other modules
if (typeof module !== 'undefined' && module.exports) {
    module.exports = OfflineSyncManager;
}