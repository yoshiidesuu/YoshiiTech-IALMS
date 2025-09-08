@extends('layouts.admin')

@section('page-title', 'Academic Year Management')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Academic Year Management</h2>
    @can('academic-years.manage')
        <a href="{{ route('admin.academic-years.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i> Add New Academic Year
        </a>
    @endcan
</div>

<!-- Search and Filter Form -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.academic-years.index') }}" class="row g-3">
            <div class="col-md-4">
                <label for="search" class="form-label">Search</label>
                <input type="text" class="form-control" id="search" name="search" 
                       value="{{ request('search') }}" placeholder="Search by name or year...">
            </div>
            <div class="col-md-3">
                <label for="status" class="form-label">Status</label>
                <select class="form-select" id="status" name="status">
                    <option value="">All Status</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    <option value="archived" {{ request('status') == 'archived' ? 'selected' : '' }}>Archived</option>
                </select>
            </div>
            <div class="col-md-3">
                <label for="year" class="form-label">Year Range</label>
                <select class="form-select" id="year" name="year">
                    <option value="">All Years</option>
                    @for($year = date('Y') + 2; $year >= date('Y') - 5; $year--)
                        <option value="{{ $year }}" {{ request('year') == $year ? 'selected' : '' }}>
                            {{ $year }}
                        </option>
                    @endfor
                </select>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-outline-primary me-2">
                    <i class="bi bi-search"></i> Search
                </button>
                <a href="{{ route('admin.academic-years.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-clockwise"></i>
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Academic Years Table -->
<div class="card">
    <div class="card-body">
        @if($academicYears->count() > 0)
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>Name</th>
                            <th>Duration</th>
                            <th>Status</th>
                            <th>Current</th>
                            <th>Semesters</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($academicYears as $academicYear)
                            <tr class="{{ $academicYear->is_current ? 'table-warning' : '' }}">
                                <td>
                                    <div class="fw-medium">{{ $academicYear->name }}</div>
                                    @if($academicYear->description)
                                        <small class="text-muted">{{ Str::limit($academicYear->description, 50) }}</small>
                                    @endif
                                </td>
                                <td>
                                    <div>
                                        <small class="text-muted">Start:</small> {{ $academicYear->start_date->format('M d, Y') }}
                                    </div>
                                    <div>
                                        <small class="text-muted">End:</small> {{ $academicYear->end_date->format('M d, Y') }}
                                    </div>
                                    <small class="badge bg-info">{{ $academicYear->duration_in_days }} days</small>
                                </td>
                                <td>
                                    @if($academicYear->status === 'active')
                                        <span class="badge bg-success">Active</span>
                                    @elseif($academicYear->status === 'inactive')
                                        <span class="badge bg-secondary">Inactive</span>
                                    @elseif($academicYear->status === 'archived')
                                        <span class="badge bg-dark">Archived</span>
                                    @else
                                        <span class="badge bg-warning">{{ ucfirst($academicYear->status) }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if($academicYear->is_current)
                                        <span class="badge bg-primary">
                                            <i class="bi bi-star-fill me-1"></i>Current
                                        </span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($academicYear->semesters_count > 0)
                                        <span class="badge bg-info">{{ $academicYear->semesters_count }} semesters</span>
                                    @else
                                        <span class="text-muted">No semesters</span>
                                    @endif
                                </td>
                                <td>
                                    <small>{{ $academicYear->created_at->format('M d, Y') }}</small>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.academic-years.show', $academicYear) }}" 
                                           class="btn btn-sm btn-outline-info" title="View">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        @can('academic-years.manage')
                                            <a href="{{ route('admin.academic-years.edit', $academicYear) }}" 
                                               class="btn btn-sm btn-outline-primary" title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            @if(!$academicYear->is_current)
                                                <form method="POST" action="{{ route('admin.academic-years.set-current', $academicYear) }}" 
                                                      class="d-inline" onsubmit="return confirm('Set this as the current academic year?')">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="btn btn-sm btn-outline-warning" title="Set as Current">
                                                        <i class="bi bi-star"></i>
                                                    </button>
                                                </form>
                                            @endif
                                            @if($academicYear->status !== 'archived')
                                                <form method="POST" action="{{ route('admin.academic-years.toggle-status', $academicYear) }}" 
                                                      class="d-inline" onsubmit="return confirm('Are you sure?')">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="btn btn-sm {{ $academicYear->status === 'active' ? 'btn-outline-secondary' : 'btn-outline-success' }}" 
                                                            title="{{ $academicYear->status === 'active' ? 'Deactivate' : 'Activate' }}">
                                                        <i class="bi bi-{{ $academicYear->status === 'active' ? 'pause' : 'play' }}"></i>
                                                    </button>
                                                </form>
                                                @if(!$academicYear->is_current && $academicYear->semesters_count == 0)
                                                    <form method="POST" action="{{ route('admin.academic-years.archive', $academicYear) }}" 
                                                          class="d-inline" onsubmit="return confirm('Archive this academic year? This action can be undone.')">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="btn btn-sm btn-outline-dark" title="Archive">
                                                            <i class="bi bi-archive"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            @else
                                                <form method="POST" action="{{ route('admin.academic-years.restore', $academicYear) }}" 
                                                      class="d-inline" onsubmit="return confirm('Restore this academic year?')">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="btn btn-sm btn-outline-success" title="Restore">
                                                        <i class="bi bi-arrow-clockwise"></i>
                                                    </button>
                                                </form>
                                            @endif
                                            @if(!$academicYear->is_current && $academicYear->semesters_count == 0 && $academicYear->status !== 'archived')
                                                <form method="POST" action="{{ route('admin.academic-years.destroy', $academicYear) }}" 
                                                      class="d-inline" onsubmit="return confirm('Are you sure you want to delete this academic year? This action cannot be undone.')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            @endif
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
                        Showing {{ $academicYears->firstItem() }} to {{ $academicYears->lastItem() }} of {{ $academicYears->total() }} results
                    </small>
                </div>
                <div>
                    {{ $academicYears->links() }}
                </div>
            </div>
        @else
            <div class="text-center py-5">
                <i class="bi bi-calendar-range display-1 text-muted"></i>
                <h4 class="mt-3">No Academic Years Found</h4>
                <p class="text-muted">No academic years match your current search criteria.</p>
                @can('academic-years.manage')
                    <a href="{{ route('admin.academic-years.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-1"></i> Add First Academic Year
                    </a>
                @endcan
            </div>
        @endif
    </div>
</div>

<!-- Statistics Cards -->
@if($academicYears->count() > 0)
<div class="row mt-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title">Total Years</h6>
                        <h3 class="mb-0">{{ $academicYears->total() }}</h3>
                    </div>
                    <div class="align-self-center">
                        <i class="bi bi-calendar-range display-6"></i>
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
                        <h6 class="card-title">Active Years</h6>
                        <h3 class="mb-0">{{ $academicYears->where('status', 'active')->count() }}</h3>
                    </div>
                    <div class="align-self-center">
                        <i class="bi bi-check-circle display-6"></i>
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
                        <h6 class="card-title">Current Year</h6>
                        <h3 class="mb-0">{{ $academicYears->where('is_current', true)->count() ? '1' : '0' }}</h3>
                    </div>
                    <div class="align-self-center">
                        <i class="bi bi-star-fill display-6"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-dark text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title">Archived</h6>
                        <h3 class="mb-0">{{ $academicYears->where('status', 'archived')->count() }}</h3>
                    </div>
                    <div class="align-self-center">
                        <i class="bi bi-archive display-6"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif
@endsection