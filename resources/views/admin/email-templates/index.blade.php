@extends('layouts.admin')

@section('title', 'Email Templates')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bi bi-envelope-paper me-2"></i>
                        Email Template Management
                    </h5>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createTemplateModal">
                        <i class="bi bi-plus-circle me-1"></i>
                        Create Template
                    </button>
                </div>
                <div class="card-body">
                    <!-- Template Selection -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label for="templateSelect" class="form-label">Select Template</label>
                            <select class="form-select" id="templateSelect">
                                @foreach($templates as $key => $template)
                                    <option value="{{ $key }}" {{ $currentTemplate === $key ? 'selected' : '' }}>
                                        {{ $template['name'] }} {{ $template['is_default'] ? '(Default)' : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 d-flex align-items-end">
                            <button type="button" class="btn btn-outline-primary me-2" id="previewBtn">
                                <i class="bi bi-eye me-1"></i>
                                Preview
                            </button>
                            <button type="button" class="btn btn-outline-success me-2" id="testEmailBtn">
                                <i class="bi bi-send me-1"></i>
                                Send Test
                            </button>
                            <button type="button" class="btn btn-outline-danger" id="deleteTemplateBtn" style="display: none;">
                                <i class="bi bi-trash me-1"></i>
                                Delete
                            </button>
                        </div>
                    </div>

                    <!-- Template Configuration Form -->
                    <form id="templateForm">
                        <div class="row">
                            <!-- Basic Settings -->
                            <div class="col-md-6">
                                <div class="card mb-3">
                                    <div class="card-header">
                                        <h6 class="mb-0">Basic Settings</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="templateName" class="form-label">Template Name</label>
                                            <input type="text" class="form-control" id="templateName" name="template_name" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="subject" class="form-label">Email Subject</label>
                                            <input type="text" class="form-control" id="subject" name="subject" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="headerText" class="form-label">Header Text</label>
                                            <textarea class="form-control tinymce-editor" id="headerText" name="header_text" rows="2"></textarea>
                                        </div>
                                        <div class="mb-3">
                                            <label for="footerText" class="form-label">Footer Text</label>
                                            <textarea class="form-control tinymce-editor" id="footerText" name="footer_text" rows="2"></textarea>
                                        </div>
                                    </div>
                                </div>

                                <!-- Company Information -->
                                <div class="card mb-3">
                                    <div class="card-header">
                                        <h6 class="mb-0">Company Information</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="companyName" class="form-label">Company Name</label>
                                            <input type="text" class="form-control" id="companyName" name="company_name">
                                        </div>
                                        <div class="mb-3">
                                            <label for="logoUrl" class="form-label">Logo URL</label>
                                            <input type="url" class="form-control" id="logoUrl" name="logo_url">
                                        </div>
                                        <div class="mb-3">
                                            <label for="companyAddress" class="form-label">Company Address</label>
                                            <textarea class="form-control tinymce-editor" id="companyAddress" name="company_address" rows="3"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Design Settings -->
                            <div class="col-md-6">
                                <div class="card mb-3">
                                    <div class="card-header">
                                        <h6 class="mb-0">Design Settings</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="primaryColor" class="form-label">Primary Color</label>
                                                    <input type="color" class="form-control form-control-color" id="primaryColor" name="primary_color" value="#007bff">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="secondaryColor" class="form-label">Secondary Color</label>
                                                    <input type="color" class="form-control form-control-color" id="secondaryColor" name="secondary_color" value="#6c757d">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Social Links -->
                                <div class="card mb-3">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <h6 class="mb-0">Social Links</h6>
                                        <button type="button" class="btn btn-sm btn-outline-primary" id="addSocialLink">
                                            <i class="bi bi-plus"></i>
                                        </button>
                                    </div>
                                    <div class="card-body">
                                        <div id="socialLinksContainer">
                                            <!-- Social links will be added here dynamically -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex justify-content-between">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="setAsActive" name="set_as_active">
                                        <label class="form-check-label" for="setAsActive">
                                            Set as active template
                                        </label>
                                    </div>
                                    <div>
                                        <button type="button" class="btn btn-secondary me-2" id="resetForm">Reset</button>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="bi bi-check-circle me-1"></i>
                                            Save Template
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Create Template Modal -->
<div class="modal fade" id="createTemplateModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Create New Template</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="createTemplateForm">
                    <div class="mb-3">
                        <label for="newTemplateName" class="form-label">Template Name</label>
                        <input type="text" class="form-control" id="newTemplateName" name="template_name" required>
                        <div class="form-text">Enter a unique name for your template</div>
                    </div>
                    <div class="mb-3">
                        <label for="baseTemplate" class="form-label">Base Template</label>
                        <select class="form-select" id="baseTemplate" name="base_template">
                            <option value="default">Default Template</option>
                            @foreach($templates as $key => $template)
                                @if(!$template['is_default'])
                                    <option value="{{ $key }}">{{ $template['name'] }}</option>
                                @endif
                            @endforeach
                        </select>
                        <div class="form-text">Choose a template to copy settings from</div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="createTemplateBtn">Create Template</button>
            </div>
        </div>
    </div>
</div>

<!-- Preview Modal -->
<div class="modal fade" id="previewModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Email Template Preview</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="previewType" class="form-label">Preview Type</label>
                    <select class="form-select" id="previewType">
                        <option value="welcome">Welcome Email</option>
                        <option value="password_reset">Password Reset</option>
                        <option value="notification">Notification</option>
                    </select>
                </div>
                <div id="previewContent" class="border rounded p-3" style="min-height: 400px;">
                    <!-- Preview content will be loaded here -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="refreshPreview">Refresh Preview</button>
            </div>
        </div>
    </div>
</div>

<!-- Test Email Modal -->
<div class="modal fade" id="testEmailModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Send Test Email</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="testEmailForm">
                    <div class="mb-3">
                        <label for="testEmail" class="form-label">Test Email Address</label>
                        <input type="email" class="form-control" id="testEmail" name="test_email" required>
                    </div>
                    <div class="mb-3">
                        <label for="testType" class="form-label">Email Type</label>
                        <select class="form-select" id="testType" name="test_type">
                            <option value="welcome">Welcome Email</option>
                            <option value="password_reset">Password Reset</option>
                            <option value="notification">Notification</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="sendTestEmailBtn">
                    <i class="bi bi-send me-1"></i>
                    Send Test Email
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
$(document).ready(function() {
    let templates = @json($templates);
    let currentTemplate = '{{ $currentTemplate }}';
    let socialLinkIndex = 0;

    // Load template data when selection changes
    $('#templateSelect').on('change', function() {
        const templateKey = $(this).val();
        loadTemplateData(templateKey);
        toggleDeleteButton(templateKey);
    });

    // Load initial template data
    loadTemplateData(currentTemplate);
    toggleDeleteButton(currentTemplate);

    // Template form submission
    $('#templateForm').on('submit', function(e) {
        e.preventDefault();
        saveTemplate();
    });

    // Create template
    $('#createTemplateBtn').on('click', function() {
        createNewTemplate();
    });

    // Preview functionality
    $('#previewBtn').on('click', function() {
        showPreview();
    });

    $('#refreshPreview').on('click', function() {
        showPreview();
    });

    $('#previewType').on('change', function() {
        showPreview();
    });

    // Test email functionality
    $('#testEmailBtn').on('click', function() {
        $('#testEmailModal').modal('show');
    });

    $('#sendTestEmailBtn').on('click', function() {
        sendTestEmail();
    });

    // Delete template
    $('#deleteTemplateBtn').on('click', function() {
        deleteTemplate();
    });

    // Reset form
    $('#resetForm').on('click', function() {
        const templateKey = $('#templateSelect').val();
        loadTemplateData(templateKey);
    });

    // Add social link
    $('#addSocialLink').on('click', function() {
        addSocialLinkField();
    });

    // Load template data
    function loadTemplateData(templateKey) {
        const template = templates[templateKey];
        if (!template) return;

        const data = template.data;
        
        $('#templateName').val(templateKey);
        $('#subject').val(data.subject || '');
        $('#headerText').val(data.header_text || '');
        $('#footerText').val(data.footer_text || '');
        $('#primaryColor').val(data.primary_color || '#007bff');
        $('#secondaryColor').val(data.secondary_color || '#6c757d');
        $('#logoUrl').val(data.logo_url || '');
        $('#companyName').val(data.company_name || '');
        $('#companyAddress').val(data.company_address || '');
        
        // Load social links
        loadSocialLinks(data.social_links || []);
        
        // Set active checkbox
        $('#setAsActive').prop('checked', currentTemplate === templateKey);
        
        // Disable template name for default template
        $('#templateName').prop('readonly', template.is_default);
    }

    // Load social links
    function loadSocialLinks(socialLinks) {
        $('#socialLinksContainer').empty();
        socialLinkIndex = 0;
        
        socialLinks.forEach(function(link) {
            addSocialLinkField(link.platform, link.url);
        });
    }

    // Add social link field
    function addSocialLinkField(platform = '', url = '') {
        const html = `
            <div class="row mb-2 social-link-row" data-index="${socialLinkIndex}">
                <div class="col-md-4">
                    <select class="form-select" name="social_links[${socialLinkIndex}][platform]">
                        <option value="">Select Platform</option>
                        <option value="facebook" ${platform === 'facebook' ? 'selected' : ''}>Facebook</option>
                        <option value="twitter" ${platform === 'twitter' ? 'selected' : ''}>Twitter</option>
                        <option value="linkedin" ${platform === 'linkedin' ? 'selected' : ''}>LinkedIn</option>
                        <option value="instagram" ${platform === 'instagram' ? 'selected' : ''}>Instagram</option>
                        <option value="youtube" ${platform === 'youtube' ? 'selected' : ''}>YouTube</option>
                    </select>
                </div>
                <div class="col-md-7">
                    <input type="url" class="form-control" name="social_links[${socialLinkIndex}][url]" placeholder="URL" value="${url}">
                </div>
                <div class="col-md-1">
                    <button type="button" class="btn btn-outline-danger btn-sm remove-social-link">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </div>
        `;
        
        $('#socialLinksContainer').append(html);
        socialLinkIndex++;
    }

    // Remove social link
    $(document).on('click', '.remove-social-link', function() {
        $(this).closest('.social-link-row').remove();
    });

    // Toggle delete button
    function toggleDeleteButton(templateKey) {
        const template = templates[templateKey];
        if (template && !template.is_default) {
            $('#deleteTemplateBtn').show();
        } else {
            $('#deleteTemplateBtn').hide();
        }
    }

    // Save template
    function saveTemplate() {
        const formData = new FormData($('#templateForm')[0]);
        const templateKey = $('#templateSelect').val();
        
        $.ajax({
            url: '{{ route("admin.email-templates.update", ":id") }}'.replace(':id', templateKey),
            method: 'PUT',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    showAlert('success', response.message);
                    // Reload page to update templates list
                    setTimeout(() => location.reload(), 1500);
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
                    showAlert('danger', 'Failed to save template');
                }
            }
        });
    }

    // Create new template
    function createNewTemplate() {
        const templateName = $('#newTemplateName').val().trim();
        const baseTemplate = $('#baseTemplate').val();
        
        if (!templateName) {
            showAlert('warning', 'Please enter a template name');
            return;
        }
        
        // Copy base template data
        const baseData = templates[baseTemplate].data;
        
        // Add new option to select
        $('#templateSelect').append(`<option value="${templateName}">${templateName}</option>`);
        $('#templateSelect').val(templateName);
        
        // Add to templates object
        templates[templateName] = {
            name: templateName,
            description: 'Custom template',
            is_default: false,
            data: { ...baseData }
        };
        
        // Load the new template
        loadTemplateData(templateName);
        toggleDeleteButton(templateName);
        
        // Close modal
        $('#createTemplateModal').modal('hide');
        $('#createTemplateForm')[0].reset();
        
        showAlert('info', 'New template created. Don\'t forget to save your changes.');
    }

    // Show preview
    function showPreview() {
        const templateKey = $('#templateSelect').val();
        const previewType = $('#previewType').val();
        
        $.ajax({
            url: '{{ route("admin.email-templates.preview") }}',
            method: 'POST',
            data: {
                template_name: templateKey,
                preview_type: previewType,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    $('#previewContent').html(response.html);
                    $('#previewModal').modal('show');
                } else {
                    showAlert('danger', response.message);
                }
            },
            error: function() {
                showAlert('danger', 'Failed to generate preview');
            }
        });
    }

    // Send test email
    function sendTestEmail() {
        const templateKey = $('#templateSelect').val();
        const testEmail = $('#testEmail').val();
        const testType = $('#testType').val();
        
        if (!testEmail) {
            showAlert('warning', 'Please enter a test email address');
            return;
        }
        
        $('#sendTestEmailBtn').prop('disabled', true).html('<i class="bi bi-hourglass-split me-1"></i>Sending...');
        
        $.ajax({
            url: '{{ route("admin.email-templates.test") }}',
            method: 'POST',
            data: {
                template_name: templateKey,
                test_email: testEmail,
                test_type: testType,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    showAlert('success', response.message);
                    $('#testEmailModal').modal('hide');
                    $('#testEmailForm')[0].reset();
                } else {
                    showAlert('danger', response.message);
                }
            },
            error: function() {
                showAlert('danger', 'Failed to send test email');
            },
            complete: function() {
                $('#sendTestEmailBtn').prop('disabled', false).html('<i class="bi bi-send me-1"></i>Send Test Email');
            }
        });
    }

    // Delete template
    function deleteTemplate() {
        const templateKey = $('#templateSelect').val();
        
        if (!confirm(`Are you sure you want to delete the template "${templateKey}"?`)) {
            return;
        }
        
        $.ajax({
            url: '{{ route("admin.email-templates.delete", ":id") }}'.replace(':id', templateKey),
            method: 'DELETE',
            data: {
                template_name: templateKey,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    showAlert('success', response.message);
                    // Remove from select and reload page
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showAlert('danger', response.message);
                }
            },
            error: function() {
                showAlert('danger', 'Failed to delete template');
            }
        });
    }

    // Show alert
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
        $('.card-body').first().prepend(alertHtml);
        
        // Auto-dismiss after 5 seconds
        setTimeout(() => {
            $('.alert').fadeOut();
        }, 5000);
    }
});
</script>
@endpush