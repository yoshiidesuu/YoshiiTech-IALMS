<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.scss', 'resources/js/app.js'])

        <!-- Styles -->
        @livewireStyles
    </head>
    <body>
        <x-banner />

        <div class="min-vh-100 bg-light">
            @livewire('navigation-menu')

            <!-- Page Heading -->
            @if (isset($header))
                <header class="bg-white shadow-sm">
                    <div class="container-fluid py-4">
                        {{ $header }}
                    </div>
                </header>
            @endif

            <!-- Page Content -->
            <main class="container-fluid">
                @yield('content')
            </main>
        </div>

        @stack('modals')

        @livewireScripts
        
        <!-- PWA Installation Script -->
        <script>
            // PWA Installation
            let deferredPrompt;
            const installButtons = document.querySelectorAll('#pwa-install-btn-nav, #pwa-install-btn-responsive');
            
            window.addEventListener('beforeinstallprompt', (e) => {
                // Prevent the mini-infobar from appearing on mobile
                e.preventDefault();
                // Stash the event so it can be triggered later
                deferredPrompt = e;
                // Show the install buttons
                installButtons.forEach(btn => {
                    if (btn) btn.classList.remove('d-none');
                });
            });
            
            // Show install buttons if not running as PWA
            if (!window.matchMedia('(display-mode: standalone)').matches) {
                // Show install buttons after page loads
                window.addEventListener('load', () => {
                    setTimeout(() => {
                        installButtons.forEach(btn => {
                            if (btn) btn.classList.remove('d-none');
                        });
                    }, 1000); // Show after 1 second
                });
            }
            
            function installPWA() {
                if (deferredPrompt) {
                    // Use the deferred prompt if available
                    deferredPrompt.prompt();
                    // Wait for the user to respond to the prompt
                    deferredPrompt.userChoice.then((choiceResult) => {
                        if (choiceResult.outcome === 'accepted') {
                            console.log('User accepted the install prompt');
                            // Show success notification
                            showInstallSuccessNotification();
                        } else {
                            console.log('User dismissed the install prompt');
                        }
                        deferredPrompt = null;
                        // Hide the install buttons
                        installButtons.forEach(btn => {
                            if (btn) btn.classList.add('d-none');
                        });
                    });
                } else {
                    // Fallback: Show manual installation instructions
                    showInstallInstructions();
                }
            }
            
            function showInstallInstructions() {
                const modal = document.createElement('div');
                modal.className = 'modal fade show';
                modal.style.display = 'block';
                modal.innerHTML = `
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title"><i class="bi bi-download me-2"></i>Install App</h5>
                                <button type="button" class="btn-close" onclick="this.closest('.modal').remove()"></button>
                            </div>
                            <div class="modal-body">
                                <p><strong>To install this app:</strong></p>
                                <div class="mb-3">
                                    <h6><i class="bi bi-phone me-2"></i>On Mobile (Android):</h6>
                                    <ol class="small">
                                        <li>Tap the menu (⋮) in your browser</li>
                                        <li>Select "Add to Home screen" or "Install app"</li>
                                        <li>Tap "Add" or "Install"</li>
                                    </ol>
                                </div>
                                <div class="mb-3">
                                    <h6><i class="bi bi-phone me-2"></i>On Mobile (iOS):</h6>
                                    <ol class="small">
                                        <li>Tap the Share button (□↗)</li>
                                        <li>Scroll down and tap "Add to Home Screen"</li>
                                        <li>Tap "Add"</li>
                                    </ol>
                                </div>
                                <div>
                                    <h6><i class="bi bi-laptop me-2"></i>On Desktop:</h6>
                                    <ol class="small">
                                        <li>Look for an install icon in the address bar</li>
                                        <li>Or use browser menu → "Install [App Name]"</li>
                                    </ol>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" onclick="this.closest('.modal').remove()">Close</button>
                            </div>
                        </div>
                    </div>
                `;
                document.body.appendChild(modal);
                
                // Auto-remove modal after 10 seconds
                setTimeout(() => {
                    modal.remove();
                }, 10000);
            }
            
            function showInstallSuccessNotification() {
                const notification = document.createElement('div');
                notification.className = 'alert alert-success alert-dismissible fade show position-fixed';
                notification.style.cssText = 'top: 80px; right: 20px; z-index: 1050; max-width: 300px;';
                notification.innerHTML = `
                    <i class="bi bi-check-circle me-2"></i>
                    <strong>App Installed!</strong><br>
                    <small>You can now access the app from your home screen.</small>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                `;
                document.body.appendChild(notification);
                
                // Auto-dismiss after 5 seconds
                setTimeout(() => {
                    if (notification.parentNode) {
                        notification.remove();
                    }
                }, 5000);
            }
            
            // Hide install buttons if app is already installed
            window.addEventListener('appinstalled', () => {
                installButtons.forEach(btn => {
                    if (btn) btn.classList.add('d-none');
                });
                console.log('PWA was installed');
            });
            
            // Check if app is already installed (for browsers that support it)
            if (window.matchMedia && window.matchMedia('(display-mode: standalone)').matches) {
                installButtons.forEach(btn => {
                    if (btn) btn.classList.add('d-none');
                });
            }
        </script>
    </body>
</html>
