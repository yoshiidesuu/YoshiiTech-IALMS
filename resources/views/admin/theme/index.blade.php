@extends('layouts.admin')

@section('title', 'Theme Management')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0 text-gray-800">Theme Management</h1>
                <div>
                    <form action="{{ route('admin.theme.reset') }}" method="POST" class="d-inline" 
                          onsubmit="return confirm('Are you sure you want to reset theme to default settings?')">
                        @csrf
                        @method('POST')
                        <button type="submit" class="btn btn-outline-secondary">
                            <i class="fas fa-undo"></i> Reset to Default
                        </button>
                    </form>
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
                <!-- Theme Configuration Form -->
                <div class="col-lg-8">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">
                                <i class="fas fa-palette"></i> Color Configuration
                            </h6>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('admin.theme.update') }}" method="POST">
                                @csrf
                                @method('PUT')

                                <div class="row">
                                    <!-- Primary Colors -->
                                    <div class="col-md-6">
                                        <h6 class="text-muted mb-3">Primary Colors</h6>
                                        
                                        <div class="mb-3">
                                            <label for="primary_color" class="form-label">Primary Color (Maroon)</label>
                                            <div class="input-group">
                                                <input type="color" class="form-control form-control-color" 
                                                       id="primary_color" name="primary_color" 
                                                       value="{{ $themeSettings['primary_color'] }}" 
                                                       title="Choose primary color">
                                                <input type="text" class="form-control" 
                                                       value="{{ $themeSettings['primary_color'] }}" 
                                                       readonly>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label for="secondary_color" class="form-label">Secondary Color (Gold)</label>
                                            <div class="input-group">
                                                <input type="color" class="form-control form-control-color" 
                                                       id="secondary_color" name="secondary_color" 
                                                       value="{{ $themeSettings['secondary_color'] }}" 
                                                       title="Choose secondary color">
                                                <input type="text" class="form-control" 
                                                       value="{{ $themeSettings['secondary_color'] }}" 
                                                       readonly>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label for="accent_color" class="form-label">Accent Color</label>
                                            <div class="input-group">
                                                <input type="color" class="form-control form-control-color" 
                                                       id="accent_color" name="accent_color" 
                                                       value="{{ $themeSettings['accent_color'] }}" 
                                                       title="Choose accent color">
                                                <input type="text" class="form-control" 
                                                       value="{{ $themeSettings['accent_color'] }}" 
                                                       readonly>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Status Colors -->
                                    <div class="col-md-6">
                                        <h6 class="text-muted mb-3">Status Colors</h6>
                                        
                                        <div class="mb-3">
                                            <label for="success_color" class="form-label">Success Color</label>
                                            <div class="input-group">
                                                <input type="color" class="form-control form-control-color" 
                                                       id="success_color" name="success_color" 
                                                       value="{{ $themeSettings['success_color'] }}" 
                                                       title="Choose success color">
                                                <input type="text" class="form-control" 
                                                       value="{{ $themeSettings['success_color'] }}" 
                                                       readonly>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label for="warning_color" class="form-label">Warning Color</label>
                                            <div class="input-group">
                                                <input type="color" class="form-control form-control-color" 
                                                       id="warning_color" name="warning_color" 
                                                       value="{{ $themeSettings['warning_color'] }}" 
                                                       title="Choose warning color">
                                                <input type="text" class="form-control" 
                                                       value="{{ $themeSettings['warning_color'] }}" 
                                                       readonly>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label for="danger_color" class="form-label">Danger Color</label>
                                            <div class="input-group">
                                                <input type="color" class="form-control form-control-color" 
                                                       id="danger_color" name="danger_color" 
                                                       value="{{ $themeSettings['danger_color'] }}" 
                                                       title="Choose danger color">
                                                <input type="text" class="form-control" 
                                                       value="{{ $themeSettings['danger_color'] }}" 
                                                       readonly>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label for="info_color" class="form-label">Info Color</label>
                                            <div class="input-group">
                                                <input type="color" class="form-control form-control-color" 
                                                       id="info_color" name="info_color" 
                                                       value="{{ $themeSettings['info_color'] }}" 
                                                       title="Choose info color">
                                                <input type="text" class="form-control" 
                                                       value="{{ $themeSettings['info_color'] }}" 
                                                       readonly>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Theme Mode -->
                                <hr>
                                <h6 class="text-muted mb-3">Theme Mode</h6>
                                <div class="mb-4">
                                    <label class="form-label">Theme Mode</label>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="theme_mode" id="light_mode" value="light" 
                                                       {{ old('theme_mode', $themeSettings['theme_mode']) == 'light' ? 'checked' : '' }} onchange="toggleThemePreview()">
                                                <label class="form-check-label" for="light_mode">
                                                    <i class="fas fa-sun me-2"></i> Light Mode
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="theme_mode" id="dark_mode" value="dark" 
                                                       {{ old('theme_mode', $themeSettings['theme_mode']) == 'dark' ? 'checked' : '' }} onchange="toggleThemePreview()">
                                                <label class="form-check-label" for="dark_mode">
                                                    <i class="fas fa-moon me-2"></i> Dark Mode
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="theme_mode" id="auto_mode" value="auto" 
                                                       {{ old('theme_mode', $themeSettings['theme_mode']) == 'auto' ? 'checked' : '' }} onchange="toggleThemePreview()">
                                                <label class="form-check-label" for="auto_mode">
                                                    <i class="fas fa-adjust me-2"></i> Auto (System)
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <small class="form-text text-muted">Auto mode will follow your system's dark/light mode preference</small>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" 
                                                       id="dark_mode_enabled" name="dark_mode_enabled" 
                                                       value="1" {{ $themeSettings['dark_mode_enabled'] ? 'checked' : '' }}>
                                                <label class="form-check-label" for="dark_mode_enabled">
                                                    Enable Dark Mode Support
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Custom CSS -->
                                <hr>
                                <h6 class="text-muted mb-3">Custom CSS</h6>
                                <div class="mb-3">
                                    <label for="custom_css" class="form-label">Custom CSS Override</label>
                                    <textarea class="form-control" id="custom_css" name="custom_css" 
                                              rows="8" placeholder="Enter custom CSS rules here...">{{ $themeSettings['custom_css'] }}</textarea>
                                    <div class="form-text">Add custom CSS to override default styles. Use with caution.</div>
                                </div>

                                <div class="d-flex justify-content-end">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Save Theme Settings
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
                            <h6 class="m-0 font-weight-bold text-primary">
                                <i class="fas fa-eye"></i> Theme Preview
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="theme-preview preview-container">
                                <div class="mb-3">
                                    <button type="button" class="btn btn-primary btn-sm me-2">Primary</button>
                                    <button type="button" class="btn btn-secondary btn-sm">Secondary</button>
                                </div>
                                <div class="mb-3">
                                    <button type="button" class="btn btn-success btn-sm me-1">Success</button>
                                    <button type="button" class="btn btn-warning btn-sm me-1">Warning</button>
                                    <button type="button" class="btn btn-danger btn-sm me-1">Danger</button>
                                    <button type="button" class="btn btn-info btn-sm">Info</button>
                                </div>
                                <div class="alert alert-primary" role="alert">
                                    <strong>Preview:</strong> This is how your theme colors will appear.
                                </div>
                                <div class="progress mb-3">
                                    <div class="progress-bar" role="progressbar" style="width: 75%"></div>
                                </div>
                                <div class="badge bg-primary me-1">Primary Badge</div>
                                <div class="badge bg-secondary">Secondary Badge</div>
                            </div>
                        </div>
                    </div>

                    <div class="card shadow">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">
                                <i class="fas fa-info-circle"></i> Theme Information
                            </h6>
                        </div>
                        <div class="card-body">
                            <p class="text-muted small mb-2">
                                <strong>Current Theme:</strong> {{ ucfirst($themeSettings['theme_mode']) }}
                            </p>
                            <p class="text-muted small mb-2">
                                <strong>Primary Color:</strong> {{ $themeSettings['primary_color'] }}
                            </p>
                            <p class="text-muted small mb-0">
                                <strong>Dark Mode:</strong> {{ $themeSettings['dark_mode_enabled'] ? 'Enabled' : 'Disabled' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Update color input text fields when color picker changes
document.querySelectorAll('input[type="color"]').forEach(function(colorInput) {
    colorInput.addEventListener('change', function() {
        const textInput = this.parentNode.querySelector('input[type="text"]');
        if (textInput) {
            textInput.value = this.value;
        }
        updatePreview();
    });
});

// Update preview colors in real-time
function updatePreview() {
    const primaryColor = document.getElementById('primary_color').value;
    const secondaryColor = document.getElementById('secondary_color').value;
    const successColor = document.getElementById('success_color').value;
    const warningColor = document.getElementById('warning_color').value;
    const dangerColor = document.getElementById('danger_color').value;
    const infoColor = document.getElementById('info_color').value;
    
    // Update CSS custom properties for preview
    document.documentElement.style.setProperty('--bs-primary', primaryColor);
    document.documentElement.style.setProperty('--bs-secondary', secondaryColor);
    document.documentElement.style.setProperty('--bs-success', successColor);
    document.documentElement.style.setProperty('--bs-warning', warningColor);
    document.documentElement.style.setProperty('--bs-danger', dangerColor);
    document.documentElement.style.setProperty('--bs-info', infoColor);
}

// Real-time preview updates
document.addEventListener('DOMContentLoaded', function() {
    updatePreview();
    initializeThemeMode();

    // Add event listeners for all color inputs
    const colorInputs = document.querySelectorAll('input[type="color"]');
    colorInputs.forEach(input => {
        input.addEventListener('change', updatePreview);
    });

    // Add event listeners for theme mode radio buttons
    const themeRadios = document.querySelectorAll('input[name="theme_mode"]');
    themeRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            toggleThemePreview();
            updatePreview();
        });
    });

    // Add event listener for custom CSS
    document.getElementById('custom_css').addEventListener('input', debounce(updatePreview, 500));
});

