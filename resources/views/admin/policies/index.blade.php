@extends('layouts.admin')

@section('page-title', 'Policy Management')

@section('content')
<div class="container-fluid">
    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Policies
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total'] ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-file-alt fa-2x text-gray-300"></i>
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
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Published
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['published'] ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
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
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Draft
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['draft'] ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-edit fa-2x text-gray-300"></i>
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
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Categories
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['categories'] ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-tags fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Policies</h6>
                    @can('policies.manage')
                        <a href="{{ route('admin.policies.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Add New Policy
                        </a>
                    @endcan
                </div>
                
                <!-- Search and Filter Form -->
                <div class="card-body border-bottom">
                    <form method="GET" action="{{ route('admin.policies.index') }}" class="row g-3">
                        <div class="col-md-4">
                            <label for="search" class="form-label">Search</label>
                            <input type="text" class="form-control" id="search" name="search" 
                                   value="{{ request('search') }}" placeholder="Search policies...">
                        </div>
                        
                        <div class="col-md-2">
                            <label for="category" class="form-label">Category</label>
                            <select class="form-select" id="category" name="category">
                                <option value="">All Categories</option>
                                <option value="academic" {{ request('category') === 'academic' ? 'selected' : '' }}>Academic</option>
                                <option value="administrative" {{ request('category') === 'administrative' ? 'selected' : '' }}>Administrative</option>
                                <option value="student_affairs" {{ request('category') === 'student_affairs' ? 'selected' : '' }}>Student Affairs</option>
                                <option value="faculty" {{ request('category') === 'faculty' ? 'selected' : '' }}>Faculty</option>
                                <option value="financial" {{ request('category') === 'financial' ? 'selected' : '' }}>Financial</option>
                                <option value="disciplinary" {{ request('category') === 'disciplinary' ? 'selected' : '' }}>Disciplinary</option>
                                <option value="general" {{ request('category') === 'general' ? 'selected' : '' }}>General</option>
                            </select>
                        </div>
                        
                        <div class="col-md-2">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status">
                                <option value="">All Status</option>
                                <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                                <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Published</option>
                                <option value="archived" {{ request('status') === 'archived' ? 'selected' : '' }}>Archived</option>
                            </select>
                        </div>
                        
                        <div class="col-md-2">
                            <label for="version" class="form-label">Version</label>
                            <select class="form-select" id="version" name="version">
                                <option value="">All Versions</option>
                                <option value="latest" {{ request('version') === 'latest' ? 'selected' : '' }}>Latest Only</option>
                                <option value="all" {{ request('version') === 'all' ? 'selected' : '' }}>All Versions</option>
                            </select>
                        </div>
                        
                        <div class="col-md-2 d-flex align-items-end">
                            <div class="btn-group w-100">
                                <button type="submit" class="btn btn-outline-primary">
                                    <i class="fas fa-search"></i> Search
                                </button>
                                <a href="{{ route('admin.policies.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-times"></i>
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
                
                <div class="card-body">
                    @if($policies->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>Title</th>
                                        <th>Category</th>
                                        <th>Version</th>
                                        <th>Status</th>
                                        <th>Published Date</th>
                                        <th>Last Updated</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($policies as $policy)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div>
                                                        <div class="font-weight-bold">{{ $policy->title }}</div>
                                                        @if($policy->description)
                                                            <small class="text-muted">{{ Str::limit($policy->description, 60) }}</small>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge badge-{{ $policy->category === 'academic' ? 'primary' : ($policy->category === 'administrative' ? 'info' : ($policy->category === 'student_affairs' ? 'success' : ($policy->category === 'faculty' ? 'warning' : ($policy->category === 'financial' ? 'danger' : ($policy->category === 'disciplinary' ? 'dark' : 'secondary'))))) }}">
                                                    {{ ucfirst(str_replace('_', ' ', $policy->category)) }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <span class="badge badge-outline-primary me-2">v{{ $policy->version }}</span>
                                                    @if($policy->is_latest_version)
                                                        <small class="text-success"><i class="fas fa-check-circle"></i> Latest</small>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge badge-{{ $policy->status === 'published' ? 'success' : ($policy->status === 'draft' ? 'warning' : 'secondary') }}">
                                                    {{ ucfirst($policy->status) }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($policy->published_at)
                                                    {{ $policy->published_at->format('M d, Y') }}
                                                    <br><small class="text-muted">{{ $policy->published_at->format('h:i A') }}</small>
                                                @else
                                                    <span class="text-muted">Not published</span>
                                                @endif
                                            </td>
                                            <td>
                                                {{ $policy->updated_at->format('M d, Y') }}
                                                <br><small class="text-muted">{{ $policy->updated_at->format('h:i A') }}</small>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('admin.policies.show', $policy) }}" 
                                                       class="btn btn-info btn-sm" title="View Policy">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    
                                                    @can('policies.manage')
                                                        <a href="{{ route('admin.policies.edit', $policy) }}" 
                                                           class="btn btn-warning btn-sm" title="Edit Policy">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        
                                                        <div class="btn-group" role="group">
                                                            <button type="button" class="btn btn-secondary btn-sm dropdown-toggle" 
                                                                    data-bs-toggle="dropdown" aria-expanded="false" title="More Actions">
                                                                <i class="fas fa-ellipsis-v"></i>
                                                            </button>
                                                            <ul class="dropdown-menu">
                                                                @if($policy->status === 'draft')
                                                                    <li>
                                                                        <form action="{{ route('admin.policies.publish', $policy) }}" method="POST" class="d-inline">
                                                                            @csrf
                                                                            <button type="submit" class="dropdown-item text-success"
                                                                                    onclick="return confirm('Publish this policy?')">
                                                                                <i class="fas fa-check-circle"></i> Publish
                                                                            </button>
                                                                        </form>
                                                                    </li>
                                                                @elseif($policy->status === 'published')
                                                                    <li>
                                                                        <form action="{{ route('admin.policies.unpublish', $policy) }}" method="POST" class="d-inline">
                                                                            @csrf
                                                                            <button type="submit" class="dropdown-item text-warning"
                                                                                    onclick="return confirm('Unpublish this policy?')">
                                                                                <i class="fas fa-pause-circle"></i> Unpublish
                                                                            </button>
                                                                        </form>
                                                                    </li>
                                                                @endif
                                                                
                                                                <li>
                                                                    <form action="{{ route('admin.policies.create-version', $policy) }}" method="POST" class="d-inline">
                                                                        @csrf
                                                                        <button type="submit" class="dropdown-item text-info"
                                                                                onclick="return confirm('Create a new version of this policy?')">
                                                                            <i class="fas fa-copy"></i> New Version
                                                                        </button>
                                                                    </form>
                                                                </li>
                                                                
                                                                <li>
                                                                    <a href="{{ route('admin.policies.versions', $policy) }}" class="dropdown-item text-primary">
                                                                        <i class="fas fa-history"></i> View Versions
                                                                    </a>
                                                                </li>
                                                                
                                                                <li><hr class="dropdown-divider"></li>
                                                                
                                                                <li>
                                                                    <form action="{{ route('admin.policies.toggle-status', $policy) }}" method="POST" class="d-inline">
                                                                        @csrf
                                                                        @method('PATCH')
                                                                        <button type="submit" class="dropdown-item text-{{ $policy->status === 'active' ? 'secondary' : 'success' }}"
                                                                                onclick="return confirm('{{ $policy->status === 'active' ? 'Deactivate' : 'Activate' }} this policy?')">
                                                                            <i class="fas fa-{{ $policy->status === 'active' ? 'pause' : 'play' }}"></i>
                                                                            {{ $policy->status === 'active' ? 'Deactivate' : 'Activate' }}
                                                                        </button>
                                                                    </form>
                                                                </li>
                                                                
                                                                <li>
                                                                    <form action="{{ route('admin.policies.destroy', $policy) }}" method="POST" class="d-inline">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                        <button type="submit" class="dropdown-item text-danger"
                                                                                onclick="return confirm('Are you sure you want to delete this policy? This action cannot be undone.')">
                                                                            <i class="fas fa-trash"></i> Delete
                                                                        </button>
                                                                    </form>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    @endcan
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Pagination -->
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div>
                                <p class="text-sm text-gray-700">
                                    Showing {{ $policies->firstItem() }} to {{ $policies->lastItem() }} of {{ $policies->total() }} results
                                </p>
                            </div>
                            <div>
                                {{ $policies->appends(request()->query())->links() }}
                            </div>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-file-alt fa-3x text-gray-300 mb-3"></i>
                            <h5 class="text-gray-600">No Policies Found</h5>
                            <p class="text-gray-500 mb-4">There are no policies matching your search criteria.</p>
                            @can('policies.manage')
                                <a href="{{ route('admin.policies.create') }}" class="btn btn-primary">
                                    <i class="fas fa-plus"></i> Create First Policy
                                </a>
                            @endcan
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.badge-outline-primary {
    color: #007bff;
    border: 1px solid #007bff;
    background-color: transparent;
}

.table td {
    vertical-align: middle;
}

.btn-group .dropdown-menu {
    min-width: 160px;
}

.dropdown-item i {
    width: 16px;
    margin-right: 8px;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-submit form on filter change
    const filterSelects = document.querySelectorAll('#category, #status, #version');
    filterSelects.forEach(select => {
        select.addEventListener('change', function() {
            this.form.submit();
        });
    });
    
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[title]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>
@endpush
@endsection