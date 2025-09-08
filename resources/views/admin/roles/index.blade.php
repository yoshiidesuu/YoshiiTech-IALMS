@extends('layouts.admin')

@section('page-title', 'Role Management')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Role Management</h2>
    @can('roles.manage')
        <a href="{{ route('admin.roles.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i> Add New Role
        </a>
    @endcan
</div>

<!-- Search and Filter Form -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.roles.index') }}" class="row g-3">
            <div class="col-md-6">
                <label for="search" class="form-label">Search</label>
                <input type="text" class="form-control" id="search" name="search" 
                       value="{{ request('search') }}" placeholder="Search by name or description...">
            </div>
            <div class="col-md-4">
                <label for="status" class="form-label">Status</label>
                <select class="form-select" id="status" name="status">
                    <option value="">All Status</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-outline-primary me-2">
                    <i class="bi bi-search"></i> Search
                </button>
                <a href="{{ route('admin.roles.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-clockwise"></i>
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Roles Table -->
<div class="card">
    <div class="card-body">
        @if($roles->count() > 0)
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>Role Name</th>
                            <th>Display Name</th>
                            <th>Description</th>
                            <th>Users</th>
                            <th>Permissions</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($roles as $role)
                            <tr>
                                <td>
                                    <code class="text-dark">{{ $role->name }}</code>
                                    @if(in_array($role->name, ['super_admin', 'admin']))
                                        <span class="badge bg-warning text-dark ms-1">System</span>
                                    @endif
                                </td>
                                <td class="fw-medium">{{ $role->display_name }}</td>
                                <td>
                                    <small class="text-muted">{{ Str::limit($role->description, 50) }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-info">{{ $role->users_count }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-secondary">{{ $role->permissions_count }}</span>
                                </td>
                                <td>
                                    @if($role->is_active)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-danger">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.roles.show', $role) }}" 
                                           class="btn btn-sm btn-outline-info" title="View">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        @can('roles.manage')
                                            <a href="{{ route('admin.roles.edit', $role) }}" 
                                               class="btn btn-sm btn-outline-primary" title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            @if(!in_array($role->name, ['super_admin', 'admin']) || !$role->is_active)
                                                <form method="POST" action="{{ route('admin.roles.toggle-status', $role) }}" 
                                                      class="d-inline" onsubmit="return confirm('Are you sure?')">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="btn btn-sm {{ $role->is_active ? 'btn-outline-warning' : 'btn-outline-success' }}" 
                                                            title="{{ $role->is_active ? 'Deactivate' : 'Activate' }}">
                                                        <i class="bi bi-{{ $role->is_active ? 'pause' : 'play' }}"></i>
                                                    </button>
                                                </form>
                                            @endif
                                            @if(!in_array($role->name, ['super_admin', 'admin']) && $role->users_count == 0)
                                                <form method="POST" action="{{ route('admin.roles.destroy', $role) }}" 
                                                      class="d-inline" onsubmit="return confirm('Are you sure you want to delete this role? This action cannot be undone.')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div>
                    <small class="text-muted">
                        Showing {{ $roles->firstItem() }} to {{ $roles->lastItem() }} of {{ $roles->total() }} results
                    </small>
                </div>
                <div>
                    {{ $roles->links() }}
                </div>
            </div>
        @else
            <div class="text-center py-5">
                <i class="bi bi-shield-lock display-1 text-muted"></i>
                <h4 class="mt-3">No Roles Found</h4>
                <p class="text-muted">No roles match your current search criteria.</p>
                @can('roles.manage')
                    <a href="{{ route('admin.roles.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-1"></i> Add First Role
                    </a>
                @endcan
            </div>
        @endif
    </div>
</div>

<!-- Role Information Modal -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Role Information</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>System Roles</h6>
                        <ul class="list-unstyled">
                            <li><code>super_admin</code> - Full system access with all permissions</li>
                            <li><code>admin</code> - System administrator with most permissions</li>
                            <li><code>registrar</code> - Academic records and student information management</li>
                            <li><code>faculty</code> - Teaching staff with grade and class management access</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h6>User Roles</h6>
                        <ul class="list-unstyled">
                            <li><code>student</code> - Student portal access</li>
                            <li><code>parent</code> - Parent portal access to student information</li>
                        </ul>
                        <small class="text-muted">
                            System roles (super_admin, admin) cannot be deleted or deactivated.
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection