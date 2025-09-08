@extends('layouts.admin')

@section('title', 'Create Role')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Create New Role</h5>
                    <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Roles
                    </a>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.roles.store') }}" method="POST">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Role Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                           id="name" name="name" value="{{ old('name') }}" required
                                           placeholder="e.g., teacher, student, admin">
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Use lowercase letters, numbers, and underscores only.</div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="display_name" class="form-label">Display Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('display_name') is-invalid @enderror" 
                                           id="display_name" name="display_name" value="{{ old('display_name') }}" required
                                           placeholder="e.g., Teacher, Student, Administrator">
                                    @error('display_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Human-readable name for the role.</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea class="form-control tinymce-editor @error('description') is-invalid @enderror" 
                                              id="description" name="description" rows="3" 
                                              placeholder="Describe the role and its responsibilities...">{{ old('description') }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label">Permissions <span class="text-danger">*</span></label>
                                    <div class="border rounded p-3">
                                        @if($permissions->count() > 0)
                                            @foreach($permissions->groupBy('module') as $module => $modulePermissions)
                                                <div class="mb-4">
                                                    <div class="d-flex align-items-center mb-2">
                                                        <h6 class="mb-0 text-uppercase text-primary">{{ $module }}</h6>
                                                        <div class="ms-auto">
                                                            <button type="button" class="btn btn-sm btn-outline-primary select-all-module" 
                                                                    data-module="{{ $module }}">
                                                                Select All
                                                            </button>
                                                            <button type="button" class="btn btn-sm btn-outline-secondary deselect-all-module" 
                                                                    data-module="{{ $module }}">
                                                                Deselect All
                                                            </button>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        @foreach($modulePermissions as $permission)
                                                            <div class="col-md-4 col-lg-3">
                                                                <div class="form-check">
                                                                    <input class="form-check-input permission-checkbox" 
                                                                           type="checkbox" name="permissions[]" 
                                                                           value="{{ $permission->id }}" 
                                                                           id="permission_{{ $permission->id }}"
                                                                           data-module="{{ $module }}"
                                                                           {{ in_array($permission->id, old('permissions', [])) ? 'checked' : '' }}>
                                                                    <label class="form-check-label" for="permission_{{ $permission->id }}">
                                                                        {{ $permission->display_name }}
                                                                        @if($permission->description)
                                                                            <small class="text-muted d-block">{{ $permission->description }}</small>
                                                                        @endif
                                                                    </label>
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
                                           {{ old('is_active', true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">
                                        Active Role
                                    </label>
                                    <div class="form-text">Inactive roles cannot be assigned to users.</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Create Role
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
    // Auto-generate role name from display name
    const displayNameInput = document.getElementById('display_name');
    const nameInput = document.getElementById('name');
    
    displayNameInput.addEventListener('input', function() {
        if (!nameInput.dataset.manuallyEdited) {
            const displayName = this.value;
            const roleName = displayName.toLowerCase()
                .replace(/[^a-z0-9\s]/g, '')
                .replace(/\s+/g, '_')
                .trim();
            nameInput.value = roleName;
        }
    });
    
    nameInput.addEventListener('input', function() {
        nameInput.dataset.manuallyEdited = 'true';
    });
    
    // Select/Deselect all permissions for a module
    document.querySelectorAll('.select-all-module').forEach(button => {
        button.addEventListener('click', function() {
            const module = this.dataset.module;
            const checkboxes = document.querySelectorAll(`input[data-module="${module}"]`);
            checkboxes.forEach(checkbox => checkbox.checked = true);
        });
    });
    
    document.querySelectorAll('.deselect-all-module').forEach(button => {
        button.addEventListener('click', function() {
            const module = this.dataset.module;
            const checkboxes = document.querySelectorAll(`input[data-module="${module}"]`);
            checkboxes.forEach(checkbox => checkbox.checked = false);
        });
    });
});
</script>
@endsection