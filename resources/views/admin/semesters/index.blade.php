@extends('layouts.admin')

@section('page-title', 'Semesters Management')

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
                            <p class="mb-0">Total Semesters</p>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-calendar-range fa-2x"></i>
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
                            <p class="mb-0">Active Semesters</p>
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
                            <h4 class="mb-0">{{ $stats['current'] ?? 0 }}</h4>
                            <p class="mb-0">Current Semester</p>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-star fa-2x"></i>
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
                            <h4 class="mb-0">{{ $stats['enrollment_open'] ?? 0 }}</h4>
                            <p class="mb-0">Enrollment Open</p>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-door-open fa-2x"></i>
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
                    <h5 class="mb-0">Semesters Management</h5>
                    @can('semesters.manage')
                        <a href="{{ route('admin.semesters.create') }}" class="btn btn-primary">
                            <i class="bi bi-plus"></i> Add New Semester
                        </a>
                    @endcan
                </div>
                <div class="card-body">
                    <!-- Search and Filter Form -->
                    <form method="GET" action="{{ route('admin.semesters.index') }}" class="mb-4">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label for="search" class="form-label">Search</label>
                                <input type="text" class="form-control" id="search" name="search" 
                                       value="{{ request('search') }}" placeholder="Search semesters...">
                            </div>
                            <div class="col-md-2">
                                <label for="academic_year" class="form-label">Academic Year</label>
                                <select class="form-select" id="academic_year" name="academic_year">
                                    <option value="">All Years</option>
                                    @foreach($academicYears as $year)
                                        <option value="{{ $year->id }}" {{ request('academic_year') == $year->id ? 'selected' : '' }}>
                                            {{ $year->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="">All Status</option>
                                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="term" class="form-label">Term</label>
                                <select class="form-select" id="term" name="term">
                                    <option value="">All Terms</option>
                                    <option value="1" {{ request('term') === '1' ? 'selected' : '' }}>1st Term</option>
                                    <option value="2" {{ request('term') === '2' ? 'selected' : '' }}>2nd Term</option>
                                    <option value="3" {{ request('term') === '3' ? 'selected' : '' }}>3rd Term</option>
                                    <option value="summer" {{ request('term') === 'summer' ? 'selected' : '' }}>Summer</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">&nbsp;</label>
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-outline-primary">
                                        <i class="bi bi-search"></i> Search
                                    </button>
                                    <a href="{{ route('admin.semesters.index') }}" class="btn btn-outline-secondary">
                                        <i class="bi bi-x-circle"></i> Clear
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>

                    <!-- Semesters Table -->
                    @if($semesters->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Name</th>
                                        <th>Academic Year</th>
                                        <th>Term</th>
                                        <th>Duration</th>
                                        <th>Enrollment Period</th>
                                        <th>Status</th>
                                        <th>Created</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($semesters as $semester)
                                        <tr>
                                            <td>
                                                <strong>{{ $semester->name }}</strong>
                                                @if($semester->is_current)
                                                    <span class="badge bg-primary ms-1">Current</span>
                                                @endif
                                            </td>
                                            <td>{{ $semester->academicYear->name ?? 'N/A' }}</td>
                                            <td>
                                                <span class="badge bg-info">{{ $semester->term_display }}</span>
                                            </td>
                                            <td>
                                                @if($semester->start_date && $semester->end_date)
                                                    <small>
                                                        {{ $semester->start_date->format('M d') }} - {{ $semester->end_date->format('M d, Y') }}<br>
                                                        <span class="text-muted">({{ $semester->start_date->diffInDays($semester->end_date) }} days)</span>
                                                    </small>
                                                @else
                                                    <span class="text-muted">Not set</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($semester->enrollment_start && $semester->enrollment_end)
                                                    <small>
                                                        {{ $semester->enrollment_start->format('M d') }} - {{ $semester->enrollment_end->format('M d, Y') }}
                                                        @if($semester->isEnrollmentOpen())
                                                            <br><span class="badge bg-success">Open</span>
                                                        @else
                                                            <br><span class="badge bg-secondary">Closed</span>
                                                        @endif
                                                    </small>
                                                @else
                                                    <span class="text-muted">Not set</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($semester->status === 'active')
                                                    <span class="badge bg-success">Active</span>
                                                @else
                                                    <span class="badge bg-secondary">Inactive</span>
                                                @endif
                                            </td>
                                            <td>
                                                <small class="text-muted">{{ $semester->created_at->format('M d, Y') }}</small>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="{{ route('admin.semesters.show', $semester) }}" 
                                                       class="btn btn-outline-info" title="View Details">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                    @can('semesters.manage')
                                                        <a href="{{ route('admin.semesters.edit', $semester) }}" 
                                                           class="btn btn-outline-warning" title="Edit">
                                                            <i class="bi bi-pencil"></i>
                                                        </a>
                                                        @if(!$semester->is_current)
                                                            <form action="{{ route('admin.semesters.set-current', $semester) }}" method="POST" class="d-inline">
                                                                @csrf
                                                                @method('PATCH')
                                                                <button type="submit" class="btn btn-outline-primary" title="Set as Current"
                                                                        onclick="return confirm('Set this as the current semester?')">
                                                                    <i class="bi bi-star"></i>
                                                                </button>
                                                            </form>
                                                        @endif
                                                        <form action="{{ route('admin.semesters.toggle-status', $semester) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            @method('PATCH')
                                                            <button type="submit" 
                                                                    class="btn btn-outline-{{ $semester->status === 'active' ? 'secondary' : 'success' }}" 
                                                                    title="{{ $semester->status === 'active' ? 'Deactivate' : 'Activate' }}"
                                                                    onclick="return confirm('{{ $semester->status === 'active' ? 'Deactivate' : 'Activate' }} this semester?')">
                                                                <i class="bi bi-{{ $semester->status === 'active' ? 'pause' : 'play' }}"></i>
                                                            </button>
                                                        </form>
                                                        @if($semester->isEnrollmentOpen())
                                                            <form action="{{ route('admin.semesters.close-enrollment', $semester) }}" method="POST" class="d-inline">
                                                                @csrf
                                                                @method('PATCH')
                                                                <button type="submit" class="btn btn-outline-warning" title="Close Enrollment"
                                                                        onclick="return confirm('Close enrollment for this semester?')">
                                                                    <i class="bi bi-door-closed"></i>
                                                                </button>
                                                            </form>
                                                        @else
                                                            <form action="{{ route('admin.semesters.open-enrollment', $semester) }}" method="POST" class="d-inline">
                                                                @csrf
                                                                @method('PATCH')
                                                                <button type="submit" class="btn btn-outline-success" title="Open Enrollment"
                                                                        onclick="return confirm('Open enrollment for this semester?')">
                                                                    <i class="bi bi-door-open"></i>
                                                                </button>
                                                            </form>
                                                        @endif
                                                        <form action="{{ route('admin.semesters.destroy', $semester) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-outline-danger" title="Delete"
                                                                    onclick="return confirm('Are you sure you want to delete this semester? This action cannot be undone.')">
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
                                    Showing {{ $semesters->firstItem() }} to {{ $semesters->lastItem() }} of {{ $semesters->total() }} results
                                </small>
                            </div>
                            <div>
                                {{ $semesters->appends(request()->query())->links() }}
                            </div>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-calendar-x fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No Semesters Found</h5>
                            <p class="text-muted">No semesters match your current search criteria.</p>
                            @can('semesters.manage')
                                <a href="{{ route('admin.semesters.create') }}" class="btn btn-primary">
                                    <i class="bi bi-plus"></i> Add First Semester
                                </a>
                            @endcan
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection