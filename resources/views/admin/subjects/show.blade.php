@extends('layouts.admin')

@section('page-title', 'Subject Details')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Subject Details: {{ $subject->code }}</h5>
                    <div class="btn-group">
                        @can('subjects.manage')
                            <a href="{{ route('admin.subjects.edit', $subject) }}" class="btn btn-outline-warning">
                                <i class="bi bi-pencil"></i> Edit Subject
                            </a>
                        @endcan
                        <a href="{{ route('admin.subjects.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left"></i> Back to Subjects
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Basic Information -->
                        <div class="col-md-8">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h6 class="mb-0">Basic Information</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label text-muted">Subject Code</label>
                                                <div>
                                                    <code class="bg-light px-3 py-2 rounded fs-5">{{ $subject->code }}</code>
                                                </div>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label class="form-label text-muted">Subject Name</label>
                                                <div class="fs-5 fw-bold">{{ $subject->name }}</div>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label class="form-label text-muted">Category</label>
                                                <div>
                                                    <span class="badge bg-{{ $subject->category === 'core' ? 'primary' : ($subject->category === 'major' ? 'success' : 'secondary') }} fs-6">
                                                        {{ ucfirst(str_replace('_', ' ', $subject->category)) }}
                                                    </span>
                                                </div>
                                            </div>
                                            
                                            @if($subject->department)
                                                <div class="mb-3">
                                                    <label class="form-label text-muted">Department</label>
                                                    <div class="fs-6">{{ $subject->department }}</div>
                                                </div>
                                            @endif
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label text-muted">Credits</label>
                                                <div class="fs-4 fw-bold text-primary">{{ $subject->credits }}</div>
                                            </div>
                                            
                                            @if($subject->year_level)
                                                <div class="mb-3">
                                                    <label class="form-label text-muted">Year Level</label>
                                                    <div>
                                                        <span class="badge bg-outline-primary fs-6">
                                                            {{ $subject->year_level }}{{ $subject->year_level == 1 ? 'st' : ($subject->year_level == 2 ? 'nd' : ($subject->year_level == 3 ? 'rd' : 'th')) }} Year
                                                        </span>
                                                    </div>
                                                </div>
                                            @endif
                                            
                                            <div class="mb-3">
                                                <label class="form-label text-muted">Status</label>
                                                <div>
                                                    <span class="badge bg-{{ $subject->status === 'active' ? 'success' : 'secondary' }} fs-6">
                                                        {{ ucfirst($subject->status) }}
                                                    </span>
                                                </div>
                                            </div>
                                            
                                            @if($subject->capacity)
                                                <div class="mb-3">
                                                    <label class="form-label text-muted">Class Capacity</label>
                                                    <div class="fs-5 fw-bold">{{ $subject->capacity }} students</div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    @if($subject->description)
                                        <div class="mt-3">
                                            <label class="form-label text-muted">Description</label>
                                            <div class="bg-light p-3 rounded">
                                                {{ $subject->description }}
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        <!-- Quick Stats -->
                        <div class="col-md-4">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h6 class="mb-0">Quick Stats</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row text-center">
                                        <div class="col-6">
                                            <div class="border-end">
                                                <div class="fs-4 fw-bold text-info">{{ $subject->lecture_hours ?? 3 }}</div>
                                                <small class="text-muted">Lecture Hours</small>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="fs-4 fw-bold text-warning">{{ $subject->laboratory_hours ?? 0 }}</div>
                                            <small class="text-muted">Lab Hours</small>
                                        </div>
                                    </div>
                                    
                                    @if($subject->has_laboratory)
                                        <div class="mt-3 text-center">
                                            <span class="badge bg-info">Has Laboratory Component</span>
                                        </div>
                                    @endif
                                    
                                    <hr>
                                    
                                    <div class="text-center">
                                        <div class="fs-5 fw-bold text-success">{{ $stats['enrolled_students'] ?? 0 }}</div>
                                        <small class="text-muted">Currently Enrolled</small>
                                    </div>
                                    
                                    <div class="text-center mt-2">
                                        <div class="fs-6 fw-bold text-primary">{{ $stats['active_sections'] ?? 0 }}</div>
                                        <small class="text-muted">Active Sections</small>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Quick Actions -->
                            @can('subjects.manage')
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <h6 class="mb-0">Quick Actions</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="d-grid gap-2">
                                            <form action="{{ route('admin.subjects.toggle-status', $subject) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-{{ $subject->status === 'active' ? 'outline-secondary' : 'outline-success' }} w-100"
                                                        onclick="return confirm('{{ $subject->status === 'active' ? 'Deactivate' : 'Activate' }} this subject?')">
                                                    <i class="bi bi-{{ $subject->status === 'active' ? 'pause' : 'play' }}"></i>
                                                    {{ $subject->status === 'active' ? 'Deactivate' : 'Activate' }} Subject
                                                </button>
                                            </form>
                                            
                                            <a href="{{ route('admin.subjects.edit', $subject) }}" class="btn btn-outline-warning">
                                                <i class="bi bi-pencil"></i> Edit Details
                                            </a>
                                            
                                            <button type="button" class="btn btn-outline-info" data-bs-toggle="modal" data-bs-target="#prerequisiteModal">
                                                <i class="bi bi-diagram-3"></i> View Prerequisites Tree
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endcan
                        </div>
                    </div>
                    
                    <!-- Prerequisites Section -->
                    @if($subject->prerequisites && $subject->prerequisites->count() > 0)
                        <div class="card mb-4">
                            <div class="card-header">
                                <h6 class="mb-0">Prerequisites</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    @foreach($subject->prerequisites as $prereq)
                                        <div class="col-md-4 mb-3">
                                            <div class="card border-primary">
                                                <div class="card-body p-3">
                                                    <div class="d-flex justify-content-between align-items-start">
                                                        <div>
                                                            <h6 class="card-title mb-1">
                                                                <code class="bg-light px-2 py-1 rounded">{{ $prereq->code }}</code>
                                                            </h6>
                                                            <p class="card-text small mb-2">{{ $prereq->name }}</p>
                                                            <div>
                                                                <span class="badge bg-{{ $prereq->category === 'core' ? 'primary' : ($prereq->category === 'major' ? 'success' : 'secondary') }}">
                                                                    {{ ucfirst(str_replace('_', ' ', $prereq->category)) }}
                                                                </span>
                                                                <span class="badge bg-light text-dark">{{ $prereq->credits }} credits</span>
                                                            </div>
                                                        </div>
                                                        <a href="{{ route('admin.subjects.show', $prereq) }}" class="btn btn-sm btn-outline-primary">
                                                            <i class="bi bi-eye"></i>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif
                    
                    <!-- Dependent Subjects (subjects that have this as prerequisite) -->
                    @if($dependentSubjects && $dependentSubjects->count() > 0)
                        <div class="card mb-4">
                            <div class="card-header">
                                <h6 class="mb-0">Dependent Subjects</h6>
                                <small class="text-muted">Subjects that require this subject as a prerequisite</small>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    @foreach($dependentSubjects as $dependent)
                                        <div class="col-md-4 mb-3">
                                            <div class="card border-success">
                                                <div class="card-body p-3">
                                                    <div class="d-flex justify-content-between align-items-start">
                                                        <div>
                                                            <h6 class="card-title mb-1">
                                                                <code class="bg-light px-2 py-1 rounded">{{ $dependent->code }}</code>
                                                            </h6>
                                                            <p class="card-text small mb-2">{{ $dependent->name }}</p>
                                                            <div>
                                                                <span class="badge bg-{{ $dependent->category === 'core' ? 'primary' : ($dependent->category === 'major' ? 'success' : 'secondary') }}">
                                                                    {{ ucfirst(str_replace('_', ' ', $dependent->category)) }}
                                                                </span>
                                                                <span class="badge bg-light text-dark">{{ $dependent->credits }} credits</span>
                                                            </div>
                                                        </div>
                                                        <a href="{{ route('admin.subjects.show', $dependent) }}" class="btn btn-sm btn-outline-success">
                                                            <i class="bi bi-eye"></i>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif
                    
                    <!-- System Information -->
                    <div class="card">
                        <div class="card-header">
                            <h6 class="mb-0">System Information</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <label class="form-label text-muted">Created At</label>
                                    <div>{{ $subject->created_at->format('M d, Y \a\t h:i A') }}</div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label text-muted">Last Updated</label>
                                    <div>{{ $subject->updated_at->format('M d, Y \a\t h:i A') }}</div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label text-muted">Subject ID</label>
                                    <div><code>{{ $subject->id }}</code></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Prerequisites Tree Modal -->
