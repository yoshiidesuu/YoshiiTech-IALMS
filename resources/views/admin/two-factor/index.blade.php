@extends('layouts.admin')

@section('page-title', 'Two-Factor Authentication')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-shield-lock me-2"></i>Two-Factor Authentication (2FA)
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Main Content -->
                        <div class="col-lg-8">
                            <!-- Status Display -->
                            <div class="mb-4">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="status-indicator me-3" id="statusIndicator">
                                        @if($isEnabled)
                                            <span class="badge bg-success fs-6">
                                                <i class="bi bi-check-circle me-1"></i>Enabled
                                            </span>
                                        @else
                                            <span class="badge bg-warning fs-6">
                                                <i class="bi bi-exclamation-triangle me-1"></i>Disabled
                                            </span>
                                        @endif
                                    </div>
                                    <div>
                                        <h6 class="mb-1">Current Status</h6>
                                        <small class="text-muted" id="statusDescription">
                                            @if($isEnabled)
                                                Two-factor authentication is active and protecting your account.
                                            @else
                                                Your account is not protected by two-factor authentication.
                                            @endif
                                        </small>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Enable 2FA Section -->
                            <div id="enableSection" class="{{ $isEnabled ? 'd-none' : '' }}">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6 class="text-primary mb-3">
                                            <i class="bi bi-plus-circle me-2"></i>Enable Two-Factor Authentication
                                        </h6>
                                        <p class="mb-3">
                                            Secure your account with an additional layer of protection. You'll need a mobile app like 
                                            <strong>Google Authenticator</strong> or <strong>Authy</strong> to generate verification codes.
                                        </p>
                                        <button type="button" class="btn btn-success" id="enableTwoFactorBtn">
                                            <i class="bi bi-shield-plus me-2"></i>Enable Two-Factor Authentication
                                        </button>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Setup Process -->
                            <div id="setupSection" class="d-none">
                                <div class="card border-primary">
                                    <div class="card-header bg-primary text-white">
                                        <h6 class="mb-0"><i class="bi bi-gear me-2"></i>Setup Two-Factor Authentication</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <h6 class="text-primary mb-3">Step 1: Scan QR Code</h6>
                                                <div class="text-center mb-3">
                                                    <div id="qrCodeContainer" class="p-3 bg-white border rounded">
                                                        <div class="spinner-border text-primary" role="status">
                                                            <span class="visually-hidden">Loading...</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="alert alert-info small">
                                                    <strong>Manual Entry:</strong><br>
                                                    If you can't scan the QR code, enter this secret key manually:
                                                    <div class="mt-2">
                                                        <code id="secretKey" class="user-select-all"></code>
                                                        <button type="button" class="btn btn-sm btn-outline-secondary ms-2" id="copySecretBtn">
                                                            <i class="bi bi-clipboard"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <h6 class="text-primary mb-3">Step 2: Verify Setup</h6>
                                                <form id="confirmTwoFactorForm">
                                                    @csrf
                                                    <div class="mb-3">
                                                        <label for="verification_code" class="form-label">Verification Code</label>
                                                        <input type="text" class="form-control text-center" id="verification_code" 
                                                               name="code" maxlength="6" placeholder="000000" required>
                                                        <small class="text-muted">Enter the 6-digit code from your authenticator app</small>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="confirm_password" class="form-label">Confirm Password</label>
                                                        <input type="password" class="form-control" id="confirm_password" 
                                                               name="password" required placeholder="Enter your current password">
                                                    </div>
                                                    <div class="d-flex gap-2">
                                                        <button type="submit" class="btn btn-primary">
                                                            <i class="bi bi-check-circle me-2"></i>Confirm Setup
                                                        </button>
                                                        <button type="button" class="btn btn-secondary" id="cancelSetupBtn">
                                                            <i class="bi bi-x-circle me-2"></i>Cancel
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Backup Codes -->
                                <div class="card mt-3 border-warning" id="backupCodesCard" style="display: none;">
                                    <div class="card-header bg-warning text-dark">
                                        <h6 class="mb-0"><i class="bi bi-key me-2"></i>Backup Recovery Codes</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="alert alert-warning">
                                            <strong>Important:</strong> Save these backup codes in a secure location. 
                                            You can use them to access your account if you lose your authenticator device.
                                        </div>
                                        <div class="row" id="backupCodesList">
                                            <!-- Backup codes will be inserted here -->
                                        </div>
                                        <div class="mt-3">
                                            <button type="button" class="btn btn-outline-primary" id="downloadBackupCodesBtn">
                                                <i class="bi bi-download me-2"></i>Download Codes
                                            </button>
                                            <button type="button" class="btn btn-outline-secondary" id="printBackupCodesBtn">
                                                <i class="bi bi-printer me-2"></i>Print Codes
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Manage 2FA Section -->
                            <div id="manageSection" class="{{ $isEnabled ? '' : 'd-none' }}">
                                <div class="card">
                                    <div class="card-body">
                                        <h6 class="text-primary mb-3">
                                            <i class="bi bi-gear me-2"></i>Manage Two-Factor Authentication
                                        </h6>
                                        
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="d-grid gap-2">
                                                    <button type="button" class="btn btn-outline-primary" id="regenerateBackupCodesBtn">
                                                        <i class="bi bi-arrow-clockwise me-2"></i>Generate New Backup Codes
                                                    </button>
                                                    <button type="button" class="btn btn-outline-danger" id="disableTwoFactorBtn">
                                                        <i class="bi bi-shield-x me-2"></i>Disable Two-Factor Authentication
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="alert alert-info small">
                                                    <i class="bi bi-info-circle me-2"></i>
                                                    <strong>Security Tip:</strong> Regularly generate new backup codes and 
                                                    store them securely. Never share your codes with anyone.
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Information Panel -->
                        <div class="col-lg-4">
                            <div class="card bg-light">
                                <div class="card-header">
                                    <h6 class="card-title mb-0"><i class="bi bi-info-circle me-2"></i>About Two-Factor Authentication</h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <h6 class="text-primary">What is 2FA?</h6>
                                        <p class="small mb-0">
                                            Two-factor authentication adds an extra layer of security to your account by requiring 
                                            both your password and a verification code from your mobile device.
                                        </p>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <h6 class="text-primary">Recommended Apps</h6>
                                        <ul class="small mb-0">
                                            <li><strong>Google Authenticator</strong> - Free, reliable</li>
                                            <li><strong>Authy</strong> - Multi-device sync</li>
                                            <li><strong>Microsoft Authenticator</strong> - Enterprise features</li>
                                            <li><strong>1Password</strong> - Password manager integration</li>
                                        </ul>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <h6 class="text-primary">Security Benefits</h6>
                                        <ul class="small mb-0">
                                            <li>Protection against password theft</li>
                                            <li>Prevents unauthorized access</li>
                                            <li>Meets compliance requirements</li>
                                            <li>Peace of mind for sensitive data</li>
                                        </ul>
                                    </div>
                                    
                                    <div class="alert alert-warning small">
                                        <i class="bi bi-exclamation-triangle me-2"></i>
                                        <strong>Important:</strong> Keep your backup codes safe! They're your only way to 
                                        access your account if you lose your authenticator device.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Disable 2FA Modal -->
