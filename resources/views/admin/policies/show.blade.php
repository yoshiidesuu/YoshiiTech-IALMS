@extends('layouts.admin')

@section('page-title', 'Policy Details')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <div>
                        <h6 class="m-0 font-weight-bold text-primary">{{ $policy->title }}</h6>
                        <small class="text-muted">
                            Version {{ $policy->version }} | 
                            <span class="badge badge-{{ $policy->status === 'published' ? 'success' : ($policy->status === 'draft' ? 'warning' : 'secondary') }}">
                                {{ ucfirst($policy->status) }}
                            </span>
                        </small>
                    </div>
                    <div>
                        @can('policies.manage')
                            <a href="{{ route('admin.policies.edit', $policy) }}" class="btn btn-warning btn-sm me-2">
                                <i class="fas fa-edit"></i> Edit Policy
                            </a>
                        @endcan
                        <a href="{{ route('admin.policies.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Back to Policies
                        </a>
                    </div>
                </div>
                
                <div class="card-body">
                    <div class="row">
                        <!-- Main Content -->
                        <div class="col-lg-8">
                            <!-- Policy Information -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h6 class="m-0 font-weight-bold text-primary">Policy Information</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <strong>Category:</strong>
                                            <span class="badge badge-{{ $policy->category === 'academic' ? 'primary' : ($policy->category === 'administrative' ? 'info' : ($policy->category === 'student_affairs' ? 'success' : ($policy->category === 'faculty' ? 'warning' : ($policy->category === 'financial' ? 'danger' : ($policy->category === 'disciplinary' ? 'dark' : 'secondary'))))) }} ms-2">
                                                {{ ucfirst(str_replace('_', ' ', $policy->category)) }}
                                            </span>
                                        </div>
                                        <div class="col-md-6">
                                            <strong>Priority:</strong>
                                            <span class="badge badge-{{ ($policy->priority ?? 'medium') === 'critical' ? 'danger' : (($policy->priority ?? 'medium') === 'high' ? 'warning' : (($policy->priority ?? 'medium') === 'medium' ? 'info' : 'secondary')) }} ms-2">
                                                {{ ucfirst($policy->priority ?? 'Medium') }}
                                            </span>
                                        </div>
                                    </div>
                                    
                                    @if($policy->description)
                                        <div class="mb-3">
                                            <strong>Description:</strong>
                                            <p class="mt-2 text-muted">{{ $policy->description }}</p>
                                        </div>
                                    @endif
                                    
                                    <div class="row mb-3">
                                        <div class="col-md-4">
                                            <strong>Created:</strong><br>
                                            <small class="text-muted">{{ $policy->created_at->format('M d, Y h:i A') }}</small>
                                        </div>
                                        <div class="col-md-4">
                                            <strong>Last Updated:</strong><br>
                                            <small class="text-muted">{{ $policy->updated_at->format('M d, Y h:i A') }}</small>
                                        </div>
                                        <div class="col-md-4">
                                            @if($policy->published_at)
                                                <strong>Published:</strong><br>
                                                <small class="text-muted">{{ $policy->published_at->format('M d, Y h:i A') }}</small>
                                            @else
                                                <strong>Status:</strong><br>
                                                <small class="text-muted">Not published</small>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    @if($policy->effective_date)
                                        <div class="mb-3">
                                            <strong>Effective Date:</strong>
                                            <span class="ms-2">{{ $policy->effective_date->format('M d, Y') }}</span>
                                            @if($policy->effective_date->isFuture())
                                                <span class="badge badge-info ms-2">Future</span>
                                            @elseif($policy->effective_date->isToday())
                                                <span class="badge badge-success ms-2">Today</span>
                                            @else
                                                <span class="badge badge-secondary ms-2">Active</span>
                                            @endif
                                        </div>
                                    @endif
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" disabled 
                                                       {{ ($policy->requires_acknowledgment ?? false) ? 'checked' : '' }}>
                                                <label class="form-check-label">
                                                    Requires Acknowledgment
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" disabled 
                                                       {{ $policy->is_latest_version ? 'checked' : '' }}>
                                                <label class="form-check-label">
                                                    Latest Version
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Policy Content -->
                            <div class="card mb-4">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h6 class="m-0 font-weight-bold text-primary">Policy Content</h6>
                                    <div class="btn-group btn-group-sm">
                                        <button type="button" class="btn btn-outline-primary" id="rawViewBtn">
                                            <i class="fas fa-code"></i> Raw
                                        </button>
                                        <button type="button" class="btn btn-primary" id="renderedViewBtn">
                                            <i class="fas fa-eye"></i> Rendered
                                        </button>
                                        <button type="button" class="btn btn-outline-secondary" id="printBtn">
                                            <i class="fas fa-print"></i> Print
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div id="renderedContent" class="policy-content">
                                        <!-- Rendered content will be inserted here -->
                                    </div>
                                    <div id="rawContent" class="d-none">
                                        <pre class="bg-light p-3 rounded"><code>{{ $policy->content }}</code></pre>
                                    </div>
                                </div>
                            </div>
                            
                            @if($policy->version_notes)
                                <!-- Version Notes -->
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <h6 class="m-0 font-weight-bold text-primary">Version Notes</h6>
                                    </div>
                                    <div class="card-body">
                                        <p class="mb-0">{{ $policy->version_notes }}</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                        
                        <!-- Sidebar -->
                        <div class="col-lg-4">
                            <!-- Quick Actions -->
                            @can('policies.manage')
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <h6 class="m-0 font-weight-bold text-primary">Quick Actions</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="d-grid gap-2">
                                            @if($policy->status === 'draft')
                                                <form action="{{ route('admin.policies.publish', $policy) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-success w-100"
                                                            onclick="return confirm('Publish this policy?')">
                                                        <i class="fas fa-check-circle"></i> Publish Policy
                                                    </button>
                                                </form>
                                            @elseif($policy->status === 'published')
                                                <form action="{{ route('admin.policies.unpublish', $policy) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-warning w-100"
                                                            onclick="return confirm('Unpublish this policy?')">
                                                        <i class="fas fa-pause-circle"></i> Unpublish Policy
                                                    </button>
                                                </form>
                                            @endif
                                            
                                            <form action="{{ route('admin.policies.create-version', $policy) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-info w-100"
                                                        onclick="return confirm('Create a new version of this policy?')">
                                                    <i class="fas fa-copy"></i> Create New Version
                                                </button>
                                            </form>
                                            
                                            <a href="{{ route('admin.policies.versions', $policy) }}" class="btn btn-outline-primary w-100">
                                                <i class="fas fa-history"></i> View All Versions
                                            </a>
                                            
                                            <hr>
                                            
                                            <form action="{{ route('admin.policies.toggle-status', $policy) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-outline-{{ $policy->status === 'active' ? 'secondary' : 'success' }} w-100"
                                                        onclick="return confirm('{{ $policy->status === 'active' ? 'Deactivate' : 'Activate' }} this policy?')">
                                                    <i class="fas fa-{{ $policy->status === 'active' ? 'pause' : 'play' }}"></i>
                                                    {{ $policy->status === 'active' ? 'Deactivate' : 'Activate' }} Policy
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endcan
                            
                            <!-- Version Information -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h6 class="m-0 font-weight-bold text-primary">Version Information</h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <strong>Current Version:</strong>
                                        <span class="badge badge-primary ms-2">v{{ $policy->version }}</span>
                                        @if($policy->is_latest_version)
                                            <span class="badge badge-success ms-1">Latest</span>
                                        @endif
                                    </div>
                                    
                                    @if($policy->parent_policy_id)
                                        <div class="mb-3">
                                            <strong>Previous Version:</strong>
                                            <a href="{{ route('admin.policies.show', $policy->parent_policy_id) }}" class="ms-2">
                                                View Previous
                                            </a>
                                        </div>
                                    @endif
                                    
                                    @if($policy->versions && $policy->versions->count() > 1)
                                        <div class="mb-3">
                                            <strong>Total Versions:</strong>
                                            <span class="ms-2">{{ $policy->versions->count() }}</span>
                                        </div>
                                    @endif
                                    
                                    <div class="mb-3">
                                        <strong>Created By:</strong>
                                        <span class="ms-2">{{ $policy->created_by_user->name ?? 'System' }}</span>
                                    </div>
                                    
                                    @if($policy->updated_by_user && $policy->updated_by_user->id !== $policy->created_by_user->id)
                                        <div class="mb-3">
                                            <strong>Last Updated By:</strong>
                                            <span class="ms-2">{{ $policy->updated_by_user->name }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            
                            <!-- Statistics -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h6 class="m-0 font-weight-bold text-primary">Statistics</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row text-center">
                                        <div class="col-6">
                                            <div class="border-end">
                                                <div class="h4 mb-0 text-primary">{{ $policy->views_count ?? 0 }}</div>
                                                <small class="text-muted">Views</small>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="h4 mb-0 text-success">{{ $policy->acknowledgments_count ?? 0 }}</div>
                                            <small class="text-muted">Acknowledged</small>
                                        </div>
                                    </div>
                                    
                                    @if($policy->requires_acknowledgment)
                                        <hr>
                                        <div class="text-center">
                                            <div class="progress mb-2">
                                                @php
                                                    $totalUsers = 100; // This should come from actual user count
                                                    $acknowledgedUsers = $policy->acknowledgments_count ?? 0;
                                                    $percentage = $totalUsers > 0 ? ($acknowledgedUsers / $totalUsers) * 100 : 0;
                                                @endphp
                                                <div class="progress-bar bg-success" role="progressbar" 
                                                     style="width: {{ $percentage }}%" 
                                                     aria-valuenow="{{ $percentage }}" 
                                                     aria-valuemin="0" aria-valuemax="100">
                                                </div>
                                            </div>
                                            <small class="text-muted">{{ number_format($percentage, 1) }}% Acknowledgment Rate</small>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            
                            <!-- Recent Activity -->
                            @if($policy->activities && $policy->activities->count() > 0)
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <h6 class="m-0 font-weight-bold text-primary">Recent Activity</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="timeline">
                                            @foreach($policy->activities->take(5) as $activity)
                                                <div class="timeline-item">
                                                    <div class="timeline-marker"></div>
                                                    <div class="timeline-content">
                                                        <div class="d-flex justify-content-between align-items-start">
                                                            <div>
                                                                <strong>{{ $activity->description }}</strong>
                                                                @if($activity->causer)
                                                                    <br><small class="text-muted">by {{ $activity->causer->name }}</small>
                                                                @endif
                                                            </div>
                                                            <small class="text-muted">{{ $activity->created_at->diffForHumans() }}</small>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Print Modal -->
<div class="modal fade" id="printModal" tabindex="-1" aria-labelledby="printModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="printModalLabel">Print Policy</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="printContent" class="print-content">
                    <!-- Print content will be inserted here -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="confirmPrintBtn">
                    <i class="fas fa-print"></i> Print
                </button>
            </div>
        </div>
    </div>
</div>

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/marked@4.0.10/lib/marked.min.js" rel="preload" as="script">
<style>
.policy-content {
    line-height: 1.6;
    font-size: 1rem;
}

.policy-content h1, .policy-content h2, .policy-content h3, 
.policy-content h4, .policy-content h5, .policy-content h6 {
    margin-top: 1.5rem;
    margin-bottom: 1rem;
    font-weight: 600;
}

.policy-content h1 { font-size: 1.75rem; }
.policy-content h2 { font-size: 1.5rem; }
.policy-content h3 { font-size: 1.25rem; }
.policy-content h4 { font-size: 1.1rem; }
.policy-content h5 { font-size: 1rem; }
.policy-content h6 { font-size: 0.9rem; }

.policy-content p {
    margin-bottom: 1rem;
}

.policy-content ul, .policy-content ol {
    margin-bottom: 1rem;
    padding-left: 2rem;
}

.policy-content li {
    margin-bottom: 0.5rem;
}

.policy-content blockquote {
    border-left: 4px solid #007bff;
    padding-left: 1rem;
    margin: 1rem 0;
    font-style: italic;
    background-color: #f8f9fa;
    padding: 1rem;
}

.policy-content code {
    background-color: #f8f9fa;
    padding: 0.2rem 0.4rem;
    border-radius: 0.25rem;
    font-size: 0.9em;
}

.policy-content pre {
    background-color: #f8f9fa;
    padding: 1rem;
    border-radius: 0.25rem;
    overflow-x: auto;
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

.timeline-marker {
    position: absolute;
    left: -6px;
    top: 0;
    width: 10px;
    height: 10px;
    border-radius: 50%;
    background-color: #4e73df;
}

.timeline-content {
    padding-left: 15px;
}

.print-content {
    font-family: 'Times New Roman', serif;
    line-height: 1.6;
    color: #000;
}

.print-content h1, .print-content h2, .print-content h3 {
    color: #000;
    page-break-after: avoid;
}

@media print {
    .print-content {
        font-size: 12pt;
    }
    
    .print-content h1 { font-size: 18pt; }
    .print-content h2 { font-size: 16pt; }
    .print-content h3 { font-size: 14pt; }
}

.badge {
    font-size: 0.8em;
}

.progress {
    height: 8px;
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/marked@4.0.10/lib/marked.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const policyContent = @json($policy->content);
    const renderedContentDiv = document.getElementById('renderedContent');
    const rawContentDiv = document.getElementById('rawContent');
    const rawViewBtn = document.getElementById('rawViewBtn');
    const renderedViewBtn = document.getElementById('renderedViewBtn');
    const printBtn = document.getElementById('printBtn');
    const printModal = new bootstrap.Modal(document.getElementById('printModal'));
    const printContent = document.getElementById('printContent');
    const confirmPrintBtn = document.getElementById('confirmPrintBtn');
    
    // Render markdown content
    function renderContent() {
        try {
            const htmlContent = marked.parse(policyContent);
            renderedContentDiv.innerHTML = htmlContent;
        } catch (error) {
            // Fallback to plain text with line breaks
            renderedContentDiv.innerHTML = policyContent.replace(/\n/g, '<br>');
        }
    }
    
    // View toggle functionality
    rawViewBtn.addEventListener('click', function() {
        renderedContentDiv.classList.add('d-none');
        rawContentDiv.classList.remove('d-none');
        rawViewBtn.classList.remove('btn-outline-primary');
        rawViewBtn.classList.add('btn-primary');
        renderedViewBtn.classList.remove('btn-primary');
        renderedViewBtn.classList.add('btn-outline-primary');
    });
    
    renderedViewBtn.addEventListener('click', function() {
        rawContentDiv.classList.add('d-none');
        renderedContentDiv.classList.remove('d-none');
        renderedViewBtn.classList.remove('btn-outline-primary');
        renderedViewBtn.classList.add('btn-primary');
        rawViewBtn.classList.remove('btn-primary');
        rawViewBtn.classList.add('btn-outline-primary');
    });
    
    // Print functionality
    printBtn.addEventListener('click', function() {
        const printHtml = `
            <div class="text-center mb-4">
                <h1>{{ $policy->title }}</h1>
                <p><strong>Version:</strong> {{ $policy->version }} | 
                   <strong>Category:</strong> {{ ucfirst(str_replace('_', ' ', $policy->category)) }} | 
                   <strong>Status:</strong> {{ ucfirst($policy->status) }}</p>
                @if($policy->effective_date)
                    <p><strong>Effective Date:</strong> {{ $policy->effective_date->format('F d, Y') }}</p>
                @endif
                <hr>
            </div>
            <div class="policy-content">
                ${renderedContentDiv.innerHTML}
            </div>
            @if($policy->version_notes)
                <div class="mt-4">
                    <h3>Version Notes</h3>
                    <p>{{ $policy->version_notes }}</p>
                </div>
            @endif
            <div class="mt-4 text-center">
                <hr>
                <small>Generated on {{ now()->format('F d, Y \\a\\t h:i A') }}</small>
            </div>
        `;
        
        printContent.innerHTML = printHtml;
        printModal.show();
    });
    
    confirmPrintBtn.addEventListener('click', function() {
        const printWindow = window.open('', '_blank');
        printWindow.document.write(`
            <!DOCTYPE html>
            <html>
            <head>
                <title>{{ $policy->title }} - Version {{ $policy->version }}</title>
                <style>
                    body { font-family: 'Times New Roman', serif; line-height: 1.6; margin: 40px; }
                    h1, h2, h3, h4, h5, h6 { color: #000; page-break-after: avoid; }
                    h1 { font-size: 24pt; text-align: center; }
                    h2 { font-size: 18pt; }
                    h3 { font-size: 16pt; }
                    p { margin-bottom: 12pt; }
                    ul, ol { margin-bottom: 12pt; }
                    li { margin-bottom: 6pt; }
                    hr { border: 1px solid #000; }
                    .text-center { text-align: center; }
                    .mt-4 { margin-top: 24pt; }
                    .mb-4 { margin-bottom: 24pt; }
                </style>
            </head>
            <body>
                ${printContent.innerHTML}
            </body>
            </html>
        `);
        printWindow.document.close();
        printWindow.print();
        printModal.hide();
    });
    
    // Initialize content rendering
    renderContent();
    
    // Track policy view (you can implement this in your controller)
    // This is just a placeholder for analytics
    fetch('{{ route('admin.policies.track-view', $policy) }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
        }
    }).catch(error => {
        // Silently handle tracking errors
        console.log('View tracking failed:', error);
    });
});
</script>
@endpush
@endsection