// Initialize theme mode based on current setting
function initializeThemeMode() {
    const currentMode = document.querySelector('input[name="theme_mode"]:checked')?.value || 'light';
    applyThemeMode(currentMode);
}

// Toggle theme preview
function toggleThemePreview() {
    const selectedMode = document.querySelector('input[name="theme_mode"]:checked')?.value || 'light';
    applyThemeMode(selectedMode);
}

// Apply theme mode to preview
function applyThemeMode(mode) {
    const previewContainer = document.querySelector('.preview-container');
    const body = document.body;
    
    if (mode === 'dark') {
        previewContainer.setAttribute('data-bs-theme', 'dark');
        previewContainer.style.backgroundColor = '#212529';
        previewContainer.style.color = '#ffffff';
    } else if (mode === 'auto') {
        // Check system preference
        const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
        if (prefersDark) {
            previewContainer.setAttribute('data-bs-theme', 'dark');
            previewContainer.style.backgroundColor = '#212529';
            previewContainer.style.color = '#ffffff';
        } else {
            previewContainer.setAttribute('data-bs-theme', 'light');
            previewContainer.style.backgroundColor = '#ffffff';
            previewContainer.style.color = '#000000';
        }
    } else {
        previewContainer.setAttribute('data-bs-theme', 'light');
        previewContainer.style.backgroundColor = '#ffffff';
        previewContainer.style.color = '#000000';
    }
}

// Debounce function for performance
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}
</script>
@endpush
@endsection