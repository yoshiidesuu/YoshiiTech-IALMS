<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="A comprehensive student information management system with role-based access control">
    <meta name="keywords" content="student management, education, school, university, enrollment, grades">
    <meta name="theme-color" content="#800020">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="SIMS">
    <meta name="msapplication-TileColor" content="#800020">
    
    <title>{{ config('app.name', 'SIMS') }} - Student Information Management System</title>
    
    <!-- PWA Manifest -->
    <link rel="manifest" href="/manifest.json">
    
    <!-- PWA Icons -->
    <link rel="icon" type="image/svg+xml" href="/images/icons/icon.svg">
    <link rel="apple-touch-icon" href="/images/icons/icon.svg">
    <link rel="mask-icon" href="/images/icons/icon.svg" color="#800020">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --maroon-primary: #800020;
            --maroon-secondary: #a0002a;
            --maroon-light: #b33347;
            --maroon-dark: #600018;
            --gold-accent: #ffd700;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            line-height: 1.6;
        }
        
        .navbar {
            background: linear-gradient(135deg, var(--maroon-primary) 0%, var(--maroon-secondary) 100%);
            box-shadow: 0 2px 10px rgba(128, 0, 32, 0.2);
        }
        
        .hero-section {
            background: linear-gradient(135deg, var(--maroon-primary) 0%, var(--maroon-secondary) 100%);
            color: white;
            padding: 100px 0;
            position: relative;
            overflow: hidden;
        }
        
        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="50" cy="50" r="1" fill="%23ffffff" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>') repeat;
            opacity: 0.1;
        }
        
        .hero-content {
            position: relative;
            z-index: 2;
        }
        
        .btn-primary {
            background: var(--gold-accent);
            border-color: var(--gold-accent);
            color: var(--maroon-dark);
            font-weight: 600;
            padding: 12px 30px;
            border-radius: 50px;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            background: #e6c200;
            border-color: #e6c200;
            color: var(--maroon-dark);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 215, 0, 0.4);
        }
        
        .btn-outline-light {
            border-color: white;
            color: white;
            font-weight: 500;
            padding: 12px 30px;
            border-radius: 50px;
            transition: all 0.3s ease;
        }
        
        .btn-outline-light:hover {
            background: white;
            color: var(--maroon-primary);
            transform: translateY(-2px);
        }
        
        .feature-card {
            background: white;
            border-radius: 15px;
            padding: 40px 30px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            height: 100%;
        }
        
        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        }
        
        .feature-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, var(--maroon-primary), var(--maroon-secondary));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            color: white;
            font-size: 2rem;
        }
        
        .stats-section {
            background: #f8f9fa;
            padding: 80px 0;
        }
        
        .stat-item {
            text-align: center;
            padding: 20px;
        }
        
        .stat-number {
            font-size: 3rem;
            font-weight: 700;
            color: var(--maroon-primary);
            display: block;
        }
        
        .stat-label {
            color: #6c757d;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-size: 0.9rem;
        }
        
        .footer {
            background: var(--maroon-dark);
            color: white;
            padding: 50px 0 30px;
        }
        
        .footer a {
            color: #ccc;
            text-decoration: none;
            transition: color 0.3s ease;
        }
        
        .footer a:hover {
            color: var(--gold-accent);
        }
        
        .section-title {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--maroon-primary);
            margin-bottom: 20px;
        }
        
        .section-subtitle {
            font-size: 1.2rem;
            color: #6c757d;
            margin-bottom: 50px;
        }
        
        @media (max-width: 768px) {
            .hero-section {
                padding: 60px 0;
            }
            
            .section-title {
                font-size: 2rem;
            }
            
            .stat-number {
                font-size: 2.5rem;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
        <div class="container">
            <a class="navbar-brand fw-bold" href="#">
                <i class="fas fa-graduation-cap me-2"></i>
                {{ config('app.name', 'SIMS') }}
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#features">Features</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#about">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#contact">Contact</a>
                    </li>
                    @auth
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('dashboard') }}">
                                <i class="fas fa-tachometer-alt me-1"></i> Dashboard
                            </a>
                        </li>
                    @else
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">
                                <i class="fas fa-sign-in-alt me-1"></i> Login
                            </a>
                        </li>
                        @if (Route::has('register'))
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('register') }}">
                                    <i class="fas fa-user-plus me-1"></i> Register
                                </a>
                            </li>
                        @endif
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 hero-content">
                    <h1 class="display-4 fw-bold mb-4">
                        Streamline Your Educational Institution
                    </h1>
                    <p class="lead mb-4">
                        Comprehensive Student Information Management System designed to simplify 
                        student enrollment, grade management, and administrative tasks.
                    </p>
                    <div class="d-flex gap-3 flex-wrap">
                        @auth
                            <a href="{{ route('dashboard') }}" class="btn btn-primary btn-lg">
                                <i class="fas fa-tachometer-alt me-2"></i> Go to Dashboard
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="btn btn-primary btn-lg">
                                <i class="fas fa-sign-in-alt me-2"></i> Get Started
                            </a>
                            <a href="#features" class="btn btn-outline-light btn-lg">
                                <i class="fas fa-info-circle me-2"></i> Learn More
                            </a>
                        @endauth
                    </div>
                </div>
                <div class="col-lg-6 text-center">
                    <div class="hero-image mt-5 mt-lg-0">
                        <i class="fas fa-school" style="font-size: 15rem; opacity: 0.3;"></i>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-5">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="section-title">Powerful Features</h2>
                <p class="section-subtitle">
                    Everything you need to manage your educational institution efficiently
                </p>
            </div>
            
            <div class="row g-4">
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <h4 class="fw-bold mb-3">Student Management</h4>
                        <p class="text-muted">
                            Comprehensive student profiles, enrollment tracking, and 
                            personal information management in one centralized system.
                        </p>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <h4 class="fw-bold mb-3">Grade Management</h4>
                        <p class="text-muted">
                            Track academic performance, generate report cards, and 
                            monitor student progress with detailed analytics.
                        </p>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        <h4 class="fw-bold mb-3">Class Scheduling</h4>
                        <p class="text-muted">
                            Efficient class scheduling, room management, and 
                            timetable generation for optimal resource utilization.
                        </p>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-dollar-sign"></i>
                        </div>
                        <h4 class="fw-bold mb-3">Financial Management</h4>
                        <p class="text-muted">
                            Handle tuition fees, payment tracking, financial reports, 
                            and billing with integrated accounting features.
                        </p>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-file-alt"></i>
                        </div>
                        <h4 class="fw-bold mb-3">Reports & Analytics</h4>
                        <p class="text-muted">
                            Generate comprehensive reports, analyze trends, and 
                            make data-driven decisions for institutional growth.
                        </p>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <h4 class="fw-bold mb-3">Security & Access Control</h4>
                        <p class="text-muted">
                            Role-based access control, data encryption, and 
                            secure authentication to protect sensitive information.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="stats-section">
        <div class="container">
            <div class="row">
                <div class="col-lg-3 col-md-6">
                    <div class="stat-item">
                        <span class="stat-number">10K+</span>
                        <span class="stat-label">Students Managed</span>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="stat-item">
                        <span class="stat-number">500+</span>
                        <span class="stat-label">Institutions</span>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="stat-item">
                        <span class="stat-number">99.9%</span>
                        <span class="stat-label">Uptime</span>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="stat-item">
                        <span class="stat-number">24/7</span>
                        <span class="stat-label">Support</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section id="about" class="py-5">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h2 class="section-title">About Our System</h2>
                    <p class="lead text-muted mb-4">
                        Our Student Information Management System is designed with modern educational 
                        institutions in mind, providing a comprehensive solution for all administrative needs.
                    </p>
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-check-circle text-success me-3 fs-5"></i>
                                <span>User-Friendly Interface</span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-check-circle text-success me-3 fs-5"></i>
                                <span>Cloud-Based Solution</span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-check-circle text-success me-3 fs-5"></i>
                                <span>Mobile Responsive</span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-check-circle text-success me-3 fs-5"></i>
                                <span>Regular Updates</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 text-center">
                    <div class="mt-5 mt-lg-0">
                        <i class="fas fa-laptop-code" style="font-size: 12rem; color: var(--maroon-light); opacity: 0.7;"></i>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer id="contact" class="footer">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 mb-4">
                    <h5 class="fw-bold mb-3">
                        <i class="fas fa-graduation-cap me-2"></i>
                        {{ config('app.name', 'SIMS') }}
                    </h5>
                    <p class="text-muted">
                        Empowering educational institutions with comprehensive 
                        student information management solutions.
                    </p>
                </div>
                <div class="col-lg-2 col-md-6 mb-4">
                    <h6 class="fw-bold mb-3">Quick Links</h6>
                    <ul class="list-unstyled">
                        <li><a href="#features">Features</a></li>
                        <li><a href="#about">About</a></li>
                        <li><a href="{{ route('login') }}">Login</a></li>
                        @if (Route::has('register'))
                            <li><a href="{{ route('register') }}">Register</a></li>
                        @endif
                    </ul>
                </div>
                <div class="col-lg-3 col-md-6 mb-4">
                    <h6 class="fw-bold mb-3">Support</h6>
                    <ul class="list-unstyled">
                        <li><a href="#">Documentation</a></li>
                        <li><a href="#">Help Center</a></li>
                        <li><a href="#">Contact Support</a></li>
                        <li><a href="#">System Status</a></li>
                    </ul>
                </div>
                <div class="col-lg-3 mb-4">
                    <h6 class="fw-bold mb-3">Contact Info</h6>
                    <div class="d-flex align-items-center mb-2">
                        <i class="fas fa-envelope me-3"></i>
                        <span>support@sims.edu</span>
                    </div>
                    <div class="d-flex align-items-center mb-2">
                        <i class="fas fa-phone me-3"></i>
                        <span>+1 (555) 123-4567</span>
                    </div>
                    <div class="d-flex align-items-center">
                        <i class="fas fa-map-marker-alt me-3"></i>
                        <span>123 Education St, Learning City</span>
                    </div>
                </div>
            </div>
            <hr class="my-4">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <p class="mb-0 text-muted">
                        &copy; {{ date('Y') }} {{ config('app.name', 'SIMS') }}. All rights reserved.
                    </p>
                </div>
                <div class="col-md-6 text-md-end">
                    <div class="social-links">
                        <a href="#" class="me-3"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="me-3"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="me-3"><i class="fab fa-linkedin-in"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JS -->
    <script>
        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
        
        // Navbar background on scroll
        window.addEventListener('scroll', function() {
            const navbar = document.querySelector('.navbar');
            if (window.scrollY > 50) {
                navbar.style.background = 'rgba(128, 0, 32, 0.95)';
            } else {
                navbar.style.background = 'linear-gradient(135deg, var(--maroon-primary) 0%, var(--maroon-secondary) 100%)';
            }
        });
        
        // PWA Service Worker Registration
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
                                    showUpdateNotification();
                                }
                            });
                        });
                    })
                    .catch(registrationError => {
                        console.log('SW registration failed: ', registrationError);
                    });
            });
        }
        
        // PWA Install Prompt
        let deferredPrompt;
        let installButton;
        
        window.addEventListener('beforeinstallprompt', (e) => {
            e.preventDefault();
            deferredPrompt = e;
            showInstallButton();
        });
        
        // Check if app is already installed
        window.addEventListener('appinstalled', () => {
            hideInstallButton();
        });
        
        // Check if running as PWA
        if (window.matchMedia('(display-mode: standalone)').matches) {
            // App is already installed, don't show install button
        } else {
            // Show install button after page loads
            window.addEventListener('load', () => {
                setTimeout(showInstallButton, 2000); // Show after 2 seconds
            });
        }
        
        function showInstallButton() {
            if (installButton || window.matchMedia('(display-mode: standalone)').matches) {
                return; // Button already exists or app is installed
            }
            
            installButton = document.createElement('button');
            installButton.className = 'btn btn-outline-light position-fixed';
            installButton.style.cssText = 'bottom: 20px; right: 20px; z-index: 1000; border-radius: 50px; padding: 12px 20px; box-shadow: 0 4px 12px rgba(0,0,0,0.3);';
            installButton.innerHTML = '<i class="bi bi-download me-2"></i>Install App';
            installButton.onclick = installApp;
            installButton.title = 'Install this app on your device';
            document.body.appendChild(installButton);
        }
        
        function hideInstallButton() {
            if (installButton) {
                installButton.remove();
                installButton = null;
            }
        }
        
        function installApp() {
            if (deferredPrompt) {
                // Use the deferred prompt if available
                deferredPrompt.prompt();
                deferredPrompt.userChoice.then((choiceResult) => {
                    if (choiceResult.outcome === 'accepted') {
                        console.log('User accepted the install prompt');
                        hideInstallButton();
                    }
                    deferredPrompt = null;
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
        
        function showUpdateNotification() {
            const notification = document.createElement('div');
            notification.className = 'alert alert-info alert-dismissible fade show position-fixed';
            notification.style.cssText = 'top: 20px; right: 20px; z-index: 1000; max-width: 300px;';
            notification.innerHTML = `
                <i class="bi bi-arrow-clockwise me-2"></i>
                <strong>Update Available!</strong><br>
                <small>A new version is ready. Refresh to update.</small>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                <div class="mt-2">
                    <button class="btn btn-sm btn-primary" onclick="window.location.reload()">Refresh Now</button>
                </div>
            `;
            document.body.appendChild(notification);
        }
    </script>
</body>
</html>
