@extends('layouts.admin')

@section('page-title', 'Dashboard')

@section('content')
<div class="container-fluid px-2 px-md-4">
    <!-- Welcome Section -->
    <div class="row mb-3 mb-md-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body bg-gradient p-3 p-md-4" style="background: linear-gradient(135deg, var(--maroon-primary) 0%, var(--maroon-secondary) 100%); color: white;">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h2 class="mb-2 fs-3 fs-md-2 text-black">Welcome back, {{ Auth::user()->name }}!</h2>
                            <p class="mb-0 opacity-75 fs-6 fs-md-5 text-black">{{ config('app.name') }} - Administrative Dashboard</p>
                        </div>
                        <div class="col-md-4 text-end d-none d-md-block">
                            <i class="bi bi-speedometer2" style="font-size: 3rem; opacity: 0.3;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-3 mb-md-4 g-2 g-md-3">
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-2 p-md-3">
                    <div class="row align-items-center">
                        <div class="col">
                            <div class="text-xs fw-bold text-uppercase mb-1 small" style="color: var(--maroon-primary);">Total Users</div>
                            <div class="h6 h5-md mb-0 fw-bold text-dark">{{ \App\Models\User::count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-people text-muted" style="font-size: 1.5rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-2 p-md-3">
                    <div class="row align-items-center">
                        <div class="col">
                            <div class="text-xs fw-bold text-uppercase mb-1 small" style="color: var(--maroon-primary);">Active Users</div>
                            <div class="h6 h5-md mb-0 fw-bold text-dark">{{ \App\Models\User::where('is_active', true)->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-person-check text-success" style="font-size: 1.5rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-2 p-md-3">
                    <div class="row align-items-center">
                        <div class="col">
                            <div class="text-xs fw-bold text-uppercase mb-1 small" style="color: var(--maroon-primary);">System Roles</div>
                            <div class="h6 h5-md mb-0 fw-bold text-dark">{{ \App\Models\Role::count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-shield-lock text-info" style="font-size: 1.5rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-2 p-md-3">
                    <div class="row align-items-center">
                        <div class="col">
                            <div class="text-xs fw-bold text-uppercase mb-1 small" style="color: var(--maroon-primary);">System Status</div>
                            <div class="h6 h5-md mb-0 fw-bold text-success">Online</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-check-circle text-success" style="font-size: 1.5rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mb-3 mb-md-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 p-2 p-md-3">
                    <h5 class="mb-0 fw-bold fs-6 fs-md-5" style="color: var(--maroon-primary);">Quick Actions</h5>
                </div>
                <div class="card-body p-2 p-md-3">
                    <div class="row g-2 g-md-3">
                        @can('users.manage')
                        <div class="col-12 col-md-4">
                            <a href="{{ route('admin.users.create') }}" class="btn btn-outline-primary w-100 py-2 py-md-3">
                                <i class="bi bi-person-plus mb-1 mb-md-2" style="font-size: 1.2rem;"></i>
                                <div class="fw-bold">Add New User</div>
                                <small class="text-muted d-none d-md-block">Create a new system user</small>
                            </a>
                        </div>
                        @endcan

                        @can('users.manage')
                        <div class="col-12 col-md-4">
                            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-info w-100 py-2 py-md-3">
                                <i class="bi bi-people mb-1 mb-md-2" style="font-size: 1.2rem;"></i>
                                <div class="fw-bold">Manage Users</div>
                                <small class="text-muted d-none d-md-block">View and edit users</small>
                            </a>
                        </div>
                        @endcan

                        @can('roles.manage')
                        <div class="col-12 col-md-4">
                            <a href="{{ route('admin.roles.index') }}" class="btn btn-outline-success w-100 py-2 py-md-3">
                                <i class="bi bi-shield-lock mb-1 mb-md-2" style="font-size: 1.2rem;"></i>
                                <div class="fw-bold">Manage Roles</div>
                                <small class="text-muted d-none d-md-block">Configure user roles</small>
                            </a>
                        </div>
                        @endcan
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="row g-2 g-md-3">
        <div class="col-12 col-lg-8 mb-3 mb-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 p-2 p-md-3">
                    <h5 class="mb-0 fw-bold fs-6 fs-md-5" style="color: var(--maroon-primary);">Recent Users</h5>
                </div>
                <div class="card-body p-2 p-md-3">
                    <div class="table-responsive">
                        <table class="table table-hover table-sm">
                            <thead class="table-light">
                                <tr>
                                    <th class="d-none d-md-table-cell">Name</th>
                                    <th class="d-md-none">User</th>
                                    <th class="d-none d-sm-table-cell">Email</th>
                                    <th class="d-none d-lg-table-cell">Role</th>
                                    <th>Status</th>
                                    <th class="d-none d-lg-table-cell">Joined</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach(\App\Models\User::with('primaryRole')->latest()->take(5)->get() as $user)
                                <tr>
                                    <td class="fw-semibold">
                                        <div class="d-md-none">
                                            <div>{{ $user->name }}</div>
                                            <small class="text-muted d-sm-none">{{ $user->email }}</small>
                                        </div>
                                        <div class="d-none d-md-block">{{ $user->name }}</div>
                                    </td>
                                    <td class="d-none d-sm-table-cell">{{ $user->email }}</td>
                                    <td class="d-none d-lg-table-cell">
                                        <span class="badge bg-secondary">{{ $user->primaryRole->display_name ?? 'No Role' }}</span>
                                    </td>
                                    <td>
                                        @if($user->is_active)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-danger">Inactive</span>
                                        @endif
                                    </td>
                                    <td class="text-muted d-none d-lg-table-cell">{{ $user->created_at->format('M d, Y') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-4 mb-3 mb-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 p-2 p-md-3">
                    <h5 class="mb-0 fw-bold fs-6 fs-md-5" style="color: var(--maroon-primary);">System Information</h5>
                </div>
                <div class="card-body p-2 p-md-3">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted small">System Version</span>
                            <span class="fw-bold small">1.0.0</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted small">Laravel Version</span>
                            <span class="fw-bold small">{{ app()->version() }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted small">PHP Version</span>
                            <span class="fw-bold small">{{ PHP_VERSION }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted small">Database</span>
                            <span class="fw-bold small">{{ config('database.default') }}</span>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="text-center">
                        <div class="mb-2">
                            <i class="bi bi-shield-check text-success" style="font-size: 1.5rem;"></i>
                        </div>
                        <div class="fw-bold text-success small">System Operational</div>
                        <small class="text-muted">All services running normally</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
