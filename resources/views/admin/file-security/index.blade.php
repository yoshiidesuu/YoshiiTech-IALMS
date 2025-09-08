@extends('layouts.admin')

@section('title', 'File Security Management')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0 text-gray-800">File Security Management</h1>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-info" id="refreshStats">
                        <i class="bi bi-arrow-clockwise me-1"></i>Refresh Stats
                    </button>
                    <button type="button" class="btn btn-warning" id="viewQuarantine">
                        <i class="bi bi-shield-exclamation me-1"></i>View Quarantine
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Security Status Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Quarantined Files</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="quarantinedCount">0</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-shield-exclamation fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Uploads Today</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="uploadsToday">0</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-cloud-upload fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Threats Blocked</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="threatsBlocked">0</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-shield-x fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Quarantine Size</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="quarantineSize">0 MB</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-hdd fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- File Security Configuration -->
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="bi bi-gear me-2"></i>File Security Configuration
                    </h6>
                </div>
                <div class="card-body">
                    <form id="fileSecurityForm">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="maxFileSize" class="form-label">Maximum File Size (KB)</label>
                                    <input type="number" class="form-control" id="maxFileSize" name="max_file_size" 
                                           value="{{ $config['max_file_size'] }}" min="1" max="102400" required>
                                    <div class="form-text">Maximum allowed file size in kilobytes (1-102400 KB)</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="allowedExtensions" class="form-label">Allowed Extensions</label>
                                    <input type="text" class="form-control" id="allowedExtensions" name="allowed_extensions" 
                                           value="{{ implode(', ', $config['allowed_extensions']) }}" required>
                                    <div class="form-text">Comma-separated list of allowed file extensions</div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="blockedExtensions" class="form-label">Blocked Extensions</label>
                                    <input type="text" class="form-control" id="blockedExtensions" name="blocked_extensions" 
                                           value="{{ implode(', ', $config['blocked_extensions']) }}">
                                    <div class="form-text">Comma-separated list of blocked file extensions</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="quarantineDays" class="form-label">Quarantine Days</label>
                                    <input type="number" class="form-control" id="quarantineDays" name="quarantine_days" 
                                           value="{{ $config['quarantine_days'] }}" min="1" max="365">
                                    <div class="form-text">Days to keep files in quarantine before auto-deletion</div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="maxUploadsPerUser" class="form-label">Max Uploads Per User</label>
                                    <input type="number" class="form-control" id="maxUploadsPerUser" name="max_uploads_per_user" 
                                           value="{{ $config['max_uploads_per_user'] }}" min="1">
                                    <div class="form-text">Maximum number of files a user can upload</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="maxStoragePerUser" class="form-label">Max Storage Per User (KB)</label>
                                    <input type="number" class="form-control" id="maxStoragePerUser" name="max_storage_per_user" 
                                           value="{{ $config['max_storage_per_user'] }}" min="1">
                                    <div class="form-text">Maximum storage space per user in kilobytes</div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="mb-3">
                                    <h6 class="text-primary mb-3">Security Options</h6>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" id="scanUploads" name="scan_uploads" 
                                               {{ $config['scan_uploads'] ? 'checked' : '' }}>
                                        <label class="form-check-label" for="scanUploads">
                                            Scan uploaded files for threats
                                        </label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" id="quarantineSuspicious" name="quarantine_suspicious" 
                                               {{ $config['quarantine_suspicious'] ? 'checked' : '' }}>
                                        <label class="form-check-label" for="quarantineSuspicious">
                                            Quarantine suspicious files
                                        </label>
                                    </div>
                                    <div class="form-check mb-3">
                                        <input class="form-check-input" type="checkbox" id="autoDeleteQuarantine" name="auto_delete_quarantine" 
                                               {{ $config['auto_delete_quarantine'] ? 'checked' : '' }}>
                                        <label class="form-check-label" for="autoDeleteQuarantine">
                                            Auto-delete quarantined files after specified days
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle me-1"></i>Update Configuration
                            </button>
                            <button type="button" class="btn btn-secondary" id="resetDefaults">
                                <i class="bi bi-arrow-counterclockwise me-1"></i>Reset to Defaults
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- File Upload Test -->
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="bi bi-upload me-2"></i>Test File Upload
                    </h6>
                </div>
                <div class="card-body">
                    <form id="testUploadForm" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="testFile" class="form-label">Select Test File</label>
                            <input type="file" class="form-control" id="testFile" name="test_file" required>
                            <div class="form-text">Upload a file to test current security rules</div>
                        </div>
                        <button type="submit" class="btn btn-info w-100">
                            <i class="bi bi-shield-check me-1"></i>Test Upload
                        </button>
                    </form>

                    <div id="testResult" class="mt-3" style="display: none;">
                        <div class="card">
                            <div class="card-body">
                                <h6 class="card-title">Test Result</h6>
                                <div id="testResultContent"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="bi bi-lightning me-2"></i>Quick Actions
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-outline-warning" id="cleanQuarantine">
                            <i class="bi bi-trash me-1"></i>Clean Quarantine
                        </button>
                        <button type="button" class="btn btn-outline-info" id="exportConfig">
                            <i class="bi bi-download me-1"></i>Export Config
                        </button>
                        <button type="button" class="btn btn-outline-secondary" id="viewLogs">
                            <i class="bi bi-journal-text me-1"></i>View Security Logs
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quarantine Modal -->
<div class="modal fade" id="quarantineModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-shield-exclamation me-2"></i>Quarantined Files
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="quarantineList">
                    <div class="text-center">
                        <div class="spinner-border" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-danger" id="clearAllQuarantine">
                    <i class="bi bi-trash me-1"></i>Clear All
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Load initial stats
    loadSecurityStats();

    // File security form submission
    $('#fileSecurityForm').on('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const submitBtn = $(this).find('button[type="submit"]');
        const originalText = submitBtn.html();
        
        submitBtn.prop('disabled', true).html('<i class="bi bi-hourglass-split me-1"></i>Updating...');
        
        $.ajax({
            url: '{{ route("admin.file-security.updateConfig") }}',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    showAlert('success', response.message);
                    loadSecurityStats();
                } else {
                    showAlert('danger', response.message);
                }
            },
            error: function(xhr) {
                const response = xhr.responseJSON;
                if (response && response.errors) {
                    let errorMsg = 'Validation errors:\n';
                    Object.keys(response.errors).forEach(key => {
                        errorMsg += `- ${response.errors[key][0]}\n`;
                    });
                    showAlert('danger', errorMsg);
                } else {
                    showAlert('danger', 'Failed to update configuration');
                }
            },
            complete: function() {
                submitBtn.prop('disabled', false).html(originalText);
            }
        });
    });

    // Test file upload
    $('#testUploadForm').on('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const submitBtn = $(this).find('button[type="submit"]');
        const originalText = submitBtn.html();
        
        submitBtn.prop('disabled', true).html('<i class="bi bi-hourglass-split me-1"></i>Testing...');
        
        $.ajax({
            url: '{{ route("admin.file-security.testValidation") }}',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    displayTestResult(response.validation_result, response.file_info);
                } else {
                    showAlert('danger', response.message);
                }
            },
            error: function(xhr) {
                showAlert('danger', 'Failed to test file upload');
            },
            complete: function() {
                submitBtn.prop('disabled', false).html(originalText);
            }
        });
    });

    // View quarantine
    $('#viewQuarantine').on('click', function() {
        loadQuarantinedFiles();
        $('#quarantineModal').modal('show');
    });

    // Refresh stats
    $('#refreshStats').on('click', function() {
        loadSecurityStats();
    });

    // Reset defaults
    $('#resetDefaults').on('click', function() {
        if (confirm('Are you sure you want to reset to default configuration?')) {
            resetToDefaults();
        }
    });

    // Clean quarantine
    $('#cleanQuarantine').on('click', function() {
        if (confirm('Are you sure you want to delete all quarantined files?')) {
            cleanQuarantine();
        }
    });

    function loadSecurityStats() {
        $.ajax({
            url: '{{ route("admin.file-security.status") }}',
            method: 'GET',
            success: function(response) {
                if (response.success) {
                    updateStatsDisplay(response.stats);
                }
            },
            error: function() {
                console.error('Failed to load security stats');
            }
        });
    }

    function updateStatsDisplay(stats) {
        $('#quarantinedCount').text(stats.quarantined_files);
        $('#uploadsToday').text(stats.total_uploads_today);
        $('#threatsBlocked').text(stats.threats_blocked_today);
        $('#quarantineSize').text(formatBytes(stats.quarantine_size));
    }

    function displayTestResult(result, fileInfo) {
        let html = `
            <div class="mb-3">
                <strong>File:</strong> ${fileInfo.name}<br>
                <strong>Size:</strong> ${formatBytes(fileInfo.size)}<br>
                <strong>Extension:</strong> ${fileInfo.extension}<br>
                <strong>MIME Type:</strong> ${fileInfo.mime_type}
            </div>
        `;

        if (result.valid) {
            html += '<div class="alert alert-success"><i class="bi bi-check-circle me-2"></i>File passed validation!</div>';
        } else {
            html += '<div class="alert alert-danger"><i class="bi bi-x-circle me-2"></i>File failed validation</div>';
            if (result.errors.length > 0) {
                html += '<div class="mb-2"><strong>Errors:</strong><ul>';
                result.errors.forEach(error => {
                    html += `<li>${error}</li>`;
                });
                html += '</ul></div>';
            }
        }

        if (result.warnings.length > 0) {
            html += '<div class="alert alert-warning"><strong>Warnings:</strong><ul>';
            result.warnings.forEach(warning => {
                html += `<li>${warning}</li>`;
            });
            html += '</ul></div>';
        }

        $('#testResultContent').html(html);
        $('#testResult').show();
    }

    function loadQuarantinedFiles() {
        $.ajax({
            url: '{{ route("admin.file-security.quarantined") }}',
            method: 'GET',
            success: function(response) {
                if (response.success) {
                    displayQuarantinedFiles(response.files);
                } else {
                    $('#quarantineList').html('<div class="alert alert-danger">Failed to load quarantined files</div>');
                }
            },
            error: function() {
                $('#quarantineList').html('<div class="alert alert-danger">Failed to load quarantined files</div>');
            }
        });
    }

    function displayQuarantinedFiles(files) {
        if (files.length === 0) {
            $('#quarantineList').html('<div class="alert alert-info">No files in quarantine</div>');
            return;
        }

        let html = '<div class="table-responsive"><table class="table table-striped"><thead><tr><th>File Name</th><th>Size</th><th>Quarantined</th><th>Days</th><th>Action</th></tr></thead><tbody>';
        
        files.forEach(file => {
            html += `
                <tr>
                    <td>${file.name}</td>
                    <td>${formatBytes(file.size)}</td>
                    <td>${file.quarantined_at}</td>
                    <td>${file.days_in_quarantine}</td>
                    <td>
                        <button class="btn btn-sm btn-danger delete-quarantine" data-path="${file.path}">
                            <i class="bi bi-trash"></i>
                        </button>
                    </td>
                </tr>
            `;
        });
        
        html += '</tbody></table></div>';
        $('#quarantineList').html(html);

        // Bind delete buttons
        $('.delete-quarantine').on('click', function() {
            const filePath = $(this).data('path');
            deleteQuarantinedFile(filePath, $(this).closest('tr'));
        });
    }

    function deleteQuarantinedFile(filePath, row) {
        $.ajax({
            url: '{{ route("admin.file-security.deleteQuarantined") }}',
            method: 'POST',
            data: {
                file_path: filePath,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    row.remove();
                    showAlert('success', response.message);
                    loadSecurityStats();
                } else {
                    showAlert('danger', response.message);
                }
            },
            error: function() {
                showAlert('danger', 'Failed to delete quarantined file');
            }
        });
    }

    function resetToDefaults() {
        $('#maxFileSize').val(10240);
        $('#allowedExtensions').val('jpg, jpeg, png, gif, pdf, doc, docx, txt');
        $('#blockedExtensions').val('exe, bat, cmd, scr, pif, com');
        $('#quarantineDays').val(30);
        $('#maxUploadsPerUser').val(100);
        $('#maxStoragePerUser').val(1048576);
        $('#scanUploads').prop('checked', true);
        $('#quarantineSuspicious').prop('checked', true);
        $('#autoDeleteQuarantine').prop('checked', true);
    }

    function cleanQuarantine() {
        // This would call an endpoint to clean quarantine
        showAlert('info', 'Quarantine cleaning functionality would be implemented here');
    }

    function formatBytes(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
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
        
        // Auto-dismiss after 5 seconds
        setTimeout(() => {
            $('.alert').fadeOut();
        }, 5000);
    }
});
</script>
@endpush