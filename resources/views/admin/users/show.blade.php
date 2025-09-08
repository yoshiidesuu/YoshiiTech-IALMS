@extends('layouts.admin')

@section('title', 'User Details')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">User Details: {{ $user->name }}</h5>
                    <div>
                        @can('users.manage')
                            <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-warning btn-sm">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                        @endcan
                        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Users
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Basic Information -->
                        <div class="col-md-6">
                            <div class="card h-100">
                                <div class="card-header">
                                    <h6 class="mb-0"><i class="fas fa-user"></i> Basic Information</h6>
                                </div>
                                <div class="card-body">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td class="fw-bold">Full Name:</td>
                                            <td>{{ $user->name }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">Email:</td>
                                            <td>{{ $user->email }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">Status:</td>
                                            <td>
                                                @if($user->is_active)
                                                    <span class="badge bg-success">Active</span>
                                                @else
                                                    <span class="badge bg-danger">Inactive</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">Email Verified:</td>
                                            <td>
                                                @if($user->email_verified_at)
                                                    <span class="badge bg-success">Verified</span>
                                                    <small class="text-muted d-block">{{ $user->email_verified_at->format('M d, Y g:i A') }}</small>
                                                @else
                                                    <span class="badge bg-warning">Not Verified</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">Created:</td>
                                            <td>{{ $user->created_at->format('M d, Y g:i A') }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">Last Updated:</td>
                                            <td>{{ $user->updated_at->format('M d, Y g:i A') }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">Last Login:</td>
                                            <td>
                                                @if($user->last_login_at)
                                                    {{ $user->last_login_at->format('M d, Y g:i A') }}
                                                @else
                                                    <span class="text-muted">Never</span>
                                                @endif
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Role Information -->
                        <div class="col-md-6">
                            <div class="card h-100">
                                <div class="card-header">
                                    <h6 class="mb-0"><i class="fas fa-user-tag"></i> Role Information</h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label class="fw-bold">Primary Role:</label>
                                        @if($user->primaryRole)
                                            <div class="mt-1">
                                                <span class="badge bg-primary fs-6">{{ $user->primaryRole->display_name }}</span>
                                                <p class="text-muted small mt-1 mb-0">{{ $user->primaryRole->description }}</p>
                                            </div>
                                        @else
                                            <p class="text-muted">No primary role assigned</p>
                                        @endif
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="fw-bold">Additional Roles:</label>
                                        @if($user->roles->count() > 0)
                                            <div class="mt-1">
                                                @foreach($user->roles as $role)
                                                    <span class="badge bg-secondary me-1 mb-1">{{ $role->display_name }}</span>
                                                @endforeach
                                            </div>
                                        @else
                                            <p class="text-muted small">No additional roles assigned</p>
                                        @endif
                                    </div>
                                    
                                    <div>
                                        <label class="fw-bold">All Permissions:</label>
                                        @php
                                            $permissions = $user->getAllPermissions();
                                        @endphp
                                        @if($permissions->count() > 0)
                                            <div class="mt-1" style="max-height: 200px; overflow-y: auto;">
                                                @foreach($permissions->groupBy('module') as $module => $modulePermissions)
                                                    <div class="mb-2">
                                                        <small class="fw-bold text-uppercase text-muted">{{ $module }}</small>
                                                        <div>
                                                            @foreach($modulePermissions as $permission)
                                                                <span class="badge bg-light text-dark border me-1 mb-1" style="font-size: 0.7rem;">
                                                                    {{ $permission->display_name }}
                                                                </span>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <p class="text-muted small">No permissions assigned</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Action Buttons -->
                    @can('users.manage')
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="mb-0"><i class="fas fa-cogs"></i> Actions</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="d-flex gap-2 flex-wrap">
                                            <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-warning">
                                                <i class="fas fa-edit"></i> Edit User
                                            </a>
                                            
                                            <form action="{{ route('admin.users.toggle-status', $user) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn {{ $user->is_active ? 'btn-danger' : 'btn-success' }}" 
                                                        onclick="return confirm('Are you sure you want to {{ $user->is_active ? 'deactivate' : 'activate' }} this user?')">
                                                    <i class="fas fa-{{ $user->is_active ? 'ban' : 'check' }}"></i> 
                                                    {{ $user->is_active ? 'Deactivate' : 'Activate' }}
                                                </button>
                                            </form>
                                            
                                            @if($user->id !== auth()->id())
                                                <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger" 
                                                            onclick="return confirm('Are you sure you want to delete this user? This action cannot be undone.')">
                                                        <i class="fas fa-trash"></i> Delete User
                                                    </button>
                                                </form>
                                            @else
                                                <button type="button" class="btn btn-danger" disabled title="You cannot delete your own account">
                                                    <i class="fas fa-trash"></i> Delete User
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endcan
                </div>
            </div>
        </div>
    </div>
</div>
@endsection