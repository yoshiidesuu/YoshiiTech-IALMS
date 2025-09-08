@extends('layouts.admin')

@section('page-title', 'Subjects Management')

@section('content')
<div class="container-fluid">
    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $stats['total'] ?? 0 }}</h4>
                            <p class="mb-0">Total Subjects</p>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-book fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $stats['active'] ?? 0 }}</h4>
                            <p class="mb-0">Active Subjects</p>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-check-circle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $stats['with_lab'] ?? 0 }}</h4>
                            <p class="mb-0">With Laboratory</p>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-cpu fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $stats['total_credits'] ?? 0 }}</h4>
                            <p class="mb-0">Total Credits</p>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-award fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Subjects Management</h5>
                    @can('subjects.manage')
                        <a href="{{ route('admin.subjects.create') }}" class="btn btn-primary">
                            <i class="bi bi-plus"></i> Add New Subject
                        </a>
                    @endcan
                </div>
                <div class="card-body">
                    <!-- Search and Filter Form -->
                    <form method="GET" action="{{ route('admin.subjects.index') }}" class="mb-4">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label for="search" class="form-label">Search</label>
                                <input type="text" class="form-control" id="search" name="search" 
                                       value="{{ request('search') }}" placeholder="Search subjects...">
                            </div>
                            <div class="col-md-2">
                                <label for="category" class="form-label">Category</label>
                                <select class="form-select" id="category" name="category">
                                    <option value="">All Categories</option>
                                    <option value="core" {{ request('category') === 'core' ? 'selected' : '' }}>Core</option>
                                    <option value="major" {{ request('category') === 'major' ? 'selected' : '' }}>Major</option>
                                    <option value="minor" {{ request('category') === 'minor' ? 'selected' : '' }}>Minor</option>
                                    <option value="elective" {{ request('category') === 'elective' ? 'selected' : '' }}>Elective</option>
                                    <option value="general_education" {{ request('category') === 'general_education' ? 'selected' : '' }}>General Education</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="department" class="form-label">Department</label>
                                <select class="form-select" id="department" name="department">
                                    <option value="">All Departments</option>
                                    @foreach($departments as $dept)
                                        <option value="{{ $dept }}" {{ request('department') === $dept ? 'selected' : '' }}>
                                            {{ $dept }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="year_level" class="form-label">Year Level</label>
                                <select class="form-select" id="year_level" name="year_level">
                                    <option value="">All Levels</option>
                                    <option value="1" {{ request('year_level') === '1' ? 'selected' : '' }}>1st Year</option>
                                    <option value="2" {{ request('year_level') === '2' ? 'selected' : '' }}>2nd Year</option>
                                    <option value="3" {{ request('year_level') === '3' ? 'selected' : '' }}>3rd Year</option>
                                    <option value="4" {{ request('year_level') === '4' ? 'selected' : '' }}>4th Year</option>
                                </select>
                            </div>
                            <div class="col-md-1">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="">All</option>
                                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">&nbsp;</label>
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-outline-primary">
                                        <i class="bi bi-search"></i> Search
                                    </button>
                                    <a href="{{ route('admin.subjects.index') }}" class="btn btn-outline-secondary">
                                        <i class="bi bi-x-circle"></i> Clear
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="row g-3 mt-2">
                            <div class="col-md-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="laboratory" name="laboratory" value="1" 
                                           {{ request('laboratory') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="laboratory">
                                        Has Laboratory
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="prerequisites" name="prerequisites" value="1" 
                                           {{ request('prerequisites') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="prerequisites">
                                        Has Prerequisites
                                    </label>
                                </div>
                            </div>
                        </div>
                    </form>

                    <!-- Subjects Table -->
                    @if($subjects->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Code</th>
                                        <th>Subject Name</th>
                                        <th>Category</th>
                                        <th>Credits</th>
                                        <th>Year Level</th>
                                        <th>Prerequisites</th>
                                        <th>Capacity</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($subjects as $subject)
                                        <tr>
                                            <td>
                                                <code class="bg-light px-2 py-1 rounded">{{ $subject->code }}</code>
                                            </td>
                                            <td>
                                                <div>
                                                    <strong>{{ $subject->name }}</strong>
                                                    @if($subject->has_laboratory)
                                                        <span class="badge bg-info ms-1">Lab</span>
                                                    @endif
                                                </div>
                                                @if($subject->department)
                                                    <small class="text-muted">{{ $subject->department }}</small>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $subject->category === 'core' ? 'primary' : ($subject->category === 'major' ? 'success' : 'secondary') }}">
                                                    {{ ucfirst(str_replace('_', ' ', $subject->category)) }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="text-center">
                                                    <strong>{{ $subject->credits }}</strong>
                                                    @if($subject->laboratory_hours > 0)
                                                        <br><small class="text-muted">+{{ $subject->laboratory_hours }}h lab</small>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                @if($subject->year_level)
                                                    <span class="badge bg-outline-primary">{{ $subject->year_level }}{{ $subject->year_level == 1 ? 'st' : ($subject->year_level == 2 ? 'nd' : ($subject->year_level == 3 ? 'rd' : 'th')) }} Year</span>
                                                @else
                                                    <span class="text-muted">Any</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($subject->prerequisites && $subject->prerequisites->count() > 0)
                                                    <div class="prerequisite-list">
                                                        @foreach($subject->prerequisites->take(2) as $prereq)
                                                            <span class="badge bg-light text-dark border me-1 mb-1">{{ $prereq->code }}</span>
                                                        @endforeach
                                                        @if($subject->prerequisites->count() > 2)
                                                            <span class="badge bg-secondary">+{{ $subject->prerequisites->count() - 2 }} more</span>
                                                        @endif
                                                    </div>
                                                @else
                                                    <span class="text-muted">None</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($subject->capacity)
                                                    <div class="text-center">
                                                        <strong>{{ $subject->capacity }}</strong>
                                                        <br><small class="text-muted">students</small>
                                                    </div>
                                                @else
                                                    <span class="text-muted">Unlimited</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($subject->status === 'active')
                                                    <span class="badge bg-success">Active</span>
                                                @else
                                                    <span class="badge bg-secondary">Inactive</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="{{ route('admin.subjects.show', $subject) }}" 
                                                       class="btn btn-outline-info" title="View Details">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                    @can('subjects.manage')
                                                        <a href="{{ route('admin.subjects.edit', $subject) }}" 
                                                           class="btn btn-outline-warning" title="Edit">
                                                            <i class="bi bi-pencil"></i>
                                                        </a>
                                                        <form action="{{ route('admin.subjects.toggle-status', $subject) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            @method('PATCH')
                                                            <button type="submit" 
                                                                    class="btn btn-outline-{{ $subject->status === 'active' ? 'secondary' : 'success' }}" 
                                                                    title="{{ $subject->status === 'active' ? 'Deactivate' : 'Activate' }}"
                                                                    onclick="return confirm('{{ $subject->status === 'active' ? 'Deactivate' : 'Activate' }} this subject?')">
                                                                <i class="bi bi-{{ $subject->status === 'active' ? 'pause' : 'play' }}"></i>
                                                            </button>
                                                        </form>
                                                        <form action="{{ route('admin.subjects.destroy', $subject) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-outline-danger" title="Delete"
                                                                    onclick="return confirm('Are you sure you want to delete this subject? This action cannot be undone.')">
                                                                <i class="bi bi-trash"></i>
                                                            </button>
                                                        </form>
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
                                <small class="text-muted">
                                    Showing {{ $subjects->firstItem() }} to {{ $subjects->lastItem() }} of {{ $subjects->total() }} results
                                </small>
                            </div>
                            <div>
                                {{ $subjects->appends(request()->query())->links() }}
                            </div>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-book fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No Subjects Found</h5>
                            <p class="text-muted">No subjects match your current search criteria.</p>
                            @can('subjects.manage')
                                <a href="{{ route('admin.subjects.create') }}" class="btn btn-primary">
                                    <i class="bi bi-plus"></i> Add First Subject
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
.prerequisite-list {
    max-width: 150px;
}

.prerequisite-list .badge {
    font-size: 0.7rem;
    line-height: 1.2;
}

code {
    font-size: 0.85rem;
    font-weight: 600;
}

.table td {
    vertical-align: middle;
}

.btn-group-sm .btn {
    padding: 0.25rem 0.4rem;
    font-size: 0.75rem;
}
</style>
@endpush
@endsection