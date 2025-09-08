@extends('layouts.admin')

@section('title', 'Edit User')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Edit User: {{ $user->name }}</h5>
                    <div>
                        <a href="{{ route('admin.users.show', $user) }}" class="btn btn-info btn-sm">
                            <i class="fas fa-eye"></i> View
                        </a>
                        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Users
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.users.update', $user) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                           id="name" name="name" value="{{ old('name', $user->name) }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                           id="email" name="email" value="{{ old('email', $user->email) }}" required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="password" class="form-label">New Password</label>
                                    <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                           id="password" name="password">
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Leave blank to keep current password. Must be at least 8 characters if changing.</div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="password_confirmation" class="form-label">Confirm New Password</label>
                                    <input type="password" class="form-control" 
                                           id="password_confirmation" name="password_confirmation">
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="primary_role_id" class="form-label">Primary Role <span class="text-danger">*</span></label>
                                    <select class="form-select @error('primary_role_id') is-invalid @enderror" 
                                            id="primary_role_id" name="primary_role_id" required>
                                        <option value="">Select a role...</option>
                                        @foreach($roles as $role)
                                            <option value="{{ $role->id }}" 
                                                {{ old('primary_role_id', $user->primary_role_id) == $role->id ? 'selected' : '' }}>
                                                {{ $role->display_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('primary_role_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Additional Roles</label>
                                    <div class="border rounded p-3" style="max-height: 200px; overflow-y: auto;">
                                        @foreach($roles as $role)
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" 
                                                       name="additional_roles[]" value="{{ $role->id }}" 
                                                       id="role_{{ $role->id }}"
                                                       {{ in_array($role->id, old('additional_roles', $user->roles->pluck('id')->toArray())) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="role_{{ $role->id }}">
                                                    {{ $role->display_name }}
                                                    <small class="text-muted d-block">{{ $role->description }}</small>
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" 
                                           id="is_active" name="is_active" value="1" 
                                           {{ old('is_active', $user->is_active) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">
                                        Active User
                                    </label>
                                    <div class="form-text">Inactive users cannot log in to the system.</div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Account Information</label>
                                    <div class="border rounded p-3 bg-light">
                                        <small class="text-muted">
                                            <strong>Created:</strong> {{ $user->created_at->format('M d, Y g:i A') }}<br>
                                            <strong>Last Updated:</strong> {{ $user->updated_at->format('M d, Y g:i A') }}<br>
                                            @if($user->last_login_at)
                                                <strong>Last Login:</strong> {{ $user->last_login_at->format('M d, Y g:i A') }}
                                            @else
                                                <strong>Last Login:</strong> Never
                                            @endif
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update User
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Prevent selecting the same role as primary and additional
document.getElementById('primary_role_id').addEventListener('change', function() {
    const primaryRoleId = this.value;
    const additionalRoleCheckboxes = document.querySelectorAll('input[name="additional_roles[]"]');
    
    additionalRoleCheckboxes.forEach(checkbox => {
        if (checkbox.value === primaryRoleId) {
            checkbox.checked = false;
            checkbox.disabled = primaryRoleId !== '';
        } else {
            checkbox.disabled = false;
        }
    });
});

// Trigger the change event on page load
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('primary_role_id').dispatchEvent(new Event('change'));
});
</script>
@endsection