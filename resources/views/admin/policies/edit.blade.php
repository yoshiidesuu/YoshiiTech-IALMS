@extends('layouts.admin')

@section('page-title', 'Edit Policy')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <div>
                        <h6 class="m-0 font-weight-bold text-primary">Edit Policy</h6>
                        <small class="text-muted">{{ $policy->title }} (v{{ $policy->version }})</small>
                    </div>
                    <div>
                        <a href="{{ route('admin.policies.show', $policy) }}" class="btn btn-info btn-sm me-2">
                            <i class="fas fa-eye"></i> View Policy
                        </a>
                        <a href="{{ route('admin.policies.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Back to Policies
                        </a>
                    </div>
                </div>
                
                <div class="card-body">
                    <form action="{{ route('admin.policies.update', $policy) }}" method="POST" id="policyForm">
                        @csrf
                        @method('PUT')
                        
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
                                                           id="title" name="title" value="{{ old('title', $policy->title) }}" 
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
                                                        <option value="academic" {{ old('category', $policy->category) === 'academic' ? 'selected' : '' }}>Academic</option>
                                                        <option value="administrative" {{ old('category', $policy->category) === 'administrative' ? 'selected' : '' }}>Administrative</option>
                                                        <option value="student_affairs" {{ old('category', $policy->category) === 'student_affairs' ? 'selected' : '' }}>Student Affairs</option>
                                                        <option value="faculty" {{ old('category', $policy->category) === 'faculty' ? 'selected' : '' }}>Faculty</option>
                                                        <option value="financial" {{ old('category', $policy->category) === 'financial' ? 'selected' : '' }}>Financial</option>
                                                        <option value="disciplinary" {{ old('category', $policy->category) === 'disciplinary' ? 'selected' : '' }}>Disciplinary</option>
                                                        <option value="general" {{ old('category', $policy->category) === 'general' ? 'selected' : '' }}>General</option>
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
                                                      placeholder="Brief description of the policy">{{ old('description', $policy->description) }}</textarea>
                                            @error('description')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        
                                        <div class="form-group">
                                            <label for="content" class="form-label">Policy Content <span class="text-danger">*</span></label>
                                            <textarea class="form-control @error('content') is-invalid @enderror" 
                                                      id="content" name="content" rows="15" 
                                                      placeholder="Enter the complete policy content..." required>{{ old('content', $policy->content) }}</textarea>
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
                                        <div class="alert alert-info">
                                            <i class="fas fa-info-circle"></i>
                                            <strong>Current Version:</strong> {{ $policy->version }}
                                            @if($policy->published_at)
                                                | <strong>Published:</strong> {{ $policy->published_at->format('M d, Y h:i A') }}
                                            @endif
                                            @if($policy->effective_date)
                                                | <strong>Effective:</strong> {{ $policy->effective_date->format('M d, Y') }}
                                            @endif
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="version" class="form-label">Version <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control @error('version') is-invalid @enderror" 
                                                           id="version" name="version" value="{{ old('version', $policy->version) }}" 
                                                           placeholder="e.g., 1.0" required>
                                                    @error('version')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                    <small class="form-text text-muted">Increment version for significant changes</small>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="effective_date" class="form-label">Effective Date</label>
                                                    <input type="date" class="form-control @error('effective_date') is-invalid @enderror" 
                                                           id="effective_date" name="effective_date" 
                                                           value="{{ old('effective_date', $policy->effective_date ? $policy->effective_date->format('Y-m-d') : '') }}">
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
                                                      placeholder="Describe changes in this version...">{{ old('version_notes', $policy->version_notes) }}</textarea>
                                            @error('version_notes')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" 
                                                   id="create_new_version" name="create_new_version" value="1">
                                            <label class="form-check-label" for="create_new_version">
                                                <strong>Create New Version</strong>
                                            </label>
                                            <small class="form-text text-muted d-block">
                                                Check this to create a new version instead of updating the current one.
                                                This preserves the current version in history.
                                            </small>
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
                                                <option value="draft" {{ old('status', $policy->status) === 'draft' ? 'selected' : '' }}>Draft</option>
                                                <option value="published" {{ old('status', $policy->status) === 'published' ? 'selected' : '' }}>Published</option>
                                                <option value="archived" {{ old('status', $policy->status) === 'archived' ? 'selected' : '' }}>Archived</option>
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
                                                <option value="low" {{ old('priority', $policy->priority ?? 'medium') === 'low' ? 'selected' : '' }}>Low</option>
                                                <option value="medium" {{ old('priority', $policy->priority ?? 'medium') === 'medium' ? 'selected' : '' }}>Medium</option>
                                                <option value="high" {{ old('priority', $policy->priority ?? 'medium') === 'high' ? 'selected' : '' }}>High</option>
                                                <option value="critical" {{ old('priority', $policy->priority ?? 'medium') === 'critical' ? 'selected' : '' }}>Critical</option>
                                            </select>
                                            @error('priority')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        
                                        <div class="form-group">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" 
                                                       id="requires_acknowledgment" name="requires_acknowledgment" 
                                                       value="1" {{ old('requires_acknowledgment', $policy->requires_acknowledgment ?? false) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="requires_acknowledgment">
                                                    Requires Acknowledgment
                                                </label>
                                            </div>
                                            <small class="form-text text-muted">Users must acknowledge reading this policy</small>
                                        </div>
                                        
                                        <div class="form-group">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" 
                                                       id="notify_users" name="notify_users" value="1">
                                                <label class="form-check-label" for="notify_users">
                                                    Notify Users of Changes
                                                </label>
                                            </div>
                                            <small class="form-text text-muted">Send notification about policy updates</small>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Version History -->
                                @if($policy->versions && $policy->versions->count() > 1)
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <h6 class="m-0 font-weight-bold text-primary">Version History</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="timeline">
                                            @foreach($policy->versions->take(5) as $version)
                                                <div class="timeline-item {{ $version->id === $policy->id ? 'current' : '' }}">
                                                    <div class="timeline-marker"></div>
                                                    <div class="timeline-content">
                                                        <div class="d-flex justify-content-between align-items-start">
                                                            <div>
                                                                <strong>v{{ $version->version }}</strong>
                                                                @if($version->id === $policy->id)
                                                                    <span class="badge badge-primary badge-sm">Current</span>
                                                                @endif
                                                            </div>
                                                            <small class="text-muted">{{ $version->updated_at->format('M d, Y') }}</small>
                                                        </div>
                                                        @if($version->version_notes)
                                                            <small class="text-muted">{{ Str::limit($version->version_notes, 60) }}</small>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                        <a href="{{ route('admin.policies.versions', $policy) }}" class="btn btn-outline-primary btn-sm w-100 mt-2">
                                            <i class="fas fa-history"></i> View All Versions
                                        </a>
                                    </div>
                                </div>
                                @endif
                                
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
                                                <i class="fas fa-save"></i> Update Policy
                                            </button>
                                            
                                            @if($policy->status !== 'published')
                                                <button type="submit" class="btn btn-success" name="action" value="save_and_publish">
                                                    <i class="fas fa-check-circle"></i> Update & Publish
                                                </button>
                                            @endif
                                            
                                            <a href="{{ route('admin.policies.show', $policy) }}" class="btn btn-info">
                                                <i class="fas fa-eye"></i> View Policy
                                            </a>
                                            
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

.timeline {
    position: relative;
    padding-left: 20px;
}

.timeline-item {
    position: relative;
    margin-bottom: 15px;
    padding-bottom: 15px;
    border-left: 2px solid #e3e6f0;
}

.timeline-item.current {
    border-left-color: #4e73df;
}

.timeline-marker {
    position: absolute;
    left: -6px;
    top: 0;
    width: 10px;
    height: 10px;
    border-radius: 50%;
    background-color: #e3e6f0;
}

.timeline-item.current .timeline-marker {
    background-color: #4e73df;
}

.timeline-content {
    padding-left: 15px;
}

.badge-sm {
    font-size: 0.7em;
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
    const versionInput = document.getElementById('version');
    const createNewVersionCheckbox = document.getElementById('create_new_version');
    const titleInput = document.getElementById('title');
    
    const originalVersion = '{{ $policy->version }}';
    const originalTitle = '{{ $policy->title }}';
    
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
    
    // Version management
    createNewVersionCheckbox.addEventListener('change', function() {
        if (this.checked) {
            // Suggest next version
            const currentVersion = originalVersion;
            const versionParts = currentVersion.split('.');
            if (versionParts.length >= 2) {
                const major = parseInt(versionParts[0]);
                const minor = parseInt(versionParts[1]);
                const patch = versionParts.length > 2 ? parseInt(versionParts[2]) : 0;
                
                // Increment minor version
                const newVersion = `${major}.${minor + 1}`;
                versionInput.value = newVersion;
            }
        } else {
            versionInput.value = originalVersion;
        }
    });
    
    // Auto-suggest version increment on significant changes
    let contentChanged = false;
    let titleChanged = false;
    
    contentTextarea.addEventListener('input', function() {
        contentChanged = true;
        suggestVersionIncrement();
    });
    
    titleInput.addEventListener('input', function() {
        titleChanged = (this.value !== originalTitle);
        suggestVersionIncrement();
    });
    
    function suggestVersionIncrement() {
        if ((contentChanged || titleChanged) && !createNewVersionCheckbox.checked) {
            // Show subtle hint about version increment
            if (!document.getElementById('versionHint')) {
                const hint = document.createElement('small');
                hint.id = 'versionHint';
                hint.className = 'form-text text-info';
                hint.innerHTML = '<i class="fas fa-lightbulb"></i> Consider creating a new version for significant changes';
                versionInput.parentNode.appendChild(hint);
            }
        }
    }
    
    // Form validation
    const form = document.getElementById('policyForm');
    form.addEventListener('submit', function(e) {
        const title = titleInput.value.trim();
        const content = contentTextarea.value.trim();
        const category = document.getElementById('category').value;
        const version = versionInput.value.trim();
        
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
            versionInput.focus();
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
        
        // Confirm new version creation
        if (createNewVersionCheckbox.checked) {
            if (!confirm('This will create a new version of the policy. The current version will be preserved in history. Continue?')) {
                e.preventDefault();
                return false;
            }
        }
    });
    
    // Character counter for content
    const maxLength = 10000; // Adjust as needed
    const counter = document.createElement('small');
    counter.className = 'form-text text-muted';
    const currentLength = contentTextarea.value.length;
    counter.innerHTML = `<i class="fas fa-info-circle"></i> ${currentLength} / ${maxLength} characters`;
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
    
    // Initial preview update
    updatePreview();
});
</script>
@endpush
@endsection