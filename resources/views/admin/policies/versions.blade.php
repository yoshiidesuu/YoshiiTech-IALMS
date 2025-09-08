@extends('layouts.admin')

@section('page-title', 'Policy Versions')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <div>
                        <h6 class="m-0 font-weight-bold text-primary">{{ $policy->title }} - Version History</h6>
                        <small class="text-muted">{{ $versions->count() }} version(s) found</small>
                    </div>
                    <div>
                        @can('policies.manage')
                            <a href="{{ route('admin.policies.create-version', $policy) }}" class="btn btn-success btn-sm me-2">
                                <i class="fas fa-plus"></i> Create New Version
                            </a>
                        @endcan
                        <a href="{{ route('admin.policies.show', $policy) }}" class="btn btn-info btn-sm me-2">
                            <i class="fas fa-eye"></i> View Current
                        </a>
                        <a href="{{ route('admin.policies.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Back to Policies
                        </a>
                    </div>
                </div>
                
                <div class="card-body">
                    @if($versions->count() > 0)
                        <!-- Version Timeline -->
                        <div class="timeline-container">
                            @foreach($versions as $version)
                                <div class="timeline-item {{ $version->is_latest_version ? 'latest-version' : '' }}">
                                    <div class="timeline-marker {{ $version->status === 'published' ? 'published' : ($version->status === 'draft' ? 'draft' : 'inactive') }}">
                                        @if($version->is_latest_version)
                                            <i class="fas fa-star"></i>
                                        @else
                                            <span class="version-number">{{ $version->version }}</span>
                                        @endif
                                    </div>
                                    
                                    <div class="timeline-content">
                                        <div class="card {{ $version->is_latest_version ? 'border-primary' : '' }}">
                                            <div class="card-header d-flex justify-content-between align-items-center">
                                                <div>
                                                    <h6 class="mb-0">
                                                        Version {{ $version->version }}
                                                        @if($version->is_latest_version)
                                                            <span class="badge badge-primary ms-2">Current</span>
                                                        @endif
                                                        <span class="badge badge-{{ $version->status === 'published' ? 'success' : ($version->status === 'draft' ? 'warning' : 'secondary') }} ms-2">
                                                            {{ ucfirst($version->status) }}
                                                        </span>
                                                    </h6>
                                                    <small class="text-muted">
                                                        @if($version->effective_date)
                                                            Effective: {{ $version->effective_date->format('M d, Y') }} |
                                                        @endif
                                                        Created: {{ $version->created_at->format('M d, Y h:i A') }}
                                                        @if($version->created_by_user)
                                                            by {{ $version->created_by_user->name }}
                                                        @endif
                                                    </small>
                                                </div>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="{{ route('admin.policies.show', $version) }}" class="btn btn-outline-primary btn-sm">
                                                        <i class="fas fa-eye"></i> View
                                                    </a>
                                                    @can('policies.manage')
                                                        @if(!$version->is_latest_version)
                                                            <a href="{{ route('admin.policies.edit', $version) }}" class="btn btn-outline-warning btn-sm">
                                                                <i class="fas fa-edit"></i> Edit
                                                            </a>
                                                        @endif
                                                        <button type="button" class="btn btn-outline-info btn-sm" 
                                                                onclick="compareVersions({{ $version->id }})">
                                                            <i class="fas fa-code-branch"></i> Compare
                                                        </button>
                                                        @if($version->status === 'draft' && !$version->is_latest_version)
                                                            <form action="{{ route('admin.policies.destroy', $version) }}" method="POST" class="d-inline">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-outline-danger btn-sm"
                                                                        onclick="return confirm('Delete this version? This action cannot be undone.')">
                                                                    <i class="fas fa-trash"></i>
                                                                </button>
                                                            </form>
                                                        @endif
                                                    @endcan
                                                </div>
                                            </div>
                                            
                                            <div class="card-body">
                                                @if($version->version_notes)
                                                    <div class="mb-3">
                                                        <strong>Version Notes:</strong>
                                                        <p class="mb-2 text-muted">{{ $version->version_notes }}</p>
                                                    </div>
                                                @endif
                                                
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="mb-2">
                                                            <strong>Status:</strong>
                                                            <span class="badge badge-{{ $version->status === 'published' ? 'success' : ($version->status === 'draft' ? 'warning' : 'secondary') }} ms-2">
                                                                {{ ucfirst($version->status) }}
                                                            </span>
                                                        </div>
                                                        @if($version->published_at)
                                                            <div class="mb-2">
                                                                <strong>Published:</strong>
                                                                <span class="ms-2">{{ $version->published_at->format('M d, Y h:i A') }}</span>
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div class="col-md-6">
                                                        @if($version->effective_date)
                                                            <div class="mb-2">
                                                                <strong>Effective Date:</strong>
                                                                <span class="ms-2">{{ $version->effective_date->format('M d, Y') }}</span>
                                                                @if($version->effective_date->isFuture())
                                                                    <span class="badge badge-info ms-1">Future</span>
                                                                @elseif($version->effective_date->isToday())
                                                                    <span class="badge badge-success ms-1">Today</span>
                                                                @else
                                                                    <span class="badge badge-secondary ms-1">Active</span>
                                                                @endif
                                                            </div>
                                                        @endif
                                                        <div class="mb-2">
                                                            <strong>Content Length:</strong>
                                                            <span class="ms-2">{{ number_format(strlen($version->content)) }} characters</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <!-- Content Preview -->
                                                <div class="mt-3">
                                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                                        <strong>Content Preview:</strong>
                                                        <button type="button" class="btn btn-sm btn-outline-secondary" 
                                                                onclick="toggleContentPreview({{ $version->id }})">
                                                            <i class="fas fa-eye"></i> Toggle Preview
                                                        </button>
                                                    </div>
                                                    <div id="content-preview-{{ $version->id }}" class="content-preview d-none">
                                                        <div class="bg-light p-3 rounded" style="max-height: 200px; overflow-y: auto;">
                                                            <div class="rendered-content">{{ Str::limit(strip_tags($version->content), 500) }}</div>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <!-- Statistics -->
                                                @if($version->is_latest_version)
                                                    <div class="mt-3">
                                                        <div class="row text-center">
                                                            <div class="col-4">
                                                                <div class="border-end">
                                                                    <div class="h6 mb-0 text-primary">{{ $version->views_count ?? 0 }}</div>
                                                                    <small class="text-muted">Views</small>
                                                                </div>
                                                            </div>
                                                            <div class="col-4">
                                                                <div class="border-end">
                                                                    <div class="h6 mb-0 text-success">{{ $version->acknowledgments_count ?? 0 }}</div>
                                                                    <small class="text-muted">Acknowledged</small>
                                                                </div>
                                                            </div>
                                                            <div class="col-4">
                                                                <div class="h6 mb-0 text-info">{{ $version->downloads_count ?? 0 }}</div>
                                                                <small class="text-muted">Downloads</small>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        <!-- Pagination -->
                        @if($versions instanceof \Illuminate\Pagination\LengthAwarePaginator)
                            <div class="d-flex justify-content-center mt-4">
                                {{ $versions->links() }}
                            </div>
                        @endif
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-history fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No versions found</h5>
                            <p class="text-muted">This policy doesn't have any versions yet.</p>
                            @can('policies.manage')
                                <a href="{{ route('admin.policies.create-version', $policy) }}" class="btn btn-primary">
                                    <i class="fas fa-plus"></i> Create First Version
                                </a>
                            @endcan
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Version Comparison Modal -->
<div class="modal fade" id="compareModal" tabindex="-1" aria-labelledby="compareModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="compareModalLabel">Compare Versions</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="version1Select" class="form-label">Select First Version:</label>
                        <select class="form-select" id="version1Select">
                            <option value="">Choose version...</option>
                            @foreach($versions as $version)
                                <option value="{{ $version->id }}" data-version="{{ $version->version }}">
                                    Version {{ $version->version }} ({{ $version->status }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="version2Select" class="form-label">Select Second Version:</label>
                        <select class="form-select" id="version2Select">
                            <option value="">Choose version...</option>
                            @foreach($versions as $version)
                                <option value="{{ $version->id }}" data-version="{{ $version->version }}">
                                    Version {{ $version->version }} ({{ $version->status }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                
                <div class="text-center mb-3">
                    <button type="button" class="btn btn-primary" id="compareBtn" disabled>
                        <i class="fas fa-code-branch"></i> Compare Versions
                    </button>
                </div>
                
                <div id="comparisonResult" class="d-none">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header bg-info text-white">
                                    <h6 class="mb-0" id="version1Title">Version 1</h6>
                                </div>
                                <div class="card-body">
                                    <div id="version1Content" class="version-content"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header bg-success text-white">
                                    <h6 class="mb-0" id="version2Title">Version 2</h6>
                                </div>
                                <div class="card-body">
                                    <div id="version2Content" class="version-content"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-3">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0">Comparison Summary</h6>
                            </div>
                            <div class="card-body">
                                <div id="comparisonSummary"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.timeline-container {
    position: relative;
    padding-left: 30px;
}

.timeline-item {
    position: relative;
    margin-bottom: 30px;
    padding-bottom: 20px;
}

.timeline-item:not(:last-child)::before {
    content: '';
    position: absolute;
    left: -15px;
    top: 40px;
    width: 2px;
    height: calc(100% + 10px);
    background-color: #e3e6f0;
}

.timeline-marker {
    position: absolute;
    left: -25px;
    top: 10px;
    width: 20px;
    height: 20px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 10px;
    font-weight: bold;
    color: white;
    z-index: 1;
}

.timeline-marker.published {
    background-color: #28a745;
}

.timeline-marker.draft {
    background-color: #ffc107;
    color: #000;
}

.timeline-marker.inactive {
    background-color: #6c757d;
}

.timeline-marker .version-number {
    font-size: 8px;
}

.timeline-marker i {
    font-size: 10px;
}

.latest-version .timeline-marker {
    width: 24px;
    height: 24px;
    left: -27px;
    background-color: #007bff;
    box-shadow: 0 0 0 4px rgba(0, 123, 255, 0.2);
}

.timeline-content {
    margin-left: 20px;
}

.content-preview {
    transition: all 0.3s ease;
}

.version-content {
    max-height: 400px;
    overflow-y: auto;
    font-family: 'Courier New', monospace;
    font-size: 0.9em;
    line-height: 1.4;
    white-space: pre-wrap;
    background-color: #f8f9fa;
    padding: 15px;
    border-radius: 5px;
}

.comparison-highlight {
    background-color: #fff3cd;
    padding: 2px 4px;
    border-radius: 3px;
}

.comparison-added {
    background-color: #d4edda;
    padding: 2px 4px;
    border-radius: 3px;
}

.comparison-removed {
    background-color: #f8d7da;
    padding: 2px 4px;
    border-radius: 3px;
    text-decoration: line-through;
}

.badge {
    font-size: 0.8em;
}

.btn-group-sm > .btn, .btn-sm {
    font-size: 0.8rem;
}

.card-header h6 {
    font-weight: 600;
}

.text-muted {
    font-size: 0.9em;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const compareModal = new bootstrap.Modal(document.getElementById('compareModal'));
    const version1Select = document.getElementById('version1Select');
    const version2Select = document.getElementById('version2Select');
    const compareBtn = document.getElementById('compareBtn');
    const comparisonResult = document.getElementById('comparisonResult');
    
    // Version data for comparison
    const versionData = @json($versions->keyBy('id')->map(function($version) {
        return [
            'id' => $version->id,
            'version' => $version->version,
            'content' => $version->content,
            'status' => $version->status,
            'created_at' => $version->created_at->format('M d, Y h:i A'),
            'version_notes' => $version->version_notes
        ];
    }));
    
    // Toggle content preview
    window.toggleContentPreview = function(versionId) {
        const preview = document.getElementById(`content-preview-${versionId}`);
        preview.classList.toggle('d-none');
    };
    
    // Compare versions functionality
    window.compareVersions = function(versionId) {
        version1Select.value = versionId;
        updateCompareButton();
        compareModal.show();
    };
    
    // Update compare button state
    function updateCompareButton() {
        const version1 = version1Select.value;
        const version2 = version2Select.value;
        compareBtn.disabled = !version1 || !version2 || version1 === version2;
    }
    
    // Event listeners for version selects
    version1Select.addEventListener('change', updateCompareButton);
    version2Select.addEventListener('change', updateCompareButton);
    
    // Compare button click handler
    compareBtn.addEventListener('click', function() {
        const version1Id = version1Select.value;
        const version2Id = version2Select.value;
        
        if (!version1Id || !version2Id || version1Id === version2Id) {
            return;
        }
        
        const version1 = versionData[version1Id];
        const version2 = versionData[version2Id];
        
        if (!version1 || !version2) {
            alert('Error loading version data');
            return;
        }
        
        // Update comparison titles
        document.getElementById('version1Title').textContent = `Version ${version1.version} (${version1.status})`;
        document.getElementById('version2Title').textContent = `Version ${version2.version} (${version2.status})`;
        
        // Display content
        document.getElementById('version1Content').textContent = version1.content;
        document.getElementById('version2Content').textContent = version2.content;
        
        // Generate comparison summary
        const summary = generateComparisonSummary(version1, version2);
        document.getElementById('comparisonSummary').innerHTML = summary;
        
        // Show comparison result
        comparisonResult.classList.remove('d-none');
    });
    
    // Generate comparison summary
    function generateComparisonSummary(version1, version2) {
        const content1Length = version1.content.length;
        const content2Length = version2.content.length;
        const lengthDiff = content2Length - content1Length;
        
        let summary = `<div class="row">`;
        
        // Content length comparison
        summary += `<div class="col-md-4">`;
        summary += `<div class="text-center">`;
        summary += `<div class="h5 mb-1 ${lengthDiff > 0 ? 'text-success' : lengthDiff < 0 ? 'text-danger' : 'text-muted'}">`;
        summary += `${lengthDiff > 0 ? '+' : ''}${lengthDiff}`;
        summary += `</div>`;
        summary += `<small class="text-muted">Character Difference</small>`;
        summary += `</div>`;
        summary += `</div>`;
        
        // Version comparison
        summary += `<div class="col-md-4">`;
        summary += `<div class="text-center">`;
        summary += `<div class="h5 mb-1 text-info">`;
        summary += `${version1.version} → ${version2.version}`;
        summary += `</div>`;
        summary += `<small class="text-muted">Version Change</small>`;
        summary += `</div>`;
        summary += `</div>`;
        
        // Status comparison
        summary += `<div class="col-md-4">`;
        summary += `<div class="text-center">`;
        summary += `<div class="h5 mb-1">`;
        if (version1.status !== version2.status) {
            summary += `<span class="badge badge-${getStatusBadgeClass(version1.status)}">${version1.status}</span>`;
            summary += ` → `;
            summary += `<span class="badge badge-${getStatusBadgeClass(version2.status)}">${version2.status}</span>`;
        } else {
            summary += `<span class="badge badge-${getStatusBadgeClass(version2.status)}">${version2.status}</span>`;
        }
        summary += `</div>`;
        summary += `<small class="text-muted">Status Change</small>`;
        summary += `</div>`;
        summary += `</div>`;
        
        summary += `</div>`;
        
        // Additional details
        summary += `<hr>`;
        summary += `<div class="row">`;
        summary += `<div class="col-md-6">`;
        summary += `<strong>Version ${version1.version}:</strong><br>`;
        summary += `<small class="text-muted">Created: ${version1.created_at}</small><br>`;
        if (version1.version_notes) {
            summary += `<small class="text-muted">Notes: ${version1.version_notes}</small>`;
        }
        summary += `</div>`;
        summary += `<div class="col-md-6">`;
        summary += `<strong>Version ${version2.version}:</strong><br>`;
        summary += `<small class="text-muted">Created: ${version2.created_at}</small><br>`;
        if (version2.version_notes) {
            summary += `<small class="text-muted">Notes: ${version2.version_notes}</small>`;
        }
        summary += `</div>`;
        summary += `</div>`;
        
        return summary;
    }
    
    // Get status badge class
    function getStatusBadgeClass(status) {
        switch (status) {
            case 'published': return 'success';
            case 'draft': return 'warning';
            case 'archived': return 'secondary';
            default: return 'secondary';
        }
    }
});
</script>
@endpush
@endsection