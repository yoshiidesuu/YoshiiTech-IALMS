<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Student Information Management System - Admin Dashboard">
    <meta name="theme-color" content="#800020">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="SIMS Admin">
    <meta name="msapplication-TileColor" content="#800020">

    <title>{{ $title ?? 'Admin Dashboard' }} - {{ config('app.system_title', config('app.name', 'SIMS')) }}</title>
    
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

    <!-- Scripts -->
    @vite(['resources/css/app.scss', 'resources/js/app.js'])
    
    <!-- Dynamic Theme CSS -->
    @include('theme.dynamic')
    
    <!-- Global Theme Switcher -->
    @include('components.theme-switcher')
</head>
<body class="bg-light">
    <!-- Mobile Header -->
    <nav class="navbar navbar-expand-lg top-navbar d-lg-none">
        <div class="container-fluid">
            <a class="navbar-brand text-white" href="{{ route('dashboard') }}">
                @if(config('app.logo_path'))
                    <img src="{{ config('app.logo_path') }}" alt="{{ config('app.institution_name', 'Institution') }}" style="height: 30px;" class="me-2">
                @endif
                {{ config('app.institution_name', config('app.name', 'SIMS')) }}
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarOffcanvas" aria-controls="sidebarOffcanvas">
                <span class="navbar-toggler-icon"></span>
            </button>
        </div>
    </nav>

    <div class="d-flex">
        <!-- Desktop Sidebar -->
        <nav class="sidebar text-white d-none d-lg-block position-fixed" style="width: 250px; height: 100vh; top: 0; left: 0; z-index: 1000;">
            <div class="p-3 h-100 d-flex flex-column">
                <div class="d-flex align-items-center mb-4">
                    @if(config('app.logo_path'))
                        <img src="{{ config('app.logo_path') }}" alt="{{ config('app.institution_name', 'Institution') }}" style="height: 40px;" class="me-3">
                    @endif
                    <div>
                        <h5 class="text-white mb-0">{{ config('app.institution_name', config('app.name', 'SIMS')) }}</h5>
                        <small class="text-white-50">{{ config('app.system_title', 'Management System') }}</small>
                    </div>
                </div>
                <ul class="nav flex-column flex-grow-1 pb-3">
                    <li class="nav-item mb-2">
                        <a href="{{ route('dashboard') }}" class="nav-link text-white-50 hover-text-white">
                            <i class="bi bi-speedometer2 me-2"></i> Dashboard
                        </a>
                    </li>
                    
                    @can('users.manage')
                    <li class="nav-item mb-2">
                        <a href="{{ route('admin.users.index') }}" class="nav-link text-white-50 hover-text-white {{ request()->routeIs('admin.users.*') ? 'text-white bg-secondary rounded' : '' }}">
                            <i class="bi bi-people me-2"></i> User Management
                        </a>
                    </li>
                    @endcan
                    
                    @can('roles.manage')
                    <li class="nav-item mb-2">
                        <a href="{{ route('admin.roles.index') }}" class="nav-link text-white-50 hover-text-white {{ request()->routeIs('admin.roles.*') ? 'text-white bg-secondary rounded' : '' }}">
                            <i class="bi bi-shield-lock me-2"></i> Role Management
                        </a>
                    </li>
                    @endcan
                    
                    @can('permissions.manage')
                    <li class="nav-item mb-2">
                        <a href="{{ route('admin.permissions.index') }}" class="nav-link text-white-50 hover-text-white {{ request()->routeIs('admin.permissions.*') ? 'text-white bg-secondary rounded' : '' }}">
                            <i class="bi bi-key me-2"></i> Permission Management
                        </a>
                    </li>
                    @endcan
                    
                    @can('configurations.manage')
                    <li class="nav-item mb-2">
                        <a href="{{ route('admin.configurations.index') }}" class="nav-link text-white-50 hover-text-white {{ request()->routeIs('admin.configurations.*') ? 'text-white bg-secondary rounded' : '' }}">
                            <i class="bi bi-gear me-2"></i> Configuration Management
                        </a>
                    </li>
                    @endcan
                    
                    @can('configurations.manage')
                    <li class="nav-item mb-2">
                        <a href="{{ route('admin.theme.index') }}" class="nav-link text-white-50 hover-text-white {{ request()->routeIs('admin.theme.*') ? 'text-white bg-secondary rounded' : '' }}">
                            <i class="bi bi-palette me-2"></i> Theme Management
                        </a>
                    </li>
                    @endcan
                    
                    @can('configurations.manage')
                    <li class="nav-item mb-2">
                        <a href="{{ route('admin.branding.index') }}" class="nav-link text-white-50 hover-text-white {{ request()->routeIs('admin.branding.*') ? 'text-white bg-secondary rounded' : '' }}">
                            <i class="bi bi-image me-2"></i> Branding Management
                        </a>
                    </li>
                    @endcan
                    
                    @can('configurations.manage')
                    <li class="nav-item mb-2">
                        <a href="{{ route('admin.smtp.index') }}" class="nav-link text-white-50 hover-text-white {{ request()->routeIs('admin.smtp.*') ? 'text-white bg-secondary rounded' : '' }}">
                            <i class="bi bi-envelope-gear me-2"></i> SMTP Configuration
                        </a>
                    </li>
                    @endcan
                    
                    @can('configurations.manage')
                    <li class="nav-item mb-2">
                        <a href="{{ route('admin.two-factor.index') }}" class="nav-link text-white-50 hover-text-white {{ request()->routeIs('admin.two-factor.*') ? 'text-white bg-secondary rounded' : '' }}">
                            <i class="bi bi-shield-lock me-2"></i> Two-Factor Authentication
                        </a>
                    </li>
                    @endcan
                    
                    @can('configurations.manage')
                    <li class="nav-item mb-2">
                        <a href="{{ route('admin.email-templates.index') }}" class="nav-link text-white-50 hover-text-white {{ request()->routeIs('admin.email-templates.*') ? 'text-white bg-secondary rounded' : '' }}">
                            <i class="bi bi-envelope-paper me-2"></i> Email Templates
                        </a>
                    </li>
                    @endcan
                    
                    @can('configurations.manage')
                     <li class="nav-item mb-2">
                         <a href="{{ route('admin.maintenance.index') }}" class="nav-link text-white-50 hover-text-white {{ request()->routeIs('admin.maintenance.*') ? 'text-white bg-secondary rounded' : '' }}">
                             <i class="bi bi-tools me-2"></i> Maintenance Mode
                         </a>
                     </li>
                     @endcan
                     
                     @can('configurations.manage')
                     <li class="nav-item mb-2">
                         <a href="{{ route('admin.file-security.index') }}" class="nav-link text-white-50 hover-text-white {{ request()->routeIs('admin.file-security.*') ? 'text-white bg-secondary rounded' : '' }}">
                             <i class="bi bi-shield-check me-2"></i> File Security
                         </a>
                     </li>
                     @endcan
                    
                    <hr class="text-white-50">
                    
                    <!-- Academic Management Section -->
                    <li class="nav-item mb-1">
                        <small class="text-white-50 text-uppercase fw-bold px-3">Academic Management</small>
                    </li>
                    
                    @can('academic.manage')
                    <li class="nav-item mb-2">
                        <a href="{{ route('admin.academic-years.index') }}" class="nav-link text-white-50 hover-text-white {{ request()->routeIs('admin.academic-years.*') ? 'text-white bg-secondary rounded' : '' }}">
                            <i class="bi bi-calendar-range me-2"></i> Academic Years
                        </a>
                    </li>
                    @endcan
                    
                    @can('academic.manage')
                    <li class="nav-item mb-2">
                        <a href="{{ route('admin.semesters.index') }}" class="nav-link text-white-50 hover-text-white {{ request()->routeIs('admin.semesters.*') ? 'text-white bg-secondary rounded' : '' }}">
                            <i class="bi bi-calendar3 me-2"></i> Semesters
                        </a>
                    </li>
                    @endcan
                    
                    @can('academic.manage')
                    <li class="nav-item mb-2">
                        <a href="{{ route('admin.subjects.index') }}" class="nav-link text-white-50 hover-text-white {{ request()->routeIs('admin.subjects.*') ? 'text-white bg-secondary rounded' : '' }}">
                            <i class="bi bi-book me-2"></i> Subjects
                        </a>
                    </li>
                    @endcan
                    
                    @can('policies.manage')
                    <li class="nav-item mb-2">
                        <a href="{{ route('admin.policies.index') }}" class="nav-link text-white-50 hover-text-white {{ request()->routeIs('admin.policies.*') ? 'text-white bg-secondary rounded' : '' }}">
                            <i class="bi bi-file-text me-2"></i> Policies
                        </a>
                    </li>
                    @endcan
                    
                    @can('grades.manage')
                    <li class="nav-item mb-2">
                        <a href="{{ route('admin.grade-encoding-periods.index') }}" class="nav-link text-white-50 hover-text-white {{ request()->routeIs('admin.grade-encoding-periods.*') ? 'text-white bg-secondary rounded' : '' }}">
                            <i class="bi bi-clipboard-data me-2"></i> Grade Encoding Periods
                        </a>
                    </li>
                    @endcan
                    
                    <hr class="text-white-50">
                     
                     <!-- PWA Install Button -->
                     <li class="nav-item mb-2">
                         <button id="pwa-install-btn-mobile" class="nav-link text-white-50 hover-text-white border-0 bg-transparent w-100 text-start d-none" onclick="installPWA()">
                             <i class="bi bi-download me-2"></i> Install App
                         </button>
                     </li>
                    
                    <li class="nav-item">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="nav-link text-white-50 hover-text-white border-0 bg-transparent w-100 text-start">
                                <i class="bi bi-box-arrow-right me-2"></i> Logout
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </nav>

        <!-- Mobile Offcanvas Sidebar -->
        <div class="offcanvas offcanvas-start sidebar text-white d-lg-none" tabindex="-1" id="sidebarOffcanvas" aria-labelledby="sidebarOffcanvasLabel">
            <div class="offcanvas-header">
                <div class="d-flex align-items-center">
                    @if(config('app.logo_path'))
                        <img src="{{ config('app.logo_path') }}" alt="{{ config('app.institution_name', 'Institution') }}" style="height: 30px;" class="me-2">
                    @endif
                    <div>
                        <h6 class="text-white mb-0" id="sidebarOffcanvasLabel">{{ config('app.institution_name', config('app.name', 'SIMS')) }}</h6>
                        <small class="text-white-50">{{ config('app.system_title', 'Management System') }}</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body" style="overflow-y: auto;">
                <ul class="nav flex-column">
                    <li class="nav-item mb-2">
                        <a href="{{ route('dashboard') }}" class="nav-link text-white-50 hover-text-white">
                            <i class="bi bi-speedometer2 me-2"></i> Dashboard
                        </a>
                    </li>
                    
                    @can('users.manage')
                    <li class="nav-item mb-2">
                        <a href="{{ route('admin.users.index') }}" class="nav-link text-white-50 hover-text-white {{ request()->routeIs('admin.users.*') ? 'text-white bg-secondary rounded' : '' }}">
                            <i class="bi bi-people me-2"></i> User Management
                        </a>
                    </li>
                    @endcan
                    
                    @can('roles.manage')
                    <li class="nav-item mb-2">
                        <a href="{{ route('admin.roles.index') }}" class="nav-link text-white-50 hover-text-white {{ request()->routeIs('admin.roles.*') ? 'text-white bg-secondary rounded' : '' }}">
                            <i class="bi bi-shield-lock me-2"></i> Role Management
                        </a>
                    </li>
                    @endcan
                    
                    @can('permissions.manage')
                    <li class="nav-item mb-2">
                        <a href="{{ route('admin.permissions.index') }}" class="nav-link text-white-50 hover-text-white {{ request()->routeIs('admin.permissions.*') ? 'text-white bg-secondary rounded' : '' }}">
                            <i class="bi bi-key me-2"></i> Permission Management
                        </a>
                    </li>
                    @endcan
                    
                    @can('configurations.manage')
                    <li class="nav-item mb-2">
                        <a href="{{ route('admin.configurations.index') }}" class="nav-link text-white-50 hover-text-white {{ request()->routeIs('admin.configurations.*') ? 'text-white bg-secondary rounded' : '' }}">
                            <i class="bi bi-gear me-2"></i> Configuration Management
                        </a>
                    </li>
                    @endcan
                    
                    @can('configurations.manage')
                    <li class="nav-item mb-2">
                        <a href="{{ route('admin.theme.index') }}" class="nav-link text-white-50 hover-text-white {{ request()->routeIs('admin.theme.*') ? 'text-white bg-secondary rounded' : '' }}">
                            <i class="bi bi-palette me-2"></i> Theme Management
                        </a>
                    </li>
                    @endcan
                    
                    @can('configurations.manage')
                    <li class="nav-item mb-2">
                        <a href="{{ route('admin.branding.index') }}" class="nav-link text-white-50 hover-text-white {{ request()->routeIs('admin.branding.*') ? 'text-white bg-secondary rounded' : '' }}">
                            <i class="bi bi-image me-2"></i> Branding Management
                        </a>
                    </li>
                    @endcan
                    
                    @can('configurations.manage')
                    <li class="nav-item mb-2">
                        <a href="{{ route('admin.smtp.index') }}" class="nav-link text-white-50 hover-text-white {{ request()->routeIs('admin.smtp.*') ? 'text-white bg-secondary rounded' : '' }}">
                            <i class="bi bi-envelope-gear me-2"></i> SMTP Configuration
                        </a>
                    </li>
                    @endcan
                    
                    @can('configurations.manage')
                    <li class="nav-item mb-2">
                        <a href="{{ route('admin.two-factor.index') }}" class="nav-link text-white-50 hover-text-white {{ request()->routeIs('admin.two-factor.*') ? 'text-white bg-secondary rounded' : '' }}">
                            <i class="bi bi-shield-lock me-2"></i> Two-Factor Authentication
                        </a>
                    </li>
                    @endcan
                    
                    @can('configurations.manage')
                    <li class="nav-item mb-2">
                        <a href="{{ route('admin.email-templates.index') }}" class="nav-link text-white-50 hover-text-white {{ request()->routeIs('admin.email-templates.*') ? 'text-white bg-secondary rounded' : '' }}">
                            <i class="bi bi-envelope-paper me-2"></i> Email Templates
                        </a>
                    </li>
                    @endcan
                    
                    @can('configurations.manage')
                     <li class="nav-item mb-2">
                         <a href="{{ route('admin.maintenance.index') }}" class="nav-link text-white-50 hover-text-white {{ request()->routeIs('admin.maintenance.*') ? 'text-white bg-secondary rounded' : '' }}">
                             <i class="bi bi-tools me-2"></i> Maintenance Mode
                         </a>
                     </li>
                     @endcan
                     
                     @can('configurations.manage')
                     <li class="nav-item mb-2">
                         <a href="{{ route('admin.file-security.index') }}" class="nav-link text-white-50 hover-text-white {{ request()->routeIs('admin.file-security.*') ? 'text-white bg-secondary rounded' : '' }}">
                             <i class="bi bi-shield-check me-2"></i> File Security
                         </a>
                     </li>
                     @endcan
                     
                     <hr class="text-white-50">
                     
                     <!-- Academic Management Section -->
                     <li class="nav-item mb-1">
                         <small class="text-white-50 text-uppercase fw-bold px-3">Academic Management</small>
                     </li>
                     
                     @can('academic.manage')
                     <li class="nav-item mb-2">
                         <a href="{{ route('admin.academic-years.index') }}" class="nav-link text-white-50 hover-text-white {{ request()->routeIs('admin.academic-years.*') ? 'text-white bg-secondary rounded' : '' }}">
                             <i class="bi bi-calendar-range me-2"></i> Academic Years
                         </a>
                     </li>
                     @endcan
                     
                     @can('academic.manage')
                     <li class="nav-item mb-2">
                         <a href="{{ route('admin.semesters.index') }}" class="nav-link text-white-50 hover-text-white {{ request()->routeIs('admin.semesters.*') ? 'text-white bg-secondary rounded' : '' }}">
                             <i class="bi bi-calendar3 me-2"></i> Semesters
                         </a>
                     </li>
                     @endcan
                     
                     @can('academic.manage')
                     <li class="nav-item mb-2">
                         <a href="{{ route('admin.subjects.index') }}" class="nav-link text-white-50 hover-text-white {{ request()->routeIs('admin.subjects.*') ? 'text-white bg-secondary rounded' : '' }}">
                             <i class="bi bi-book me-2"></i> Subjects
                         </a>
                     </li>
                     @endcan
                     
                     @can('policies.manage')
                     <li class="nav-item mb-2">
                         <a href="{{ route('admin.policies.index') }}" class="nav-link text-white-50 hover-text-white {{ request()->routeIs('admin.policies.*') ? 'text-white bg-secondary rounded' : '' }}">
                             <i class="bi bi-file-text me-2"></i> Policies
                         </a>
                     </li>
                     @endcan
                     
                     @can('grades.manage')
                     <li class="nav-item mb-2">
                         <a href="{{ route('admin.grade-encoding-periods.index') }}" class="nav-link text-white-50 hover-text-white {{ request()->routeIs('admin.grade-encoding-periods.*') ? 'text-white bg-secondary rounded' : '' }}">
                             <i class="bi bi-clipboard-data me-2"></i> Grade Encoding Periods
                         </a>
                     </li>
                     @endcan
                     
                     <hr class="text-white-50">
                     
                     <!-- PWA Install Button -->
                     <li class="nav-item mb-2">
                         <button id="pwa-install-btn" class="nav-link text-white-50 hover-text-white border-0 bg-transparent w-100 text-start d-none" onclick="installPWA()">
                             <i class="bi bi-download me-2"></i> Install App
                         </button>
                     </li>
                    
                    <li class="nav-item">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="nav-link text-white-50 hover-text-white border-0 bg-transparent w-100 text-start">
                                <i class="bi bi-box-arrow-right me-2"></i> Logout
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex-grow-1 w-100" style="margin-left: 250px;">
            <!-- Desktop Top Navigation -->
            <nav class="navbar navbar-expand-lg top-navbar d-none d-lg-block">
                <div class="container-fluid">
                    <span class="navbar-brand text-white mb-0 h1">@yield('page-title', 'Admin Panel')</span>
                    
                    <div class="navbar-nav ms-auto">
                        <div class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle text-white" href="#" role="button" data-bs-toggle="dropdown">
                                {{ Auth::user()->name }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="{{ route('profile.show') }}">Profile</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item">Logout</button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Mobile Top Bar -->
            <div class="bg-light border-bottom p-3 d-lg-none">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">@yield('page-title', 'Admin Panel')</h6>
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="{{ route('profile.show') }}">Profile</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item">Logout</button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Page Content -->
            <main class="p-4 main-content">
                <!-- Alerts -->
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

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
        }
        
        .sidebar {
            background: linear-gradient(180deg, var(--maroon-dark) 0%, #2c3e50 100%) !important;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
            overflow-y: auto;
            overflow-x: hidden;
            scrollbar-width: thin;
            scrollbar-color: rgba(255,255,255,0.3) transparent;
        }
        
        .sidebar::-webkit-scrollbar {
            width: 6px;
        }
        
        .sidebar::-webkit-scrollbar-track {
            background: transparent;
        }
        
        .sidebar::-webkit-scrollbar-thumb {
            background-color: rgba(255,255,255,0.3);
            border-radius: 3px;
        }
        
        .sidebar::-webkit-scrollbar-thumb:hover {
            background-color: rgba(255,255,255,0.5);
        }
        
        .sidebar .nav-link {
            transition: all 0.3s ease;
            border-radius: 8px;
            margin: 2px 0;
        }
        
        .sidebar .nav-link:hover {
            background-color: rgba(255,255,255,0.1) !important;
            color: white !important;
            transform: translateX(5px);
        }
        
        .sidebar .nav-link.active {
            background-color: var(--maroon-primary) !important;
            color: white !important;
            box-shadow: 0 4px 8px rgba(128, 0, 32, 0.3);
        }
        
        .top-navbar {
            background: linear-gradient(90deg, var(--maroon-primary) 0%, var(--maroon-secondary) 100%) !important;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .main-content {
            background-color: #f8f9fa;
        }
        
        .card {
            border: none;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            border-radius: 12px;
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
        
        .alert {
            border-radius: 10px;
            border: none;
        }
        
        /* Ensure proper text contrast */
        .text-gray-800 {
            color: #343a40 !important;
        }
        
        .card-body {
            color: #212529;
        }
        
        .table {
            color: #212529;
        }
        
        /* Fix any potential white text issues */
        .text-white {
            color: #ffffff !important;
        }
        
        .text-dark {
            color: #212529 !important;
        }
        
        /* Responsive Design */
        @media (max-width: 991.98px) {
            .flex-grow-1.w-100 {
                margin-left: 0 !important;
            }
        }
        
        @media (min-width: 992px) {
            .main-content {
                padding-left: 0;
            }
        }
        
        /* Smooth transitions */
        .sidebar .nav-link {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        /* Section headers styling */
        .sidebar small.text-uppercase {
            font-size: 0.7rem;
            letter-spacing: 0.5px;
            margin-top: 0.5rem;
            opacity: 0.8;
        }
        
        /* Improved hover effects */
        .sidebar .nav-link:hover {
            background-color: rgba(255,255,255,0.15) !important;
            transform: translateX(3px);
            box-shadow: 0 2px 8px rgba(0,0,0,0.2);
        }
        
        /* Active state improvements */
        .sidebar .nav-link.active,
        .sidebar .nav-link.text-white.bg-secondary.rounded {
            background-color: var(--maroon-primary) !important;
            color: white !important;
            box-shadow: 0 4px 12px rgba(128, 0, 32, 0.4);
            transform: translateX(3px);
        }
        
        /* Mobile offcanvas improvements */
        .offcanvas.sidebar {
            width: 280px !important;
        }
        
        /* Ensure proper spacing */
        .sidebar .nav-item:last-child {
            margin-bottom: 0;
        }
    </style>

    <!-- Bootstrap JS is included via Vite build -->
    
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
        
        function showUpdateNotification() {
            const notification = document.createElement('div');
            notification.className = 'alert alert-info alert-dismissible fade show position-fixed';
            notification.style.cssText = 'top: 80px; right: 20px; z-index: 1050; max-width: 300px;';
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
        
        // PWA Installation
        let deferredPrompt;
        const installButtons = document.querySelectorAll('#pwa-install-btn, #pwa-install-btn-mobile');
        
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
        
        // Check if app is already installed
        window.addEventListener('appinstalled', () => {
            installButtons.forEach(btn => {
                if (btn) btn.classList.add('d-none');
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