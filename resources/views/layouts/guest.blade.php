<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="description" content="Student Information Management System - Secure authentication portal">
        <meta name="theme-color" content="#800020">
        <meta name="mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="default">
        <meta name="apple-mobile-web-app-title" content="SIMS">
        <meta name="msapplication-TileColor" content="#800020">

        <title>{{ config('app.name', 'Laravel') }} - Authentication</title>
        
        <!-- PWA Manifest -->
        <link rel="manifest" href="/manifest.json">
        
        <!-- PWA Icons -->
        <link rel="icon" type="image/svg+xml" href="/images/icons/icon.svg">
        <link rel="apple-touch-icon" href="/images/icons/icon.svg">
        <link rel="mask-icon" href="/images/icons/icon.svg" color="#800020">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Bootstrap CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">

        <!-- Custom Styles -->
        <style>
            :root {
                --maroon-primary: #800020;
                --maroon-secondary: #a0002a;
                --maroon-light: #f8f4f5;
                --maroon-dark: #600018;
            }
            
            body {
                font-family: 'Figtree', sans-serif;
                background: linear-gradient(135deg, var(--maroon-primary) 0%, var(--maroon-secondary) 100%);
                min-height: 100vh;
            }
            
            .auth-card {
                background: rgba(255, 255, 255, 0.95);
                backdrop-filter: blur(10px);
                border: none;
                box-shadow: 0 20px 40px rgba(128, 0, 32, 0.2);
            }
            
            .btn-maroon {
                background-color: var(--maroon-primary);
                border-color: var(--maroon-primary);
                color: white;
            }
            
            .btn-maroon:hover {
                background-color: var(--maroon-dark);
                border-color: var(--maroon-dark);
                color: white;
            }
            
            .form-control:focus {
                border-color: var(--maroon-primary);
                box-shadow: 0 0 0 0.2rem rgba(128, 0, 32, 0.25);
            }
            
            .text-maroon {
                color: var(--maroon-primary) !important;
            }
            
            .text-maroon:hover {
                color: var(--maroon-dark) !important;
            }
            
            .logo-container {
                background: white;
                border-radius: 50%;
                width: 80px;
                height: 80px;
                display: flex;
                align-items: center;
                justify-content: center;
                box-shadow: 0 10px 30px rgba(128, 0, 32, 0.3);
                margin-bottom: 2rem;
            }
        </style>

        <!-- Scripts -->
        @vite(['resources/css/app.scss', 'resources/js/app.js'])

        <!-- Styles -->
        @livewireStyles
    </head>
    <body>
        <div class="auth-container">
            <div class="container">
                {{ $slot }}
            </div>
        </div>

        <!-- Bootstrap JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        @livewireScripts
    
    <!-- PWA Service Worker -->
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js', { scope: '/' })
                    .then(registration => {
                        console.log('SW registered: ', registration);
                        
                        // Check for updates
                        registration.addEventListener('updatefound', () => {
                            const newWorker = registration.installing;
                            newWorker.addEventListener('statechange', () => {
                                if (newWorker.state === 'installed' && navigator.serviceWorker.controller) {
                                    // Show update notification
                                    console.log('New service worker installed, page will reload to update cache');
                                    // Optional: Add a visual notification here
                                }
                            });
                        });
                    })
                    .catch(registrationError => {
                        console.log('SW registration failed: ', registrationError);
                    });
            });
        }
    </script>
</body>
</html>
