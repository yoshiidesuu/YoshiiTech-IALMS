@extends('layouts.admin')

@section('title', 'Maintenance Mode Management')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="bi bi-tools me-2"></i>Maintenance Mode Management
                    </h4>
                    <div class="d-flex align-items-center">
                        <span class="badge {{ $isMaintenanceMode ? 'bg-warning' : 'bg-success' }} me-2">
                            <i class="bi {{ $isMaintenanceMode ? 'bi-exclamation-triangle' : 'bi-check-circle' }} me-1"></i>
                            {{ $isMaintenanceMode ? 'Maintenance Active' : 'System Online' }}
                        </span>
                        @if($isMaintenanceMode)
                            <button type="button" class="btn btn-success btn-sm" id="disableMaintenanceBtn">
                                <i class="bi bi-play-circle me-1"></i>Bring Online
                            </button>
                        @else
                            <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#enableMaintenanceModal">
                                <i class="bi bi-pause-circle me-1"></i>Enable Maintenance
                            </button>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <!-- Current Status -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card border-{{ $isMaintenanceMode ? 'warning' : 'success' }}">
                                <div class="card-body text-center">
                                    <i class="bi {{ $isMaintenanceMode ? 'bi-exclamation-triangle-fill text-warning' : 'bi-check-circle-fill text-success' }}" style="font-size: 3rem;"></i>
                                    <h5 class="mt-3">{{ $isMaintenanceMode ? 'Maintenance Mode Active' : 'System Online' }}</h5>
                                    <p class="text-muted mb-0">
                                        {{ $isMaintenanceMode ? 'Users cannot access the application' : 'Application is accessible to all users' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card border-info">
                                <div class="card-body">
                                    <h6 class="card-title"><i class="bi bi-info-circle me-2"></i>Current Configuration</h6>
                                    <div class="small">
                                        <div class="mb-2">
                                            <strong>Message:</strong> 
                                            <span class="text-muted">{{ Str::limit($maintenanceConfig['message'] ?? 'Default message', 50) }}</span>
                                        </div>
                                        <div class="mb-2">
                                            <strong>Allowed IPs:</strong> 
                                            <span class="text-muted">{{ empty($maintenanceConfig['allowed_ips']) ? 'None' : count($maintenanceConfig['allowed_ips']) . ' IP(s)' }}</span>
                                        </div>
                                        <div class="mb-2">
                                            <strong>Retry After:</strong> 
                                            <span class="text-muted">{{ ($maintenanceConfig['retry_after'] ?? 3600) / 60 }} minutes</span>
                                        </div>
                                        @if(isset($maintenanceConfig['enabled_at']))
                                        <div class="mb-0">
                                            <strong>Enabled At:</strong> 
                                            <span class="text-muted">{{ \Carbon\Carbon::parse($maintenanceConfig['enabled_at'])->format('M j, Y g:i A') }}</span>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Configuration Form -->
                    <div class="card">
                        <div class="card-header">
                            <h6 class="mb-0"><i class="bi bi-gear me-2"></i>Maintenance Configuration</h6>
                        </div>
                        <div class="card-body">
                            <form id="maintenanceConfigForm">
                                @csrf
                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <label for="message" class="form-label">Maintenance Message</label>
                                        <textarea class="form-control tinymce-editor" id="message" name="message" rows="3" placeholder="Enter the message users will see during maintenance">{{ $maintenanceConfig['message'] ?? 'We are currently performing scheduled maintenance. Please check back soon.' }}</textarea>
                                        <div class="form-text">This message will be displayed to users when they try to access the application.</div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="allowed_ips" class="form-label">Allowed IP Addresses</label>
                                        <textarea class="form-control" id="allowed_ips" name="allowed_ips" rows="3" placeholder="192.168.1.1, 10.0.0.1">{{ is_array($maintenanceConfig['allowed_ips'] ?? []) ? implode(', ', $maintenanceConfig['allowed_ips']) : '' }}</textarea>
                                        <div class="form-text">Comma-separated list of IP addresses that can access the application during maintenance.</div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="retry_after" class="form-label">Retry After (seconds)</label>
                                        <input type="number" class="form-control" id="retry_after" name="retry_after" min="60" max="86400" value="{{ $maintenanceConfig['retry_after'] ?? 3600 }}">
                                        <div class="form-text">How long browsers should wait before retrying (60-86400 seconds).</div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="redirect_url" class="form-label">Redirect URL (Optional)</label>
                                        <input type="url" class="form-control" id="redirect_url" name="redirect_url" value="{{ $maintenanceConfig['redirect_url'] ?? '' }}" placeholder="https://status.example.com">
                                        <div class="form-text">Redirect users to an external status page.</div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="estimated_time" class="form-label">Estimated Duration</label>
                                        <input type="text" class="form-control" id="estimated_time" name="estimated_time" value="{{ $maintenanceConfig['estimated_time'] ?? '' }}" placeholder="2 hours">
                                        <div class="form-text">Estimated maintenance duration for user information.</div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="show_progress" name="show_progress" {{ ($maintenanceConfig['show_progress'] ?? false) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="show_progress">
                                                Show Progress Information
                                            </label>
                                            <div class="form-text">Display estimated time and progress information to users.</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-check-lg me-1"></i>Update Configuration
                                    </button>
                                    <button type="button" class="btn btn-secondary" id="resetConfigBtn">
                                        <i class="bi bi-arrow-clockwise me-1"></i>Reset to Defaults
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Enable Maintenance Modal -->
<div class="modal fade" id="enableMaintenanceModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-exclamation-triangle me-2"></i>Enable Maintenance Mode</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    <strong>Warning:</strong> Enabling maintenance mode will make the application inaccessible to regular users. Only administrators and whitelisted IP addresses will be able to access the system.
                </div>
                <p>Are you sure you want to enable maintenance mode with the current configuration?</p>
                <div class="bg-light p-3 rounded">
                    <h6>Current Settings:</h6>
                    <ul class="mb-0">
                        <li><strong>Message:</strong> <span id="previewMessage">{{ Str::limit($maintenanceConfig['message'] ?? 'Default message', 80) }}</span></li>
                        <li><strong>Allowed IPs:</strong> <span id="previewIPs">{{ empty($maintenanceConfig['allowed_ips']) ? 'None' : count($maintenanceConfig['allowed_ips']) . ' IP(s)' }}</span></li>
                        <li><strong>Retry After:</strong> <span id="previewRetry">{{ ($maintenanceConfig['retry_after'] ?? 3600) / 60 }} minutes</span></li>
                    </ul>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-warning" id="confirmEnableBtn">
                    <i class="bi bi-pause-circle me-1"></i>Enable Maintenance Mode
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Update configuration
    $('#maintenanceConfigForm').submit(function(e) {
        e.preventDefault();
        
        const submitBtn = $(this).find('button[type="submit"]');
        const originalText = submitBtn.html();
        submitBtn.prop('disabled', true).html('<i class="bi bi-hourglass-split me-1"></i>Updating...');
        
        $.ajax({
            url: '{{ route("admin.maintenance.updateConfig") }}',
            method: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                if (response.success) {
                    showAlert('success', response.message);
                    updatePreviewInfo();
                } else {
                    showAlert('danger', response.message);
                }
            },
            error: function(xhr) {
                const response = xhr.responseJSON;
                if (response && response.errors) {
                    let errorMessage = 'Validation errors:\n';
                    Object.keys(response.errors).forEach(key => {
                        errorMessage += `- ${response.errors[key].join(', ')}\n`;
                    });
                    showAlert('danger', errorMessage);
                } else {
                    showAlert('danger', 'Failed to update configuration');
                }
            },
            complete: function() {
                submitBtn.prop('disabled', false).html(originalText);
            }
        });
    });
    
    // Enable maintenance mode
    $('#confirmEnableBtn').click(function() {
        const btn = $(this);
        const originalText = btn.html();
        btn.prop('disabled', true).html('<i class="bi bi-hourglass-split me-1"></i>Enabling...');
        
        $.ajax({
            url: '{{ route("admin.maintenance.enable") }}',
            method: 'POST',
            data: $('#maintenanceConfigForm').serialize(),
            success: function(response) {
                if (response.success) {
                    showAlert('success', response.message);
                    $('#enableMaintenanceModal').modal('hide');
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                } else {
                    showAlert('danger', response.message);
                }
            },
            error: function(xhr) {
                const response = xhr.responseJSON;
                if (response && response.errors) {
                    let errorMessage = 'Validation errors:\n';
                    Object.keys(response.errors).forEach(key => {
                        errorMessage += `- ${response.errors[key].join(', ')}\n`;
                    });
                    showAlert('danger', errorMessage);
                } else {
                    showAlert('danger', 'Failed to enable maintenance mode');
                }
            },
            complete: function() {
                btn.prop('disabled', false).html(originalText);
            }
        });
    });
    
    // Disable maintenance mode
    $('#disableMaintenanceBtn').click(function() {
        const btn = $(this);
        const originalText = btn.html();
        btn.prop('disabled', true).html('<i class="bi bi-hourglass-split me-1"></i>Disabling...');
        
        $.ajax({
            url: '{{ route("admin.maintenance.disable") }}',
            method: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    showAlert('success', response.message);
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                } else {
                    showAlert('danger', response.message);
                }
            },
            error: function(xhr) {
                showAlert('danger', 'Failed to disable maintenance mode');
            },
            complete: function() {
                btn.prop('disabled', false).html(originalText);
            }
        });
    });
    
    // Reset configuration
    $('#resetConfigBtn').click(function() {
        if (confirm('Are you sure you want to reset the configuration to defaults?')) {
            $('#message').val('We are currently performing scheduled maintenance. Please check back soon.');
            $('#allowed_ips').val('');
            $('#retry_after').val('3600');
            $('#redirect_url').val('');
            $('#estimated_time').val('');
            $('#show_progress').prop('checked', false);
            updatePreviewInfo();
        }
    });
    
    // Update preview info when form changes
    $('#maintenanceConfigForm input, #maintenanceConfigForm textarea, #maintenanceConfigForm select').on('input change', function() {
        updatePreviewInfo();
    });
    
    function updatePreviewInfo() {
        const message = $('#message').val() || 'Default maintenance message';
        const ips = $('#allowed_ips').val().split(',').filter(ip => ip.trim()).length;
        const retry = Math.floor($('#retry_after').val() / 60) || 60;
        
        $('#previewMessage').text(message.length > 80 ? message.substring(0, 80) + '...' : message);
        $('#previewIPs').text(ips > 0 ? ips + ' IP(s)' : 'None');
        $('#previewRetry').text(retry + ' minutes');
    }
    
    function showAlert(type, message) {
        const alertHtml = `
            <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
        
        // Remove existing alerts
        $('.alert').remove();
        
        // Add new alert at the top of the container
        $('.container-fluid').prepend(alertHtml);
        
        // Auto-dismiss success alerts
        if (type === 'success') {
            setTimeout(() => {
                $('.alert-success').fadeOut();
            }, 3000);
        }
    }
});
</script>
@endpush