@extends('layouts.admin')

@section('page-title', 'Academic Year Details')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Academic Year Details: {{ $academicYear->name }}</h5>
                    <div>
                        @can('academic-years.manage')
                            <a href="{{ route('admin.academic-years.edit', $academicYear) }}" class="btn btn-warning btn-sm">
                                <i class="bi bi-pencil"></i> Edit
                            </a>
                            @if(!$academicYear->is_current)
                                <form action="{{ route('admin.academic-years.set-current', $academicYear) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-success btn-sm" 
                                            onclick="return confirm('Are you sure you want to set this as the current academic year?')">
                                        <i class="bi bi-check-circle"></i> Set as Current
                                    </button>
                                </form>
                            @endif
                        @endcan
                        <a href="{{ route('admin.academic-years.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Back to Academic Years
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if($academicYear->is_current)
                        <div class="alert alert-success mb-3">
                            <i class="bi bi-check-circle"></i>
                            <strong>Current Academic Year:</strong> This is the currently active academic year.
                        </div>
                    @endif
                    
                    <div class="row">
                        <!-- Basic Information -->
                        <div class="col-md-6">
                            <div class="card h-100">
                                <div class="card-header">
                                    <h6 class="mb-0"><i class="bi bi-calendar"></i> Basic Information</h6>
                                </div>
                                <div class="card-body">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td class="fw-bold">Name:</td>
                                            <td>{{ $academicYear->name }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">Start Date:</td>
                                            <td>{{ $academicYear->start_date?->format('M d, Y') ?? 'Not set' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">End Date:</td>
                                            <td>{{ $academicYear->end_date?->format('M d, Y') ?? 'Not set' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">Duration:</td>
                                            <td>
                                                @if($academicYear->start_date && $academicYear->end_date)
                                                    {{ $academicYear->start_date->diffInDays($academicYear->end_date) }} days
                                                @else
                                                    Not calculated
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">Status:</td>
                                            <td>
                                                @if($academicYear->status === 'active')
                                                    <span class="badge bg-success">Active</span>
                                                @else
                                                    <span class="badge bg-secondary">Inactive</span>
                                                @endif
                                                @if($academicYear->is_current)
                                                    <span class="badge bg-primary ms-1">Current</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">Created:</td>
                                            <td>{{ $academicYear->created_at->format('M d, Y g:i A') }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">Last Updated:</td>
                                            <td>{{ $academicYear->updated_at->format('M d, Y g:i A') }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Statistics -->
                        <div class="col-md-6">
                            <div class="card h-100">
                                <div class="card-header">
                                    <h6 class="mb-0"><i class="bi bi-bar-chart"></i> Statistics</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row text-center">
                                        <div class="col-6 mb-3">
                                            <div class="border rounded p-3">
                                                <h4 class="text-primary mb-1">{{ $academicYear->semesters_count ?? $academicYear->semesters->count() }}</h4>
                                                <small class="text-muted">Total Semesters</small>
                                            </div>
                                        </div>
                                        <div class="col-6 mb-3">
                                            <div class="border rounded p-3">
                                                <h4 class="text-success mb-1">{{ $academicYear->active_semesters_count ?? $academicYear->semesters->where('status', 'active')->count() }}</h4>
                                                <small class="text-muted">Active Semesters</small>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="border rounded p-3">
                                                <h4 class="text-info mb-1">{{ $academicYear->subjects_count ?? 0 }}</h4>
                                                <small class="text-muted">Total Subjects</small>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="border rounded p-3">
                                                <h4 class="text-warning mb-1">{{ $academicYear->grade_encoding_periods_count ?? 0 }}</h4>
                                                <small class="text-muted">Encoding Periods</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    @if($academicYear->description)
                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="mb-0"><i class="bi bi-file-text"></i> Description</h6>
                                    </div>
                                    <div class="card-body">
                                        <p class="mb-0">{{ $academicYear->description }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                    
                    <!-- Semesters -->
                    @if($academicYear->semesters->count() > 0)
                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <h6 class="mb-0"><i class="bi bi-calendar-range"></i> Semesters</h6>
                                        @can('semesters.manage')
                                            <a href="{{ route('admin.semesters.create', ['academic_year' => $academicYear->id]) }}" class="btn btn-primary btn-sm">
                                                <i class="bi bi-plus"></i> Add Semester
                                            </a>
                                        @endcan
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>Name</th>
                                                        <th>Term</th>
                                                        <th>Duration</th>
                                                        <th>Enrollment Period</th>
                                                        <th>Status</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($academicYear->semesters as $semester)
                                                        <tr>
                                                            <td>{{ $semester->name }}</td>
                                                            <td>
                                                                <span class="badge bg-info">{{ $semester->term_display }}</span>
                                                            </td>
                                                            <td>
                                                                @if($semester->start_date && $semester->end_date)
                                                                    {{ $semester->start_date->format('M d') }} - {{ $semester->end_date->format('M d, Y') }}
                                                                @else
                                                                    <span class="text-muted">Not set</span>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                @if($semester->enrollment_start && $semester->enrollment_end)
                                                                    {{ $semester->enrollment_start->format('M d') }} - {{ $semester->enrollment_end->format('M d, Y') }}
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
                                                                @if($semester->is_current)
                                                                    <span class="badge bg-primary ms-1">Current</span>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                <div class="btn-group btn-group-sm">
                                                                    <a href="{{ route('admin.semesters.show', $semester) }}" class="btn btn-outline-info" title="View">
                                                                        <i class="bi bi-eye"></i>
                                                                    </a>
                                                                    @can('semesters.manage')
                                                                        <a href="{{ route('admin.semesters.edit', $semester) }}" class="btn btn-outline-warning" title="Edit">
                                                                            <i class="bi bi-pencil"></i>
                                                                        </a>
                                                                    @endcan
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                    
                    <!-- Grade Encoding Periods -->
                    @if($academicYear->gradeEncodingPeriods && $academicYear->gradeEncodingPeriods->count() > 0)
                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <h6 class="mb-0"><i class="bi bi-calendar-check"></i> Grade Encoding Periods</h6>
                                        @can('grade-encoding-periods.manage')
                                            <a href="{{ route('admin.grade-encoding-periods.create', ['academic_year' => $academicYear->id]) }}" class="btn btn-primary btn-sm">
                                                <i class="bi bi-plus"></i> Add Period
                                            </a>
                                        @endcan
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>Name</th>
                                                        <th>Semester</th>
                                                        <th>Period</th>
                                                        <th>Grade Type</th>
                                                        <th>Status</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($academicYear->gradeEncodingPeriods as $period)
                                                        <tr>
                                                            <td>{{ $period->name }}</td>
                                                            <td>{{ $period->semester->name ?? 'N/A' }}</td>
                                                            <td>
                                                                @if($period->start_date && $period->end_date)
                                                                    {{ $period->start_date->format('M d') }} - {{ $period->end_date->format('M d, Y') }}
                                                                @else
                                                                    <span class="text-muted">Not set</span>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                <span class="badge bg-secondary">{{ $period->grade_type_display }}</span>
                                                            </td>
                                                            <td>
                                                                @switch($period->status)
                                                                    @case('scheduled')
                                                                        <span class="badge bg-info">Scheduled</span>
                                                                        @break
                                                                    @case('active')
                                                                        <span class="badge bg-success">Active</span>
                                                                        @break
                                                                    @case('closed')
                                                                        <span class="badge bg-secondary">Closed</span>
                                                                        @break
                                                                    @case('extended')
                                                                        <span class="badge bg-warning">Extended</span>
                                                                        @break
                                                                @endswitch
                                                            </td>
                                                            <td>
                                                                <div class="btn-group btn-group-sm">
                                                                    <a href="{{ route('admin.grade-encoding-periods.show', $period) }}" class="btn btn-outline-info" title="View">
                                                                        <i class="bi bi-eye"></i>
                                                                    </a>
                                                                    @can('grade-encoding-periods.manage')
                                                                        <a href="{{ route('admin.grade-encoding-periods.edit', $period) }}" class="btn btn-outline-warning" title="Edit">
                                                                            <i class="bi bi-pencil"></i>
                                                                        </a>
                                                                    @endcan
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection