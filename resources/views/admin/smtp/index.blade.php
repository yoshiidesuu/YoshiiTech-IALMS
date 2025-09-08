@extends('layouts.admin')

@section('page-title', 'SMTP Configuration')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-envelope-gear me-2"></i>SMTP Configuration
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Configuration Form -->
                        <div class="col-lg-8">
                            <form id="smtpConfigForm">
                                @csrf
                                
                                <!-- Mail Driver Selection -->
                                <div class="mb-4">
                                    <h6 class="text-primary mb-3"><i class="bi bi-gear me-2"></i>Mail Driver</h6>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label for="default_mailer" class="form-label">Default Mailer</label>
                                            <select class="form-select" id="default_mailer" name="default_mailer" required>
                                                <option value="smtp" {{ $smtpConfig['default_mailer'] === 'smtp' ? 'selected' : '' }}>SMTP</option>
                                                <option value="log" {{ $smtpConfig['default_mailer'] === 'log' ? 'selected' : '' }}>Log (Development)</option>
                                                <option value="array" {{ $smtpConfig['default_mailer'] === 'array' ? 'selected' : '' }}>Array (Testing)</option>
                                            </select>
                                            <small class="text-muted">Choose how emails should be sent</small>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- SMTP Server Settings -->
                                <div class="mb-4" id="smtpSettings">
                                    <h6 class="text-primary mb-3"><i class="bi bi-server me-2"></i>SMTP Server Settings</h6>
                                    <div class="row">
                                        <div class="col-md-8">
                                            <label for="host" class="form-label">SMTP Host *</label>
                                            <input type="text" class="form-control" id="host" name="host" 
                                                   value="{{ $smtpConfig['host'] }}" required
                                                   placeholder="e.g., smtp.gmail.com, smtp.mailgun.org">
                                            <small class="text-muted">Your SMTP server hostname</small>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="port" class="form-label">Port *</label>
                                            <input type="number" class="form-control" id="port" name="port" 
                                                   value="{{ $smtpConfig['port'] }}" required min="1" max="65535"
                                                   placeholder="587">
                                            <small class="text-muted">Common: 587, 465, 25</small>
                                        </div>
                                    </div>
                                    
                                    <div class="row mt-3">
                                        <div class="col-md-6">
                                            <label for="encryption" class="form-label">Encryption</label>
                                            <select class="form-select" id="encryption" name="encryption">
                                                <option value="" {{ empty($smtpConfig['encryption']) ? 'selected' : '' }}>None</option>
                                                <option value="tls" {{ $smtpConfig['encryption'] === 'tls' ? 'selected' : '' }}>TLS</option>
                                                <option value="ssl" {{ $smtpConfig['encryption'] === 'ssl' ? 'selected' : '' }}>SSL</option>
                                            </select>
                                            <small class="text-muted">Recommended: TLS for port 587</small>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Authentication -->
                                <div class="mb-4" id="smtpAuth">
                                    <h6 class="text-primary mb-3"><i class="bi bi-shield-lock me-2"></i>Authentication</h6>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label for="username" class="form-label">Username</label>
                                            <input type="text" class="form-control" id="username" name="username" 
                                                   value="{{ $smtpConfig['username'] }}"
                                                   placeholder="your-email@domain.com">
                                            <small class="text-muted">SMTP authentication username</small>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="password" class="form-label">Password</label>
                                            <div class="input-group">
                                                <input type="password" class="form-control" id="password" name="password" 
                                                       value="{{ $smtpConfig['password'] ? '••••••••' : '' }}"
                                                       placeholder="Enter password">
                                                <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                                    <i class="bi bi-eye"></i>
                                                </button>
                                            </div>
                                            <small class="text-muted">SMTP authentication password</small>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Sender Information -->
                                <div class="mb-4">
                                    <h6 class="text-primary mb-3"><i class="bi bi-person-badge me-2"></i>Sender Information</h6>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label for="from_address" class="form-label">From Email Address *</label>
                                            <input type="email" class="form-control" id="from_address" name="from_address" 
                                                   value="{{ $smtpConfig['from_address'] }}" required
                                                   placeholder="noreply@yourdomain.com">
                                            <small class="text-muted">Default sender email address</small>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="from_name" class="form-label">From Name *</label>
                                            <input type="text" class="form-control" id="from_name" name="from_name" 
                                                   value="{{ $smtpConfig['from_name'] }}" required
                                                   placeholder="Your Organization">
                                            <small class="text-muted">Default sender name</small>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Action Buttons -->
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-check-circle me-2"></i>Save Configuration
                                    </button>
                                    <button type="button" class="btn btn-success" id="testConnectionBtn">
                                        <i class="bi bi-send me-2"></i>Test Connection
                                    </button>
                                    <button type="button" class="btn btn-secondary" id="resetFormBtn">
                                        <i class="bi bi-arrow-clockwise me-2"></i>Reset
                                    </button>
                                </div>
                            </form>
                        </div>
                        
                        <!-- Information Panel -->
                        <div class="col-lg-4">
                            <div class="card bg-light">
                                <div class="card-header">
                                    <h6 class="card-title mb-0"><i class="bi bi-info-circle me-2"></i>Configuration Help</h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <h6 class="text-primary">Common SMTP Providers</h6>
                                        <div class="small">
                                            <strong>Gmail:</strong><br>
                                            Host: smtp.gmail.com<br>
                                            Port: 587, Encryption: TLS<br><br>
                                            
                                            <strong>Outlook/Hotmail:</strong><br>
                                            Host: smtp-mail.outlook.com<br>
                                            Port: 587, Encryption: TLS<br><br>
                                            
                                            <strong>Yahoo:</strong><br>
                                            Host: smtp.mail.yahoo.com<br>
                                            Port: 587, Encryption: TLS<br><br>
                                            
                                            <strong>Mailgun:</strong><br>
                                            Host: smtp.mailgun.org<br>
                                            Port: 587, Encryption: TLS
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <h6 class="text-primary">Security Notes</h6>
                                        <ul class="small mb-0">
                                            <li>Use TLS encryption when available</li>
                                            <li>For Gmail, use App Passwords instead of your regular password</li>
                                            <li>Test your configuration before going live</li>
                                            <li>Keep credentials secure and private</li>
                                        </ul>
                                    </div>
                                    
                                    <div class="alert alert-info small">
                                        <i class="bi bi-lightbulb me-2"></i>
                                        <strong>Tip:</strong> Use the test connection feature to verify your settings before saving.
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Test Email Section -->
                            <div class="card mt-4">
                                <div class="card-header">
                                    <h6 class="mb-0"><i class="bi bi-send me-2"></i>Test Email Configuration</h6>
                                </div>
                                <div class="card-body">
                                    <form id="testEmailForm">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="mb-3">
                                                    <label for="testEmail" class="form-label">Test Email Address *</label>
                                                    <input type="email" class="form-control" id="testEmail" name="test_email" required
                                                           placeholder="Enter email address to receive test email">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="testSubject" class="form-label">Email Subject</label>
                                            <input type="text" class="form-control" id="testSubject" name="test_subject"
                                                   placeholder="SMTP Configuration Test" value="SMTP Configuration Test">
                                        </div>
                                        <div class="mb-3">
                                            <label for="testMessage" class="form-label">Test Message</label>
                                            <textarea class="form-control" id="testMessage" name="test_message" rows="3"
                                                      placeholder="Enter your test message...">This is a test email to verify your SMTP configuration is working correctly.</textarea>
                                        </div>
                                        
                                        <!-- Email Template Options -->
                                        <div class="mb-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="useTemplate" name="use_template">
                                                <label class="form-check-label" for="useTemplate">
                                                    Use Email Template
                                                </label>
                                            </div>
                                        </div>
                                        
                                        <div class="mb-3" id="templateSelection" style="display: none;">
                                            <label for="templateSelect" class="form-label">Select Template</label>
                                            <select class="form-select" id="templateSelect" name="template_name">
                                                <option value="">Loading templates...</option>
                                            </select>
                                            <div class="form-text">
                                                <i class="bi bi-info-circle me-1"></i>
                                                Templates provide professional styling and branding for your emails.
                                                <a href="{{ route('admin.email-templates.index') }}" target="_blank" class="text-decoration-none">
                                                    Manage Templates <i class="bi bi-box-arrow-up-right"></i>
                                                </a>
                                            </div>
                                        </div>
                                        
                                        <button type="submit" class="btn btn-outline-primary">
                                            <i class="bi bi-send me-1"></i>Send Test Email
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Test Email Modal -->
<div class="modal fade" id="testEmailModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-send me-2"></i>Test Email Connection</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="testEmailForm">
                    <div class="mb-3">
                        <label for="test_email" class="form-label">Test Email Address</label>
                        <input type="email" class="form-control" id="test_email" name="test_email" required
                               placeholder="Enter email address to send test email">
                        <small class="text-muted">A test email will be sent to this address</small>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" id="sendTestEmailBtn">
                    <i class="bi bi-send me-2"></i>Send Test Email
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Toggle password visibility
    $('#togglePassword').click(function() {
        const passwordField = $('#password');
        const icon = $(this).find('i');
        
        if (passwordField.attr('type') === 'password') {
            passwordField.attr('type', 'text');
            icon.removeClass('bi-eye').addClass('bi-eye-slash');
        } else {
            passwordField.attr('type', 'password');
            icon.removeClass('bi-eye-slash').addClass('bi-eye');
        }
    });
    
    // Toggle SMTP settings visibility based on mailer selection
    $('#default_mailer').change(function() {
        const isSmtp = $(this).val() === 'smtp';
        $('#smtpSettings, #smtpAuth').toggle(isSmtp);
    }).trigger('change');
    
    // Save SMTP configuration
    $('#smtpConfigForm').submit(function(e) {
        e.preventDefault();
        
        const submitBtn = $(this).find('button[type="submit"]');
        const originalText = submitBtn.html();
        
        submitBtn.prop('disabled', true).html('<i class="bi bi-hourglass-split me-2"></i>Saving...');
        
        $.ajax({
            url: '{{ route("admin.smtp.update") }}',
            method: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                if (response.success) {
                    showAlert('success', response.message);
                } else {
                    showAlert('danger', response.message || 'Failed to update configuration');
                }
            },
            error: function(xhr) {
                const errors = xhr.responseJSON?.errors;
                if (errors) {
                    let errorMessage = 'Validation errors:\n';
                    Object.keys(errors).forEach(key => {
                        errorMessage += `• ${errors[key][0]}\n`;
                    });
                    showAlert('danger', errorMessage);
                } else {
                    showAlert('danger', 'Failed to update SMTP configuration');
                }
            },
            complete: function() {
                submitBtn.prop('disabled', false).html(originalText);
            }
        });
    });
    
    // Test connection button
    $('#testConnectionBtn').click(function() {
        $('#testEmailModal').modal('show');
    });
    
    // Load email templates on page load
    loadEmailTemplates();
    
    // Toggle template selection
    $('#useTemplate').change(function() {
        if ($(this).is(':checked')) {
            $('#templateSelection').slideDown();
            loadEmailTemplates();
        } else {
            $('#templateSelection').slideUp();
        }
    });
    
    // Send test email
    $('#testEmailForm').submit(function(e) {
        e.preventDefault();
        
        const testEmail = $('#testEmail').val();
        if (!testEmail) {
            showAlert('warning', 'Please enter a test email address');
            return;
        }
        
        const submitBtn = $(this).find('button[type="submit"]');
        const originalText = submitBtn.html();
        submitBtn.prop('disabled', true).html('<i class="bi bi-hourglass-split me-1"></i>Sending...');
        
        const formData = {
            test_email: testEmail,
            test_subject: $('#testSubject').val(),
            test_message: $('#testMessage').val(),
            use_template: $('#useTemplate').is(':checked'),
            template_name: $('#templateSelect').val(),
            _token: $('meta[name="csrf-token"]').attr('content')
        };
        
        $.ajax({
            url: '{{ route("admin.smtp.test") }}',
            method: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    showAlert('success', response.message);
                    // Don't reset form to preserve user input
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
                    showAlert('danger', 'Failed to send test email');
                }
            },
            complete: function() {
                submitBtn.prop('disabled', false).html(originalText);
            }
        });
    });
    

    
    // Load email templates
    function loadEmailTemplates() {
        $.ajax({
            url: '{{ route("admin.smtp.getTemplates") }}',
            method: 'GET',
            success: function(response) {
                if (response.success) {
                    const select = $('#templateSelect');
                    select.empty();
                    select.append('<option value="">Select a template</option>');
                    
                    Object.keys(response.templates).forEach(key => {
                        const template = response.templates[key];
                        select.append(`<option value="${key}">${template.name} - ${template.description}</option>`);
                    });
                } else {
                    $('#templateSelect').html('<option value="">Failed to load templates</option>');
                }
            },
            error: function() {
                $('#templateSelect').html('<option value="">Error loading templates</option>');
            }
        });
    }
    
    // Reset form
    $('#resetFormBtn').click(function() {
        if (confirm('Are you sure you want to reset the form? All unsaved changes will be lost.')) {
            location.reload();
        }
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
        $('.alert').remove();
        
        // Add new alert at the top of the card body
        $('.card-body').prepend(alertHtml);
        
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