@extends('layouts.admin')

@section('title', 'Role Details')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-user-tag"></i> Role Details: {{ $role->display_name }}
                        @if($role->is_system)
                            <span class="badge bg-warning ms-2">
                                <i class="fas fa-lock"></i> System Role
                            </span>
                        @endif
                    </h5>
                    <div class="btn-group" role="group">
                        @can('update', $role)
                            <a href="{{ route('admin.roles.edit', $role) }}" class="btn btn-light btn-sm">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                        @endcan
                        <a href="{{ route('admin.roles.index') }}" class="btn btn-light btn-sm">
                            <i class="fas fa-arrow-left"></i> Back to Roles
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Basic Information -->
                        <div class="col-md-6">
                            <div class="card h-100">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0"><i class="fas fa-info-circle"></i> Basic Information</h6>
                                </div>
                                <div class="card-body">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>Role Name:</strong></td>
                                            <td><code>{{ $role->name }}</code></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Display Name:</strong></td>
                                            <td>{{ $role->display_name }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Description:</strong></td>
                                            <td>{{ $role->description ?: 'No description provided' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Status:</strong></td>
                                            <td>
                                                @if($role->is_active)
                                                    <span class="badge bg-success">
                                                        <i class="fas fa-check-circle"></i> Active
                                                    </span>
                                                @else
                                                    <span class="badge bg-danger">
                                                        <i class="fas fa-times-circle"></i> Inactive
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>System Role:</strong></td>
                                            <td>
                                                @if($role->is_system)
                                                    <span class="badge bg-warning">
                                                        <i class="fas fa-lock"></i> Yes
                                                    </span>
                                                @else
                                                    <span class="badge bg-secondary">
                                                        <i class="fas fa-unlock"></i> No
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Users Assigned:</strong></td>
                                            <td>
                                                <span class="badge bg-primary">{{ $role->users_count ?? 0 }}</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Created:</strong></td>
                                            <td>{{ $role->created_at->format('M d, Y \\a\\t g:i A') }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Last Updated:</strong></td>
                                            <td>{{ $role->updated_at->format('M d, Y \\a\\t g:i A') }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Permissions -->
                        <div class="col-md-6">
                            <div class="card h-100">
                                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0">
                                        <i class="fas fa-key"></i> Permissions 
                                        <span class="badge bg-primary ms-1">{{ $role->permissions->count() }}</span>
                                    </h6>
                                </div>
                                <div class="card-body">
                                    @if($role->permissions->count() > 0)
                                        <div class="permissions-container" style="max-height: 400px; overflow-y: auto;">
                                            @foreach($role->permissions->groupBy('module') as $module => $modulePermissions)
                                                <div class="mb-3">
                                                    <h6 class="text-primary border-bottom pb-1">
                                                        <i class="fas fa-folder"></i> {{ ucfirst($module) }} Module
                                                        <span class="badge bg-primary ms-1">{{ $modulePermissions->count() }}</span>
                                                    </h6>
                                                    <div class="row">
                                                        @foreach($modulePermissions as $permission)
                                                            <div class="col-12 mb-1">
                                                                <div class="d-flex align-items-center">
                                                                    <i class="fas fa-check-circle text-success me-2"></i>
                                                                    <div>
                                                                        <strong>{{ $permission->display_name }}</strong>
                                                                        @if($permission->description)
                                                                            <br><small class="text-muted">{{ $permission->description }}</small>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="text-center text-muted py-4">
                                            <i class="fas fa-key fa-3x mb-3 opacity-50"></i>
                                            <p class="mb-0">No permissions assigned to this role.</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    @if($role->users->count() > 0)
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0">
                                            <i class="fas fa-users"></i> Assigned Users 
                                            <span class="badge bg-primary ms-1">{{ $role->users->count() }}</span>
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-hover">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>Name</th>
                                                        <th>Email</th>
                                                        <th>Status</th>
                                                        <th>Last Login</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($role->users->take(10) as $user)
                                                        <tr>
                                                            <td>
                                                                <div class="d-flex align-items-center">
                                                                    <img src="{{ $user->profile_photo_url }}" 
                                                                         alt="{{ $user->name }}" 
                                                                         class="rounded-circle me-2" 
                                                                         width="32" height="32">
                                                                    {{ $user->name }}
                                                                </div>
                                                            </td>
                                                            <td>{{ $user->email }}</td>
                                                            <td>
                                                                @if($user->is_active)
                                                                    <span class="badge bg-success">Active</span>
                                                                @else
                                                                    <span class="badge bg-danger">Inactive</span>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                @if($user->last_login_at)
                                                                    {{ $user->last_login_at->diffForHumans() }}
                                                                @else
                                                                    <span class="text-muted">Never</span>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                @can('view', $user)
                                                                    <a href="{{ route('admin.users.show', $user) }}" 
                                                                       class="btn btn-sm btn-outline-primary">
                                                                        <i class="fas fa-eye"></i>
                                                                    </a>
                                                                @endcan
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                        @if($role->users->count() > 10)
                                            <div class="text-center mt-3">
                                                <p class="text-muted">Showing 10 of {{ $role->users->count() }} users.</p>
                                                <a href="{{ route('admin.users.index', ['role' => $role->name]) }}" 
                                                   class="btn btn-outline-primary btn-sm">
                                                    View All Users with This Role
                                                </a>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                    
                    <!-- Action Buttons -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="d-flex justify-content-end gap-2">
                                @can('update', $role)
                                    <a href="{{ route('admin.roles.edit', $role) }}" class="btn btn-primary">
                                        <i class="fas fa-edit"></i> Edit Role
                                    </a>
                                @endcan
                                
                                @can('update', $role)
                                    @if(!$role->is_system)
                                        <form action="{{ route('admin.roles.toggle-status', $role) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-{{ $role->is_active ? 'warning' : 'success' }}">
                                                <i class="fas fa-{{ $role->is_active ? 'pause' : 'play' }}"></i>
                                                {{ $role->is_active ? 'Deactivate' : 'Activate' }}
                                            </button>
                                        </form>
                                    @endif
                                @endcan
                                
                                @can('delete', $role)
                                    @if(!$role->is_system && $role->users_count == 0)
                                        <form action="{{ route('admin.roles.destroy', $role) }}" method="POST" 
                                              class="d-inline" 
                                              onsubmit="return confirm('Are you sure you want to delete this role? This action cannot be undone.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger">
                                                <i class="fas fa-trash"></i> Delete Role
                                            </button>
                                        </form>
                                    @endif
                                @endcan
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection