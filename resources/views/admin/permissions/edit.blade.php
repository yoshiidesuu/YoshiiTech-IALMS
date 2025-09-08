@extends('layouts.admin')

@section('title', 'Edit Permission')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-edit"></i> Edit Permission: {{ $permission->display_name }}
                        </h5>
                        <div>
                            <a href="{{ route('admin.permissions.show', $permission) }}" class="btn btn-light btn-sm me-2">
                                <i class="fas fa-eye"></i> View
                            </a>
                            <a href="{{ route('admin.permissions.index') }}" class="btn btn-light btn-sm">
                                <i class="fas fa-arrow-left"></i> Back to Permissions
                            </a>
                        </div>
                    </div>
                </div>
                
                <form action="{{ route('admin.permissions.update', $permission) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        <div class="row">
                            <!-- Permission Name -->
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label required">Permission Name</label>
                                <input type="text" 
                                       class="form-control @error('name') is-invalid @enderror" 
                                       id="name" 
                                       name="name" 
                                       value="{{ old('name', $permission->name) }}" 
                                       placeholder="e.g., users.manage"
                                       required>
                                <div class="form-text">
                                    Use lowercase letters, dots, and underscores only. Follow format: module.action
                                </div>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Display Name -->
                            <div class="col-md-6 mb-3">
                                <label for="display_name" class="form-label required">Display Name</label>
                                <input type="text" 
                                       class="form-control @error('display_name') is-invalid @enderror" 
                                       id="display_name" 
                                       name="display_name" 
                                       value="{{ old('display_name', $permission->display_name) }}" 
                                       placeholder="e.g., Manage Users"
                                       required>
                                <div class="form-text">
                                    Human-readable name for the permission
                                </div>
                                @error('display_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="row">
                            <!-- Module -->
                            <div class="col-md-6 mb-3">
                                <label for="module" class="form-label required">Module</label>
                                <select class="form-select @error('module') is-invalid @enderror" 
                                        id="module" 
                                        name="module" 
                                        required>
                                    <option value="">Select Module</option>
                                    @foreach($modules as $module)
                                        <option value="{{ $module }}" {{ old('module', $permission->module) == $module ? 'selected' : '' }}>
                                            {{ ucfirst($module) }}
                                        </option>
                                    @endforeach
                                    @if(!in_array($permission->module, $modules))
                                        <option value="{{ $permission->module }}" selected>
                                            {{ ucfirst($permission->module) }} (Current)
                                        </option>
                                    @endif
                                    <option value="custom" {{ old('module') == 'custom' ? 'selected' : '' }}>Custom Module</option>
                                </select>
                                <div class="form-text">
                                    Functional area this permission belongs to
                                </div>
                                @error('module')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Custom Module Input (hidden by default) -->
                            <div class="col-md-6 mb-3" id="custom-module-group" style="display: none;">
                                <label for="custom_module" class="form-label">Custom Module Name</label>
                                <input type="text" 
                                       class="form-control @error('custom_module') is-invalid @enderror" 
                                       id="custom_module" 
                                       name="custom_module" 
                                       value="{{ old('custom_module') }}" 
                                       placeholder="e.g., reports">
                                <div class="form-text">
                                    Enter new module name (lowercase, no spaces)
                                </div>
                                @error('custom_module')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <!-- Description -->
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control tinymce-editor @error('description') is-invalid @enderror" 
                                      id="description" 
                                      name="description" 
                                      rows="3" 
                                      placeholder="Describe what this permission allows users to do...">{{ old('description', $permission->description) }}</textarea>
                            <div class="form-text">
                                Optional: Provide a clear description of what this permission grants access to
                            </div>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <!-- Active Status -->
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input @error('is_active') is-invalid @enderror" 
                                       type="checkbox" 
                                       id="is_active" 
                                       name="is_active" 
                                       value="1" 
                                       {{ old('is_active', $permission->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    <strong>Active Permission</strong>
                                </label>
                                <div class="form-text">
                                    Active permissions can be assigned to roles and used for access control
                                </div>
                                @error('is_active')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <!-- Permission Usage Information -->
                        @if($permission->roles->count() > 0)
                            <div class="alert alert-warning">
                                <h6 class="alert-heading">
                                    <i class="fas fa-exclamation-triangle"></i> Permission Usage
                                </h6>
                                <p class="mb-2">
                                    This permission is currently assigned to <strong>{{ $permission->roles->count() }}</strong> role(s):
                                </p>
                                <div class="mb-2">
                                    @foreach($permission->roles as $role)
                                        <span class="badge bg-secondary me-1">{{ $role->display_name }}</span>
                                    @endforeach
                                </div>
                                <small class="text-muted">
                                    Changes to this permission will affect all users with these roles.
                                </small>
                            </div>
                        @endif
                        
                        <!-- Permission Preview -->
                        <div class="alert alert-info" id="permission-preview">
                            <h6 class="alert-heading">
                                <i class="fas fa-eye"></i> Permission Preview
                            </h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <strong>Name:</strong> <code id="preview-name">{{ $permission->name }}</code><br>
                                    <strong>Display Name:</strong> <span id="preview-display">{{ $permission->display_name }}</span>
                                </div>
                                <div class="col-md-6">
                                    <strong>Module:</strong> <span class="badge bg-secondary" id="preview-module">{{ ucfirst($permission->module) }}</span><br>
                                    <strong>Status:</strong> <span class="badge {{ $permission->is_active ? 'bg-success' : 'bg-danger' }}" id="preview-status">
                                        <i class="fas fa-{{ $permission->is_active ? 'check' : 'times' }}-circle"></i> 
                                        {{ $permission->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </div>
                            </div>
                            <div class="mt-2">
                                <strong>Description:</strong> <span id="preview-description" class="text-muted">{{ $permission->description ?: 'No description provided' }}</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card-footer">
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.permissions.show', $permission) }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update Permission
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const moduleSelect = document.getElementById('module');
    const customModuleGroup = document.getElementById('custom-module-group');
    const customModuleInput = document.getElementById('custom_module');
    
    // Form fields for preview
    const nameInput = document.getElementById('name');
    const displayNameInput = document.getElementById('display_name');
    const descriptionInput = document.getElementById('description');
    const isActiveInput = document.getElementById('is_active');
    
    // Preview elements
    const previewName = document.getElementById('preview-name');
    const previewDisplay = document.getElementById('preview-display');
    const previewModule = document.getElementById('preview-module');
    const previewStatus = document.getElementById('preview-status');
    const previewDescription = document.getElementById('preview-description');
    
    // Handle custom module visibility
    moduleSelect.addEventListener('change', function() {
        if (this.value === 'custom') {
            customModuleGroup.style.display = 'block';
            customModuleInput.required = true;
        } else {
            customModuleGroup.style.display = 'none';
            customModuleInput.required = false;
            customModuleInput.value = '';
        }
        updatePreview();
    });
    
    // Update preview on field changes
    [nameInput, displayNameInput, descriptionInput, isActiveInput].forEach(field => {
        field.addEventListener('input', updatePreview);
        field.addEventListener('change', updatePreview);
    });
    
    function getSelectedModule() {
        if (moduleSelect.value === 'custom') {
            return customModuleInput.value.toLowerCase().replace(/[^a-z0-9]/g, '');
        }
        return moduleSelect.value;
    }
    
    function updatePreview() {
        const name = nameInput.value;
        const displayName = displayNameInput.value;
        const module = getSelectedModule();
        const description = descriptionInput.value;
        const isActive = isActiveInput.checked;
        
        previewName.textContent = name || 'Not set';
        previewDisplay.textContent = displayName || 'Not set';
        previewModule.textContent = module ? module.charAt(0).toUpperCase() + module.slice(1) : 'Not set';
        previewDescription.textContent = description || 'No description provided';
        
        previewStatus.className = 'badge ' + (isActive ? 'bg-success' : 'bg-danger');
        previewStatus.innerHTML = (isActive ? '<i class="fas fa-check-circle"></i> Active' : '<i class="fas fa-times-circle"></i> Inactive');
    }
    
    // Initial setup
    if (moduleSelect.value === 'custom') {
        customModuleGroup.style.display = 'block';
        customModuleInput.required = true;
    }
});
</script>
@endsection