@extends('layouts.admin')

@section('title', 'Permission Details')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-key"></i> Permission Details: {{ $permission->display_name }}
                        </h5>
                        <div>
                            @can('update', $permission)
                                <a href="{{ route('admin.permissions.edit', $permission) }}" class="btn btn-light btn-sm me-2">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                            @endcan
                            <a href="{{ route('admin.permissions.index') }}" class="btn btn-light btn-sm">
                                <i class="fas fa-arrow-left"></i> Back to Permissions
                            </a>
                        </div>
                    </div>
                </div>
                
                <div class="card-body">
                    <div class="row">
                        <!-- Basic Information -->
                        <div class="col-md-6">
                            <div class="card h-100">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">
                                        <i class="fas fa-info-circle"></i> Basic Information
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label class="form-label text-muted">Permission Name</label>
                                        <div>
                                            <code class="fs-6">{{ $permission->name }}</code>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label text-muted">Display Name</label>
                                        <div class="fw-bold">{{ $permission->display_name }}</div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label text-muted">Module</label>
                                        <div>
                                            <span class="badge bg-secondary fs-6">
                                                {{ ucfirst($permission->module) }}
                                            </span>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label text-muted">Status</label>
                                        <div>
                                            @if($permission->is_active)
                                                <span class="badge bg-success fs-6">
                                                    <i class="fas fa-check-circle"></i> Active
                                                </span>
                                            @else
                                                <span class="badge bg-danger fs-6">
                                                    <i class="fas fa-times-circle"></i> Inactive
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label text-muted">Description</label>
                                        <div class="text-muted">
                                            {{ $permission->description ?: 'No description provided' }}
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-6">
                                            <label class="form-label text-muted">Created</label>
                                            <div class="small">
                                                {{ $permission->created_at->format('M d, Y') }}<br>
                                                <span class="text-muted">{{ $permission->created_at->format('h:i A') }}</span>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <label class="form-label text-muted">Last Updated</label>
                                            <div class="small">
                                                {{ $permission->updated_at->format('M d, Y') }}<br>
                                                <span class="text-muted">{{ $permission->updated_at->format('h:i A') }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Assigned Roles -->
                        <div class="col-md-6">
                            <div class="card h-100">
                                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0">
                                        <i class="fas fa-users-cog"></i> Assigned Roles
                                        <span class="badge bg-primary ms-2">{{ $permission->roles->count() }}</span>
                                    </h6>
                                </div>
                                <div class="card-body">
                                    @if($permission->roles->count() > 0)
                                        <div class="list-group list-group-flush" style="max-height: 300px; overflow-y: auto;">
                                            @foreach($permission->roles as $role)
                                                <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                                                    <div>
                                                        <div class="fw-bold">{{ $role->display_name }}</div>
                                                        <small class="text-muted">
                                                            <code>{{ $role->name }}</code>
                                                            @if($role->is_system)
                                                                <span class="badge bg-warning ms-1">System</span>
                                                            @endif
                                                        </small>
                                                    </div>
                                                    <div>
                                                        @if($role->is_active)
                                                            <span class="badge bg-success">
                                                                <i class="fas fa-check-circle"></i>
                                                            </span>
                                                        @else
                                                            <span class="badge bg-danger">
                                                                <i class="fas fa-times-circle"></i>
                                                            </span>
                                                        @endif
                                                        @can('view', $role)
                                                            <a href="{{ route('admin.roles.show', $role) }}" 
                                                               class="btn btn-sm btn-outline-primary ms-2">
                                                                <i class="fas fa-eye"></i>
                                                            </a>
                                                        @endcan
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                        
                                        @if($permission->roles->count() > 5)
                                            <div class="mt-3 text-center">
                                                <a href="{{ route('admin.roles.index', ['permission' => $permission->name]) }}" 
                                                   class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-list"></i> View All Roles with This Permission
                                                </a>
                                            </div>
                                        @endif
                                    @else
                                        <div class="text-center py-4">
                                            <i class="fas fa-users-slash fa-2x text-muted mb-2"></i>
                                            <p class="text-muted mb-0">No roles assigned</p>
                                            <small class="text-muted">
                                                This permission is not currently assigned to any roles
                                            </small>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Users with This Permission (through roles) -->
                    @if($permission->roles->count() > 0)
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                        <h6 class="mb-0">
                                            <i class="fas fa-users"></i> Users with This Permission
                                            @php
                                                $totalUsers = $permission->roles->sum(function($role) {
                                                    return $role->users->count();
                                                });
                                            @endphp
                                            <span class="badge bg-info ms-2">{{ $totalUsers }}</span>
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        @if($totalUsers > 0)
                                            <div class="row">
                                                @foreach($permission->roles as $role)
                                                    @if($role->users->count() > 0)
                                                        <div class="col-md-6 mb-3">
                                                            <h6 class="text-primary">
                                                                {{ $role->display_name }} 
                                                                <span class="badge bg-primary">{{ $role->users->count() }}</span>
                                                            </h6>
                                                            <div class="list-group list-group-flush" style="max-height: 200px; overflow-y: auto;">
                                                                @foreach($role->users->take(10) as $user)
                                                                    <div class="list-group-item d-flex justify-content-between align-items-center px-0 py-2">
                                                                        <div class="d-flex align-items-center">
                                                                            <div class="avatar-sm me-2">
                                                                                @if($user->avatar)
                                                                                    <img src="{{ asset('storage/' . $user->avatar) }}" 
                                                                                         class="rounded-circle" width="32" height="32" 
                                                                                         alt="{{ $user->name }}">
                                                                                @else
                                                                                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" 
                                                                                         style="width: 32px; height: 32px; font-size: 14px;">
                                                                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                                                                    </div>
                                                                                @endif
                                                                            </div>
                                                                            <div>
                                                                                <div class="fw-bold">{{ $user->name }}</div>
                                                                                <small class="text-muted">{{ $user->email }}</small>
                                                                            </div>
                                                                        </div>
                                                                        <div>
                                                                            @if($user->is_active)
                                                                                <span class="badge bg-success">
                                                                                    <i class="fas fa-check-circle"></i>
                                                                                </span>
                                                                            @else
                                                                                <span class="badge bg-danger">
                                                                                    <i class="fas fa-times-circle"></i>
                                                                                </span>
                                                                            @endif
                                                                            @can('view', $user)
                                                                                <a href="{{ route('admin.users.show', $user) }}" 
                                                                                   class="btn btn-sm btn-outline-primary ms-1">
                                                                                    <i class="fas fa-eye"></i>
                                                                                </a>
                                                                            @endcan
                                                                        </div>
                                                                    </div>
                                                                @endforeach
                                                                @if($role->users->count() > 10)
                                                                    <div class="list-group-item px-0 py-2 text-center">
                                                                        <small class="text-muted">
                                                                            ... and {{ $role->users->count() - 10 }} more users
                                                                        </small>
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    @endif
                                                @endforeach
                                            </div>
                                        @else
                                            <div class="text-center py-4">
                                                <i class="fas fa-user-slash fa-2x text-muted mb-2"></i>
                                                <p class="text-muted mb-0">No users found</p>
                                                <small class="text-muted">
                                                    No users are currently assigned to roles with this permission
                                                </small>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
                
                <!-- Action Buttons -->
                <div class="card-footer">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <a href="{{ route('admin.permissions.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back to Permissions
                            </a>
                        </div>
                        <div>
                            @can('update', $permission)
                                <form action="{{ route('admin.permissions.toggle-status', $permission) }}" 
                                      method="POST" class="d-inline me-2">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" 
                                            class="btn btn-{{ $permission->is_active ? 'warning' : 'success' }}">
                                        <i class="fas fa-{{ $permission->is_active ? 'pause' : 'play' }}"></i>
                                        {{ $permission->is_active ? 'Deactivate' : 'Activate' }}
                                    </button>
                                </form>
                            @endcan
                            
                            @can('update', $permission)
                                <a href="{{ route('admin.permissions.edit', $permission) }}" class="btn btn-primary me-2">
                                    <i class="fas fa-edit"></i> Edit Permission
                                </a>
                            @endcan
                            
                            @can('delete', $permission)
                                @if($permission->roles->count() == 0)
                                    <form action="{{ route('admin.permissions.destroy', $permission) }}" 
                                          method="POST" class="d-inline" 
                                          onsubmit="return confirm('Are you sure you want to delete this permission? This action cannot be undone.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </form>
                                @else
                                    <button type="button" class="btn btn-danger" disabled title="Cannot delete permission assigned to roles">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                @endif
                            @endcan
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection