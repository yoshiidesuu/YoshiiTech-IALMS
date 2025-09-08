@extends('layouts.admin')

@section('page-title', 'Create New Policy')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Create New Policy</h6>
                    <a href="{{ route('admin.policies.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left"></i> Back to Policies
                    </a>
                </div>
                
                <div class="card-body">
                    <form action="{{ route('admin.policies.store') }}" method="POST" id="policyForm">
                        @csrf
                        
                        <div class="row">
                            <!-- Basic Information -->
                            <div class="col-lg-8">
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <h6 class="m-0 font-weight-bold text-primary">Basic Information</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-8">
                                                <div class="form-group">
                                                    <label for="title" class="form-label">Policy Title <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                                           id="title" name="title" value="{{ old('title') }}" 
                                                           placeholder="Enter policy title" required>
                                                    @error('title')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="category" class="form-label">Category <span class="text-danger">*</span></label>
                                                    <select class="form-select @error('category') is-invalid @enderror" 
                                                            id="category" name="category" required>
                                                        <option value="">Select Category</option>
                                                        <option value="academic" {{ old('category') === 'academic' ? 'selected' : '' }}>Academic</option>
                                                        <option value="administrative" {{ old('category') === 'administrative' ? 'selected' : '' }}>Administrative</option>
                                                        <option value="student_affairs" {{ old('category') === 'student_affairs' ? 'selected' : '' }}>Student Affairs</option>
                                                        <option value="faculty" {{ old('category') === 'faculty' ? 'selected' : '' }}>Faculty</option>
                                                        <option value="financial" {{ old('category') === 'financial' ? 'selected' : '' }}>Financial</option>
                                                        <option value="disciplinary" {{ old('category') === 'disciplinary' ? 'selected' : '' }}>Disciplinary</option>
                                                        <option value="general" {{ old('category') === 'general' ? 'selected' : '' }}>General</option>
                                                    </select>
                                                    @error('category')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label for="description" class="form-label">Description</label>
                                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                                      id="description" name="description" rows="3" 
                                                      placeholder="Brief description of the policy">{{ old('description') }}</textarea>
                                            @error('description')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        
                                        <div class="form-group">
                                            <label for="content" class="form-label">Policy Content <span class="text-danger">*</span></label>
                                            <textarea class="form-control @error('content') is-invalid @enderror" 
                                                      id="content" name="content" rows="15" 
                                                      placeholder="Enter the complete policy content..." required>{{ old('content') }}</textarea>
                                            @error('content')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <small class="form-text text-muted">
                                                <i class="fas fa-info-circle"></i> You can use Markdown formatting for better presentation.
                                            </small>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Version Information -->
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <h6 class="m-0 font-weight-bold text-primary">Version Information</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="version" class="form-label">Version <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control @error('version') is-invalid @enderror" 
                                                           id="version" name="version" value="{{ old('version', '1.0') }}" 
                                                           placeholder="e.g., 1.0" required>
                                                    @error('version')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                    <small class="form-text text-muted">Use semantic versioning (e.g., 1.0, 1.1, 2.0)</small>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="effective_date" class="form-label">Effective Date</label>
                                                    <input type="date" class="form-control @error('effective_date') is-invalid @enderror" 
                                                           id="effective_date" name="effective_date" value="{{ old('effective_date') }}">
                                                    @error('effective_date')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                    <small class="form-text text-muted">When this policy becomes effective</small>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label for="version_notes" class="form-label">Version Notes</label>
                                            <textarea class="form-control @error('version_notes') is-invalid @enderror" 
                                                      id="version_notes" name="version_notes" rows="3" 
                                                      placeholder="Describe changes in this version...">{{ old('version_notes') }}</textarea>
                                            @error('version_notes')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Settings Sidebar -->
                            <div class="col-lg-4">
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <h6 class="m-0 font-weight-bold text-primary">Policy Settings</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                            <select class="form-select @error('status') is-invalid @enderror" 
                                                    id="status" name="status" required>
                                                <option value="draft" {{ old('status', 'draft') === 'draft' ? 'selected' : '' }}>Draft</option>
                                                <option value="published" {{ old('status') === 'published' ? 'selected' : '' }}>Published</option>
                                            </select>
                                            @error('status')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <small class="form-text text-muted">Draft policies are not visible to users</small>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label for="priority" class="form-label">Priority</label>
                                            <select class="form-select @error('priority') is-invalid @enderror" 
                                                    id="priority" name="priority">
                                                <option value="low" {{ old('priority', 'medium') === 'low' ? 'selected' : '' }}>Low</option>
                                                <option value="medium" {{ old('priority', 'medium') === 'medium' ? 'selected' : '' }}>Medium</option>
                                                <option value="high" {{ old('priority') === 'high' ? 'selected' : '' }}>High</option>
                                                <option value="critical" {{ old('priority') === 'critical' ? 'selected' : '' }}>Critical</option>
                                            </select>
                                            @error('priority')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        
                                        <div class="form-group">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" 
                                                       id="requires_acknowledgment" name="requires_acknowledgment" 
                                                       value="1" {{ old('requires_acknowledgment') ? 'checked' : '' }}>
                                                <label class="form-check-label" for="requires_acknowledgment">
                                                    Requires Acknowledgment
                                                </label>
                                            </div>
                                            <small class="form-text text-muted">Users must acknowledge reading this policy</small>
                                        </div>
                                        
                                        <div class="form-group">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" 
                                                       id="notify_users" name="notify_users" 
                                                       value="1" {{ old('notify_users') ? 'checked' : '' }}>
                                                <label class="form-check-label" for="notify_users">
                                                    Notify Users
                                                </label>
                                            </div>
                                            <small class="form-text text-muted">Send notification when published</small>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Preview Card -->
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <h6 class="m-0 font-weight-bold text-primary">Preview</h6>
                                    </div>
                                    <div class="card-body">
                                        <div id="contentPreview" class="border rounded p-3 bg-light" style="min-height: 200px;">
                                            <p class="text-muted text-center">Content preview will appear here...</p>
                                        </div>
                                        <button type="button" class="btn btn-outline-primary btn-sm mt-2 w-100" id="previewBtn">
                                            <i class="fas fa-eye"></i> Update Preview
                                        </button>
                                    </div>
                                </div>
                                
                                <!-- Action Buttons -->
                                <div class="card">
                                    <div class="card-body">
                                        <div class="d-grid gap-2">
                                            <button type="submit" class="btn btn-primary" name="action" value="save">
                                                <i class="fas fa-save"></i> Save Policy
                                            </button>
                                            
                                            <button type="submit" class="btn btn-success" name="action" value="save_and_publish" 
                                                    id="publishBtn" style="display: none;">
                                                <i class="fas fa-check-circle"></i> Save & Publish
                                            </button>
                                            
                                            <a href="{{ route('admin.policies.index') }}" class="btn btn-secondary">
                                                <i class="fas fa-times"></i> Cancel
                                            </a>
                                        </div>
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

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/marked@4.0.10/lib/marked.min.js" rel="preload" as="script">
<style>
.form-group {
    margin-bottom: 1rem;
}

.card-header h6 {
    margin-bottom: 0;
}

#contentPreview {
    max-height: 300px;
    overflow-y: auto;
}

#contentPreview h1, #contentPreview h2, #contentPreview h3 {
    margin-top: 0;
}

#contentPreview p:last-child {
    margin-bottom: 0;
}

.form-check {
    margin-bottom: 0.5rem;
}

.text-danger {
    color: #e74c3c !important;
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/marked@4.0.10/lib/marked.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const contentTextarea = document.getElementById('content');
    const previewDiv = document.getElementById('contentPreview');
    const previewBtn = document.getElementById('previewBtn');
    const statusSelect = document.getElementById('status');
    const publishBtn = document.getElementById('publishBtn');
    const titleInput = document.getElementById('title');
    
    // Show/hide publish button based on status
    function togglePublishButton() {
        if (statusSelect.value === 'published') {
            publishBtn.style.display = 'block';
        } else {
            publishBtn.style.display = 'none';
        }
    }
    
    statusSelect.addEventListener('change', togglePublishButton);
    togglePublishButton(); // Initial check
    
    // Preview functionality
    function updatePreview() {
        const content = contentTextarea.value.trim();
        if (content) {
            try {
                // Use marked.js to parse markdown
                const htmlContent = marked.parse(content);
                previewDiv.innerHTML = htmlContent;
            } catch (error) {
                // Fallback to plain text with line breaks
                previewDiv.innerHTML = content.replace(/\n/g, '<br>');
            }
        } else {
            previewDiv.innerHTML = '<p class="text-muted text-center">Content preview will appear here...</p>';
        }
    }
    
    previewBtn.addEventListener('click', updatePreview);
    
    // Auto-update preview on content change (debounced)
    let previewTimeout;
    contentTextarea.addEventListener('input', function() {
        clearTimeout(previewTimeout);
        previewTimeout = setTimeout(updatePreview, 1000);
    });
    
    // Form validation
    const form = document.getElementById('policyForm');
    form.addEventListener('submit', function(e) {
        const title = titleInput.value.trim();
        const content = contentTextarea.value.trim();
        const category = document.getElementById('category').value;
        const version = document.getElementById('version').value.trim();
        
        if (!title || !content || !category || !version) {
            e.preventDefault();
            alert('Please fill in all required fields.');
            return false;
        }
        
        // Validate version format
        const versionPattern = /^\d+\.\d+(\.\d+)?$/;
        if (!versionPattern.test(version)) {
            e.preventDefault();
            alert('Please enter a valid version number (e.g., 1.0, 1.1, 2.0.1)');
            document.getElementById('version').focus();
            return false;
        }
        
        // Confirm publication
        const action = e.submitter.value;
        if (action === 'save_and_publish') {
            if (!confirm('Are you sure you want to publish this policy? It will be visible to all users.')) {
                e.preventDefault();
                return false;
            }
        }
    });
    
    // Auto-generate version based on title changes
    let initialTitle = titleInput.value;
    titleInput.addEventListener('blur', function() {
        if (this.value !== initialTitle && document.getElementById('version').value === '1.0') {
            // Keep version as 1.0 for new policies
        }
    });
    
    // Character counter for content
    const maxLength = 10000; // Adjust as needed
    const counter = document.createElement('small');
    counter.className = 'form-text text-muted';
    counter.innerHTML = `<i class="fas fa-info-circle"></i> 0 / ${maxLength} characters`;
    contentTextarea.parentNode.appendChild(counter);
    
    contentTextarea.addEventListener('input', function() {
        const length = this.value.length;
        counter.innerHTML = `<i class="fas fa-info-circle"></i> ${length} / ${maxLength} characters`;
        
        if (length > maxLength * 0.9) {
            counter.className = 'form-text text-warning';
        } else if (length > maxLength) {
            counter.className = 'form-text text-danger';
        } else {
            counter.className = 'form-text text-muted';
        }
    });
    
    // Initial preview update if content exists
    if (contentTextarea.value.trim()) {
        updatePreview();
    }
});
</script>
@endpush
@endsection