<div class="modal fade" id="prerequisiteModal" tabindex="-1" aria-labelledby="prerequisiteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="prerequisiteModalLabel">Prerequisites Tree: {{ $subject->code }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="prerequisite-tree" class="text-center">
                    <div class="spinner-border" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2">Loading prerequisites tree...</p>
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
code {
    font-size: 0.9rem;
    font-weight: 600;
}

.card-title code {
    font-size: 0.85rem;
}

.prerequisite-tree {
    font-family: monospace;
    white-space: pre-line;
    background-color: #f8f9fa;
    padding: 1rem;
    border-radius: 0.375rem;
    border: 1px solid #dee2e6;
}

.tree-node {
    margin: 0.25rem 0;
    padding: 0.25rem 0.5rem;
    border-left: 2px solid #007bff;
    margin-left: 1rem;
}

.tree-node.current {
    background-color: #e3f2fd;
    border-left-color: #2196f3;
    font-weight: bold;
}

.tree-connector {
    color: #6c757d;
    margin-right: 0.5rem;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const prerequisiteModal = document.getElementById('prerequisiteModal');
    const prerequisiteTree = document.getElementById('prerequisite-tree');
    
    if (prerequisiteModal) {
        prerequisiteModal.addEventListener('show.bs.modal', function() {
            loadPrerequisiteTree();
        });
    }
    
    function loadPrerequisiteTree() {
        // Simulate loading prerequisite tree
        setTimeout(() => {
            const treeHtml = generatePrerequisiteTree();
            prerequisiteTree.innerHTML = treeHtml;
        }, 1000);
    }
    
    function generatePrerequisiteTree() {
        // This would typically come from the server
        const tree = `
            <div class="prerequisite-tree">
                <div class="tree-node current">
                    <span class="tree-connector">📚</span>
                    <strong>{{ $subject->code }}</strong> - {{ $subject->name }}
                </div>
                @if($subject->prerequisites && $subject->prerequisites->count() > 0)
                    @foreach($subject->prerequisites as $prereq)
                        <div class="tree-node">
                            <span class="tree-connector">├─</span>
                            <strong>{{ $prereq->code }}</strong> - {{ $prereq->name }}
                            <!-- Add nested prerequisites here if available -->
                        </div>
                    @endforeach
                @else
                    <div class="tree-node">
                        <span class="tree-connector">└─</span>
                        <em class="text-muted">No prerequisites required</em>
                    </div>
                @endif
            </div>
            <div class="mt-3">
                <small class="text-muted">
                    <strong>Legend:</strong>
                    📚 Current Subject | ├─ Direct Prerequisite | └─ End of Branch
                </small>
            </div>
        `;
        
        return tree;
    }
});
</script>
@endpush
@endsection