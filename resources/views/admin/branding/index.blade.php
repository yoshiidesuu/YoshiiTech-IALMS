@extends('layouts.admin')

@section('title', 'Branding Management')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0 text-gray-800">Branding Management</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Branding Management</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <!-- Branding Settings Form -->
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Branding Settings</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.branding.update') }}" method="POST" enctype="multipart/form-data" id="brandingForm">
                        @csrf
                        @method('PUT')

                        <!-- Institution Information -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="institution_name" class="form-label">Institution Name</label>
                                <input type="text" class="form-control" id="institution_name" name="institution_name" 
                                       value="{{ old('institution_name', $settings['institution_name']) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label for="system_title" class="form-label">System Title</label>
                                <input type="text" class="form-control" id="system_title" name="system_title" 
                                       value="{{ old('system_title', $settings['system_title']) }}" required>
                            </div>
                        </div>

                        <!-- Logo Upload -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="logo" class="form-label">Logo</label>
                                <input type="file" class="form-control" id="logo" name="logo" accept="image/*">
                                <small class="form-text text-muted">Supported formats: JPEG, PNG, JPG, GIF, SVG. Max size: 2MB</small>
                                @if($settings['logo_path'])
                                    <div class="mt-2">
                                        <img src="{{ $settings['logo_path'] }}" alt="Current Logo" class="img-thumbnail" style="max-height: 100px;">
                                        <button type="button" class="btn btn-sm btn-danger ms-2" onclick="deleteFile('logo')">
                                            <i class="bi bi-trash"></i> Delete
                                        </button>
                                    </div>
                                @endif
                            </div>
                            <div class="col-md-6">
                                <label for="favicon" class="form-label">Favicon</label>
                                <input type="file" class="form-control" id="favicon" name="favicon" accept=".ico,.png">
                                <small class="form-text text-muted">Supported formats: ICO, PNG. Max size: 512KB</small>
                                @if($settings['favicon_path'])
                                    <div class="mt-2">
                                        <img src="{{ $settings['favicon_path'] }}" alt="Current Favicon" class="img-thumbnail" style="max-height: 32px;">
                                        <button type="button" class="btn btn-sm btn-danger ms-2" onclick="deleteFile('favicon')">
                                            <i class="bi bi-trash"></i> Delete
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Contact Information -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="contact_email" class="form-label">Contact Email</label>
                                <input type="email" class="form-control" id="contact_email" name="contact_email" 
                                       value="{{ old('contact_email', $settings['contact_email']) }}">
                            </div>
                            <div class="col-md-6">
                                <label for="contact_phone" class="form-label">Contact Phone</label>
                                <input type="text" class="form-control" id="contact_phone" name="contact_phone" 
                                       value="{{ old('contact_phone', $settings['contact_phone']) }}">
                            </div>
                        </div>

                        <!-- Footer Text -->
                        <div class="mb-4">
                            <label for="footer_text" class="form-label">Footer Text</label>
                            <textarea class="form-control tinymce-editor" id="footer_text" name="footer_text" rows="3" 
                                      placeholder="Enter footer text...">{{ old('footer_text', $settings['footer_text']) }}</textarea>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex justify-content-between">
                            <button type="button" class="btn btn-secondary" onclick="resetBranding()">
                                <i class="bi bi-arrow-clockwise"></i> Reset to Defaults
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-lg"></i> Update Branding
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Preview Panel -->
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Live Preview</h6>
                </div>
                <div class="card-body">
                    <div class="preview-container">
                        <!-- Header Preview -->
                        <div class="preview-header bg-primary text-white p-3 rounded mb-3">
                            <div class="d-flex align-items-center">
                                <div class="preview-logo me-3">
                                    @if($settings['logo_path'])
                                        <img src="{{ $settings['logo_path'] }}" alt="Logo" style="max-height: 40px;" id="previewLogo">
                                    @else
                                        <div class="bg-white text-primary rounded p-2" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;" id="previewLogo">
                                            <i class="bi bi-building"></i>
                                        </div>
                                    @endif
                                </div>
                                <div>
                                    <h5 class="mb-0" id="previewInstitution">{{ $settings['institution_name'] }}</h5>
                                    <small id="previewTitle">{{ $settings['system_title'] }}</small>
                                </div>
                            </div>
                        </div>

                        <!-- Contact Preview -->
                        <div class="preview-contact mb-3">
                            <h6 class="text-muted">Contact Information</h6>
                            <p class="mb-1">
                                <i class="bi bi-envelope me-2"></i>
                                <span id="previewEmail">{{ $settings['contact_email'] }}</span>
                            </p>
                            <p class="mb-0">
                                <i class="bi bi-telephone me-2"></i>
                                <span id="previewPhone">{{ $settings['contact_phone'] }}</span>
                            </p>
                        </div>

                        <!-- Footer Preview -->
                        <div class="preview-footer bg-light p-2 rounded text-center">
                            <small class="text-muted" id="previewFooter">{{ $settings['footer_text'] }}</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Real-time preview updates
document.addEventListener('DOMContentLoaded', function() {
    // Update preview on input change
    document.getElementById('institution_name').addEventListener('input', function() {
        document.getElementById('previewInstitution').textContent = this.value || 'Institution Name';
    });

    document.getElementById('system_title').addEventListener('input', function() {
        document.getElementById('previewTitle').textContent = this.value || 'Management System';
    });

    document.getElementById('contact_email').addEventListener('input', function() {
        document.getElementById('previewEmail').textContent = this.value || 'contact@institution.edu';
    });

    document.getElementById('contact_phone').addEventListener('input', function() {
        document.getElementById('previewPhone').textContent = this.value || '+1 (555) 123-4567';
    });

    document.getElementById('footer_text').addEventListener('input', function() {
        document.getElementById('previewFooter').textContent = this.value || '© 2024 Institution. All rights reserved.';
    });

    // Logo preview
    document.getElementById('logo').addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const previewLogo = document.getElementById('previewLogo');
                previewLogo.innerHTML = `<img src="${e.target.result}" alt="Logo" style="max-height: 40px;">`;
            };
            reader.readAsDataURL(file);
        }
    });
});

// Delete file function
function deleteFile(type) {
    if (confirm(`Are you sure you want to delete the ${type}?`)) {
        fetch('{{ route("admin.branding.deleteFile") }}', {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ type: type })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while deleting the file.');
        });
    }
}

// Reset branding function
function resetBranding() {
    if (confirm('Are you sure you want to reset all branding settings to defaults? This action cannot be undone.')) {
        fetch('{{ route("admin.branding.reset") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => {
            if (response.ok) {
                location.reload();
            } else {
                alert('An error occurred while resetting branding settings.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while resetting branding settings.');
        });
    }
}
</script>
@endpush
@endsection