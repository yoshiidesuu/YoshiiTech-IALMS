@extends('layouts.admin')

@section('title', 'Edit Role')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-edit"></i> Edit Role: {{ $role->display_name }}
                    </h5>
                    <div class="btn-group" role="group">
                        @can('view', $role)
                            <a href="{{ route('admin.roles.show', $role) }}" class="btn btn-light btn-sm">
                                <i class="fas fa-eye"></i> View
                            </a>
                        @endcan
                        <a href="{{ route('admin.roles.index') }}" class="btn btn-light btn-sm">
                            <i class="fas fa-arrow-left"></i> Back to Roles
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.roles.update', $role) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Role Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                           id="name" name="name" value="{{ old('name', $role->name) }}" 
                                           {{ $role->is_system ? 'readonly' : 'required' }}>
                                    @if($role->is_system)
                                        <div class="form-text text-warning">
                                            <i class="fas fa-lock"></i> System role name cannot be changed.
                                        </div>
                                    @endif
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="display_name" class="form-label">Display Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('display_name') is-invalid @enderror" 
                                           id="display_name" name="display_name" 
                                           value="{{ old('display_name', $role->display_name) }}" required>
                                    @error('display_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea class="form-control tinymce-editor @error('description') is-invalid @enderror" 
                                              id="description" name="description" rows="3" 
                                              placeholder="Brief description of this role's purpose and responsibilities">{{ old('description', $role->description) }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label">Permissions</label>
                                    <div class="border rounded p-3 bg-light">
                                        @if($permissions->count() > 0)
                                            @foreach($permissions->groupBy('module') as $module => $modulePermissions)
                                                <div class="mb-4">
                                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                                        <h6 class="text-primary mb-0">
                                                            <i class="fas fa-folder"></i> {{ ucfirst($module) }} Module
                                                        </h6>
                                                        <div class="btn-group btn-group-sm" role="group">
                                                            <button type="button" class="btn btn-outline-primary select-all-module" 
                                                                    data-module="{{ $module }}">
                                                                Select All
                                                            </button>
                                                            <button type="button" class="btn btn-outline-secondary deselect-all-module" 
                                                                    data-module="{{ $module }}">
                                                                Deselect All
                                                            </button>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        @foreach($modulePermissions as $permission)
                                                            <div class="col-md-4 col-lg-3">
                                                                <div class="form-check mb-2">
                                                                    <input class="form-check-input" type="checkbox" 
                                                                           id="permission_{{ $permission->id }}" 
                                                                           name="permissions[]" 
                                                                           value="{{ $permission->id }}"
                                                                           data-module="{{ $module }}"
                                                                           {{ in_array($permission->id, $assignedPermissions) ? 'checked' : '' }}>
                                                                    <label class="form-check-label" for="permission_{{ $permission->id }}">
                                                                        {{ $permission->display_name }}
                                                                    </label>
                                                                    @if($permission->description)
                                                                        <div class="form-text small">{{ $permission->description }}</div>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endforeach
                                        @else
                                            <p class="text-muted mb-0">No permissions available. Please create permissions first.</p>
                                        @endif
                                    </div>
                                    @error('permissions')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" 
                                           id="is_active" name="is_active" value="1" 
                                           {{ old('is_active', $role->is_active) ? 'checked' : '' }}
                                           {{ $role->is_system ? 'disabled' : '' }}>
                                    <label class="form-check-label" for="is_active">
                                        Active Role
                                    </label>
                                    @if($role->is_system)
                                        <div class="form-text text-warning">
                                            <i class="fas fa-lock"></i> System role status cannot be changed.
                                        </div>
                                    @else
                                        <div class="form-text">Inactive roles cannot be assigned to users.</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        @if($role->users_count > 0)
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i>
                                <strong>Note:</strong> This role is currently assigned to {{ $role->users_count }} user(s). 
                                Changes to permissions will affect all assigned users.
                            </div>
                        @endif
                        
                        <div class="card mt-4">
                            <div class="card-header bg-light">
                                <h6 class="mb-0"><i class="fas fa-info-circle"></i> Role Information</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <strong>Created:</strong><br>
                                        <span class="text-muted">{{ $role->created_at->format('M d, Y \\a\\t g:i A') }}</span>
                                    </div>
                                    <div class="col-md-4">
                                        <strong>Last Updated:</strong><br>
                                        <span class="text-muted">{{ $role->updated_at->format('M d, Y \\a\\t g:i A') }}</span>
                                    </div>
                                    <div class="col-md-4">
                                        <strong>Users Assigned:</strong><br>
                                        <span class="badge bg-primary">{{ $role->users_count ?? 0 }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update Role
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Select/Deselect all permissions for a module
    document.querySelectorAll('.select-all-module').forEach(button => {
        button.addEventListener('click', function() {
            const module = this.dataset.module;
            const checkboxes = document.querySelectorAll(`input[data-module="${module}"]`);
            checkboxes.forEach(checkbox => {
                if (!checkbox.disabled) {
                    checkbox.checked = true;
                }
            });
        });
    });
    
    document.querySelectorAll('.deselect-all-module').forEach(button => {
        button.addEventListener('click', function() {
            const module = this.dataset.module;
            const checkboxes = document.querySelectorAll(`input[data-module="${module}"]`);
            checkboxes.forEach(checkbox => {
                if (!checkbox.disabled) {
                    checkbox.checked = false;
                }
            });
        });
    });
});
</script>
@endsection