<div class="modal fade" id="disableTwoFactorModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-danger">
                    <i class="bi bi-shield-x me-2"></i>Disable Two-Factor Authentication
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger">
                    <strong>Warning:</strong> Disabling two-factor authentication will make your account less secure.
                </div>
                <form id="disableTwoFactorForm">
                    @csrf
                    <div class="mb-3">
                        <label for="disable_password" class="form-label">Current Password</label>
                        <input type="password" class="form-control" id="disable_password" name="password" required>
                    </div>
                    <div class="mb-3">
                        <label for="disable_code" class="form-label">Verification Code</label>
                        <input type="text" class="form-control text-center" id="disable_code" 
                               name="code" maxlength="6" placeholder="000000" required>
                        <small class="text-muted">Enter the current code from your authenticator app</small>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDisableBtn">
                    <i class="bi bi-shield-x me-2"></i>Disable 2FA
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Generate Backup Codes Modal -->
<div class="modal fade" id="generateBackupCodesModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-key me-2"></i>Generate New Backup Codes
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning">
                    <strong>Important:</strong> Generating new backup codes will invalidate all existing codes.
                </div>
                <form id="generateBackupCodesForm">
                    @csrf
                    <div class="mb-3">
                        <label for="backup_password" class="form-label">Current Password</label>
                        <input type="password" class="form-control" id="backup_password" name="password" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="confirmGenerateBackupCodesBtn">
                    <i class="bi bi-key me-2"></i>Generate Codes
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
$(document).ready(function() {
    let currentSecret = '';
    let currentBackupCodes = [];
    
    // Enable Two-Factor Authentication
    $('#enableTwoFactorBtn').click(function() {
        const btn = $(this);
        const originalText = btn.html();
        
        btn.prop('disabled', true).html('<i class="bi bi-hourglass-split me-2"></i>Setting up...');
        
        $.ajax({
            url: '{{ route("admin.two-factor.enable") }}',
            method: 'POST',
            data: { _token: '{{ csrf_token() }}' },
            success: function(response) {
                if (response.success) {
                    currentSecret = response.secret;
                    currentBackupCodes = response.backup_codes;
                    
                    // Show QR code
                    if (response.qr_code.startsWith('data:image')) {
                        $('#qrCodeContainer').html(`<img src="${response.qr_code}" alt="QR Code" class="img-fluid">`);
                    } else {
                        $('#qrCodeContainer').html(`<p class="text-muted">QR Code: ${response.qr_code}</p>`);
                    }
                    
                    $('#secretKey').text(response.secret);
                    
                    // Show setup section
                    $('#enableSection').addClass('d-none');
                    $('#setupSection').removeClass('d-none');
                } else {
                    showAlert('danger', response.message || 'Failed to enable two-factor authentication');
                }
            },
            error: function() {
                showAlert('danger', 'Failed to enable two-factor authentication');
            },
            complete: function() {
                btn.prop('disabled', false).html(originalText);
            }
        });
    });
    
    // Copy secret key
    $('#copySecretBtn').click(function() {
        navigator.clipboard.writeText(currentSecret).then(function() {
            showAlert('success', 'Secret key copied to clipboard!');
        });
    });
    
    // Confirm Two-Factor Setup
    $('#confirmTwoFactorForm').submit(function(e) {
        e.preventDefault();
        
        const submitBtn = $(this).find('button[type="submit"]');
        const originalText = submitBtn.html();
        
        submitBtn.prop('disabled', true).html('<i class="bi bi-hourglass-split me-2"></i>Verifying...');
        
        $.ajax({
            url: '{{ route("admin.two-factor.confirm") }}',
            method: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                if (response.success) {
                    showAlert('success', response.message);
                    
                    // Show backup codes
                    displayBackupCodes(currentBackupCodes);
                    $('#backupCodesCard').show();
                    
                    // Update UI after successful setup
                    setTimeout(function() {
                        location.reload();
                    }, 3000);
                } else {
                    showAlert('danger', response.message || 'Invalid verification code');
                }
            },
            error: function(xhr) {
                const message = xhr.responseJSON?.message || 'Failed to confirm setup';
                showAlert('danger', message);
            },
            complete: function() {
                submitBtn.prop('disabled', false).html(originalText);
            }
        });
    });
    
    // Cancel setup
    $('#cancelSetupBtn').click(function() {
        if (confirm('Are you sure you want to cancel the setup? You will need to start over.')) {
            location.reload();
        }
    });
    
    // Disable Two-Factor Authentication
    $('#disableTwoFactorBtn').click(function() {
        $('#disableTwoFactorModal').modal('show');
    });
    
    $('#confirmDisableBtn').click(function() {
        const btn = $(this);
        const originalText = btn.html();
        
        btn.prop('disabled', true).html('<i class="bi bi-hourglass-split me-2"></i>Disabling...');
        
        $.ajax({
            url: '{{ route("admin.two-factor.disable") }}',
            method: 'POST',
            data: $('#disableTwoFactorForm').serialize(),
            success: function(response) {
                if (response.success) {
                    showAlert('success', response.message);
                    $('#disableTwoFactorModal').modal('hide');
                    setTimeout(function() {
                        location.reload();
                    }, 2000);
                } else {
                    showAlert('danger', response.message || 'Failed to disable two-factor authentication');
                }
            },
            error: function(xhr) {
                const message = xhr.responseJSON?.message || 'Failed to disable two-factor authentication';
                showAlert('danger', message);
            },
            complete: function() {
                btn.prop('disabled', false).html(originalText);
            }
        });
    });
    
    // Generate new backup codes
    $('#regenerateBackupCodesBtn').click(function() {
        $('#generateBackupCodesModal').modal('show');
    });
    
    $('#confirmGenerateBackupCodesBtn').click(function() {
        const btn = $(this);
        const originalText = btn.html();
        
        btn.prop('disabled', true).html('<i class="bi bi-hourglass-split me-2"></i>Generating...');
        
        $.ajax({
            url: '{{ route("admin.two-factor.backup-codes") }}',
            method: 'POST',
            data: $('#generateBackupCodesForm').serialize(),
            success: function(response) {
                if (response.success) {
                    showAlert('success', response.message);
                    displayBackupCodes(response.backup_codes);
                    $('#generateBackupCodesModal').modal('hide');
                    $('#backupCodesCard').show();
                } else {
                    showAlert('danger', response.message || 'Failed to generate backup codes');
                }
            },
            error: function(xhr) {
                const message = xhr.responseJSON?.message || 'Failed to generate backup codes';
                showAlert('danger', message);
            },
            complete: function() {
                btn.prop('disabled', false).html(originalText);
            }
        });
    });
    
    // Display backup codes
    function displayBackupCodes(codes) {
        let html = '';
        codes.forEach(function(code, index) {
            html += `<div class="col-md-6 mb-2"><code class="user-select-all">${code}</code></div>`;
        });
        $('#backupCodesList').html(html);
        currentBackupCodes = codes;
    }
    
    // Download backup codes
    $('#downloadBackupCodesBtn').click(function() {
        if (currentBackupCodes.length === 0) return;
        
        const content = `Two-Factor Authentication Backup Codes\n\nGenerated: ${new Date().toLocaleString()}\nAccount: {{ Auth::user()->email }}\n\n${currentBackupCodes.join('\n')}\n\nKeep these codes safe and secure!`;
        const blob = new Blob([content], { type: 'text/plain' });
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = '2fa-backup-codes.txt';
        a.click();
        window.URL.revokeObjectURL(url);
    });
    
    // Print backup codes
    $('#printBackupCodesBtn').click(function() {
        if (currentBackupCodes.length === 0) return;
        
        const printContent = `
            <h3>Two-Factor Authentication Backup Codes</h3>
            <p><strong>Generated:</strong> ${new Date().toLocaleString()}</p>
            <p><strong>Account:</strong> {{ Auth::user()->email }}</p>
            <div style="margin: 20px 0;">
                ${currentBackupCodes.map(code => `<div style="margin: 5px 0; font-family: monospace; font-size: 14px;">${code}</div>`).join('')}
            </div>
            <p><em>Keep these codes safe and secure!</em></p>
        `;
        
        const printWindow = window.open('', '_blank');
        printWindow.document.write(`
            <html>
                <head><title>2FA Backup Codes</title></head>
                <body>${printContent}</body>
            </html>
        `);
        printWindow.document.close();
        printWindow.print();
    });
    
    // Auto-format verification code input
    $('#verification_code, #disable_code').on('input', function() {
        this.value = this.value.replace(/\D/g, '').substring(0, 6);
    });
    
    // Show alert function
    function showAlert(type, message) {
        const alertHtml = `
            <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
        
        // Remove existing alerts
        $('.alert').not('.alert-info, .alert-warning').remove();
        
        // Add new alert at the top of the card body
        $('.card-body').first().prepend(alertHtml);
        
        // Auto-dismiss success alerts after 5 seconds
        if (type === 'success') {
            setTimeout(() => {
                $('.alert-success').fadeOut();
            }, 5000);
        }
    }
});
</script>
@endpush