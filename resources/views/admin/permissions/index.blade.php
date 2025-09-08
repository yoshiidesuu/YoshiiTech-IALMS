@extends('layouts.admin')

@section('title', 'Permissions Management')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="h4 mb-0">
                    <i class="fas fa-key"></i> Permissions Management
                </h2>
                @can('create', App\Models\Permission::class)
                    <a href="{{ route('admin.permissions.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Add New Permission
                    </a>
                @endcan
            </div>

            <!-- Search and Filter Form -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.permissions.index') }}">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="search" class="form-label">Search</label>
                                <input type="text" class="form-control" id="search" name="search" 
                                       value="{{ request('search') }}" 
                                       placeholder="Search by name, display name, or description...">
                            </div>
                            <div class="col-md-3">
                                <label for="module" class="form-label">Module</label>
                                <select class="form-select" id="module" name="module">
                                    <option value="">All Modules</option>
                                    @foreach($modules as $module)
                                        <option value="{{ $module }}" {{ request('module') == $module ? 'selected' : '' }}>
                                            {{ ucfirst($module) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="">All Status</option>
                                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="submit" class="btn btn-outline-primary me-2">
                                    <i class="fas fa-search"></i> Search
                                </button>
                                <a href="{{ route('admin.permissions.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-times"></i>
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Permissions Table -->
            <div class="card">
                <div class="card-body">
                    @if($permissions->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Permission Name</th>
                                        <th>Display Name</th>
                                        <th>Module</th>
                                        <th>Description</th>
                                        <th>Status</th>
                                        <th>Roles</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($permissions as $permission)
                                        <tr>
                                            <td>
                                                <code>{{ $permission->name }}</code>
                                            </td>
                                            <td>
                                                <strong>{{ $permission->display_name }}</strong>
                                            </td>
                                            <td>
                                                <span class="badge bg-secondary">
                                                    {{ ucfirst($permission->module) }}
                                                </span>
                                            </td>
                                            <td>
                                                <small class="text-muted">
                                                    {{ Str::limit($permission->description, 50) ?: 'No description' }}
                                                </small>
                                            </td>
                                            <td>
                                                @if($permission->is_active)
                                                    <span class="badge bg-success">
                                                        <i class="fas fa-check-circle"></i> Active
                                                    </span>
                                                @else
                                                    <span class="badge bg-danger">
                                                        <i class="fas fa-times-circle"></i> Inactive
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-primary">
                                                    {{ $permission->roles_count ?? 0 }} roles
                                                </span>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm" role="group">
                                                    @can('view', $permission)
                                                        <a href="{{ route('admin.permissions.show', $permission) }}" 
                                                           class="btn btn-info" title="View" data-bs-toggle="tooltip">
                                                            <i class="bi bi-eye"></i>
                                                        </a>
                                                    @endcan
                                                    
                                                    @can('update', $permission)
                                                        <a href="{{ route('admin.permissions.edit', $permission) }}" 
                                                           class="btn btn-primary" title="Edit" data-bs-toggle="tooltip">
                                                            <i class="bi bi-pencil"></i>
                                                        </a>
                                                    @endcan
                                                    
                                                    @can('update', $permission)
                                                        <form action="{{ route('admin.permissions.toggle-status', $permission) }}" 
                                                              method="POST" class="d-inline">
                                                            @csrf
                                                            @method('PATCH')
                                                            <button type="submit" 
                                                                    class="btn btn-{{ $permission->is_active ? 'warning' : 'success' }}" 
                                                                    title="{{ $permission->is_active ? 'Deactivate' : 'Activate' }}" 
                                                                    data-bs-toggle="tooltip">
                                                                <i class="bi bi-{{ $permission->is_active ? 'pause-fill' : 'play-fill' }}"></i>
                                                            </button>
                                                        </form>
                                                    @endcan
                                                    
                                                    @can('delete', $permission)
                                                        <form action="{{ route('admin.permissions.destroy', $permission) }}" 
                                                              method="POST" class="d-inline" 
                                                              onsubmit="return confirm('Are you sure you want to delete this permission? This action cannot be undone.')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger" title="Delete" data-bs-toggle="tooltip">
                                                                <i class="bi bi-trash"></i>
                                                            </button>
                                                        </form>
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
                                    Showing {{ $permissions->firstItem() }} to {{ $permissions->lastItem() }} of {{ $permissions->total() }} results
                                </small>
                            </div>
                            <div>
                                {{ $permissions->appends(request()->query())->links('pagination.bootstrap-4') }}
                            </div>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-key fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No permissions found</h5>
                            <p class="text-muted">No permissions match your current search criteria.</p>
                            @can('create', App\Models\Permission::class)
                                <a href="{{ route('admin.permissions.create') }}" class="btn btn-primary">
                                    <i class="fas fa-plus"></i> Add First Permission
                                </a>
                            @endcan
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Permission Info Modal -->
<div class="modal fade" id="permissionInfoModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="fas fa-info-circle"></i> Permission System Information
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-primary">What are Permissions?</h6>
                        <p class="small text-muted">
                            Permissions define specific actions that users can perform in the system. 
                            They are the building blocks of the role-based access control (RBAC) system.
                        </p>
                        
                        <h6 class="text-primary mt-3">Permission Structure</h6>
                        <ul class="small text-muted">
                            <li><strong>Name:</strong> Unique identifier (e.g., users.manage)</li>
                            <li><strong>Display Name:</strong> Human-readable name</li>
                            <li><strong>Module:</strong> Functional area grouping</li>
                            <li><strong>Description:</strong> What the permission allows</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-primary">Permission Modules</h6>
                        <div class="small text-muted">
                            @foreach($modules as $module)
                                <span class="badge bg-secondary me-1 mb-1">{{ ucfirst($module) }}</span>
                            @endforeach
                        </div>
                        
                        <h6 class="text-primary mt-3">Best Practices</h6>
                        <ul class="small text-muted">
                            <li>Use descriptive permission names</li>
                            <li>Group related permissions by module</li>
                            <li>Follow naming convention: module.action</li>
                            <li>Provide clear descriptions</li>
                            <li>Regularly review and cleanup unused permissions</li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Info Button -->
<div class="position-fixed bottom-0 end-0 p-3">
    <button type="button" class="btn btn-info btn-sm rounded-circle" 
            data-bs-toggle="modal" data-bs-target="#permissionInfoModal" 
            title="Permission System Info">
        <i class="fas fa-question"></i>
    </button>
</div>
@endsection