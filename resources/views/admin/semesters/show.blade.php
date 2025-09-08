@extends('layouts.admin')

@section('page-title', 'Semester Details')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ $semester->name }}</h5>
                    <div>
                        @if($semester->is_current)
                            <span class="badge bg-primary me-2">Current Semester</span>
                        @endif
                        @if($semester->status === 'active')
                            <span class="badge bg-success">Active</span>
                        @else
                            <span class="badge bg-secondary">Inactive</span>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-muted mb-3">Basic Information</h6>
                            <table class="table table-sm">
                                <tr>
                                    <td><strong>Academic Year:</strong></td>
                                    <td>
                                        @if($semester->academicYear)
                                            <a href="{{ route('admin.academic-years.show', $semester->academicYear) }}" class="text-decoration-none">
                                                {{ $semester->academicYear->name }}
                                            </a>
                                        @else
                                            <span class="text-muted">Not assigned</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Term:</strong></td>
                                    <td>
                                        <span class="badge bg-info">{{ $semester->term_display }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Duration:</strong></td>
                                    <td>
                                        @if($semester->start_date && $semester->end_date)
                                            {{ $semester->start_date->format('M d, Y') }} - {{ $semester->end_date->format('M d, Y') }}
                                            <br><small class="text-muted">({{ $semester->start_date->diffInDays($semester->end_date) }} days)</small>
                                        @else
                                            <span class="text-muted">Not set</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Enrollment Period:</strong></td>
                                    <td>
                                        @if($semester->enrollment_start && $semester->enrollment_end)
                                            {{ $semester->enrollment_start->format('M d, Y') }} - {{ $semester->enrollment_end->format('M d, Y') }}
                                            <br>
                                            @if($semester->isEnrollmentOpen())
                                                <span class="badge bg-success">Open</span>
                                            @else
                                                <span class="badge bg-secondary">Closed</span>
                                            @endif
                                        @else
                                            <span class="text-muted">Not set</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Created:</strong></td>
                                    <td>{{ $semester->created_at->format('M d, Y g:i A') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Last Updated:</strong></td>
                                    <td>{{ $semester->updated_at->format('M d, Y g:i A') }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted mb-3">Statistics</h6>
                            <div class="row g-3">
                                <div class="col-6">
                                    <div class="card bg-light">
                                        <div class="card-body text-center">
                                            <h4 class="text-primary mb-1">{{ $semester->subjects()->count() }}</h4>
                                            <small class="text-muted">Subjects</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="card bg-light">
                                        <div class="card-body text-center">
                                            <h4 class="text-success mb-1">{{ $semester->gradeEncodingPeriods()->count() }}</h4>
                                            <small class="text-muted">Grade Periods</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="card bg-light">
                                        <div class="card-body text-center">
                                            <h4 class="text-info mb-1">{{ $semester->subjects()->where('status', 'active')->count() }}</h4>
                                            <small class="text-muted">Active Subjects</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="card bg-light">
                                        <div class="card-body text-center">
                                            <h4 class="text-warning mb-1">{{ $semester->gradeEncodingPeriods()->where('status', 'active')->count() }}</h4>
                                            <small class="text-muted">Active Periods</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($semester->description)
                        <div class="mt-4">
                            <h6 class="text-muted mb-2">Description</h6>
                            <div class="bg-light p-3 rounded">
                                {{ $semester->description }}
                            </div>
                        </div>
                    @endif

                    <div class="d-flex justify-content-between mt-4">
                        <div>
                            <a href="{{ route('admin.semesters.index') }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Back to Semesters
                            </a>
                        </div>
                        <div>
                            @can('semesters.manage')
                                @if(!$semester->is_current)
                                    <form action="{{ route('admin.semesters.set-current', $semester) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-primary" 
                                                onclick="return confirm('Set this as the current semester?')">
                                            <i class="bi bi-star"></i> Set as Current
                                        </button>
                                    </form>
                                @endif
                                <a href="{{ route('admin.semesters.edit', $semester) }}" class="btn btn-warning">
                                    <i class="bi bi-pencil"></i> Edit
                                </a>
                            @endcan
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <!-- Quick Actions -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">Quick Actions</h6>
                </div>
                <div class="card-body">
                    @can('semesters.manage')
                        <div class="d-grid gap-2">
                            @if($semester->isEnrollmentOpen())
                                <form action="{{ route('admin.semesters.close-enrollment', $semester) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-outline-warning" 
                                            onclick="return confirm('Close enrollment for this semester?')">
                                        <i class="bi bi-door-closed"></i> Close Enrollment
                                    </button>
                                </form>
                            @else
                                <form action="{{ route('admin.semesters.open-enrollment', $semester) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-outline-success" 
                                            onclick="return confirm('Open enrollment for this semester?')">
                                        <i class="bi bi-door-open"></i> Open Enrollment
                                    </button>
                                </form>
                            @endif
                            
                            <form action="{{ route('admin.semesters.toggle-status', $semester) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <button type="submit" 
                                        class="btn btn-outline-{{ $semester->status === 'active' ? 'secondary' : 'success' }}" 
                                        onclick="return confirm('{{ $semester->status === 'active' ? 'Deactivate' : 'Activate' }} this semester?')">
                                    <i class="bi bi-{{ $semester->status === 'active' ? 'pause' : 'play' }}"></i> 
                                    {{ $semester->status === 'active' ? 'Deactivate' : 'Activate' }}
                                </button>
                            </form>
                        </div>
                    @endcan
                </div>
            </div>

            <!-- Timeline -->
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">Timeline</h6>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        @if($semester->enrollment_start)
                            <div class="timeline-item {{ now() >= $semester->enrollment_start ? 'completed' : 'upcoming' }}">
                                <div class="timeline-marker"></div>
                                <div class="timeline-content">
                                    <h6 class="mb-1">Enrollment Opens</h6>
                                    <small class="text-muted">{{ $semester->enrollment_start->format('M d, Y') }}</small>
                                </div>
                            </div>
                        @endif
                        
                        @if($semester->start_date)
                            <div class="timeline-item {{ now() >= $semester->start_date ? 'completed' : 'upcoming' }}">
                                <div class="timeline-marker"></div>
                                <div class="timeline-content">
                                    <h6 class="mb-1">Semester Starts</h6>
                                    <small class="text-muted">{{ $semester->start_date->format('M d, Y') }}</small>
                                </div>
                            </div>
                        @endif
                        
                        @if($semester->enrollment_end)
                            <div class="timeline-item {{ now() >= $semester->enrollment_end ? 'completed' : 'upcoming' }}">
                                <div class="timeline-marker"></div>
                                <div class="timeline-content">
                                    <h6 class="mb-1">Enrollment Closes</h6>
                                    <small class="text-muted">{{ $semester->enrollment_end->format('M d, Y') }}</small>
                                </div>
                            </div>
                        @endif
                        
                        @if($semester->end_date)
                            <div class="timeline-item {{ now() >= $semester->end_date ? 'completed' : 'upcoming' }}">
                                <div class="timeline-marker"></div>
                                <div class="timeline-content">
                                    <h6 class="mb-1">Semester Ends</h6>
                                    <small class="text-muted">{{ $semester->end_date->format('M d, Y') }}</small>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Related Data -->
    <div class="row mt-4">
        @if($semester->subjects()->count() > 0)
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">Subjects ({{ $semester->subjects()->count() }})</h6>
                        <a href="{{ route('admin.subjects.index', ['semester' => $semester->id]) }}" class="btn btn-sm btn-outline-primary">
                            View All
                        </a>
                    </div>
                    <div class="card-body">
                        @if($semester->subjects()->limit(5)->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Code</th>
                                            <th>Name</th>
                                            <th>Credits</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($semester->subjects()->limit(5)->get() as $subject)
                                            <tr>
                                                <td><code>{{ $subject->code }}</code></td>
                                                <td>
                                                    <a href="{{ route('admin.subjects.show', $subject) }}" class="text-decoration-none">
                                                        {{ Str::limit($subject->name, 30) }}
                                                    </a>
                                                </td>
                                                <td>{{ $subject->credits }}</td>
                                                <td>
                                                    <span class="badge bg-{{ $subject->status === 'active' ? 'success' : 'secondary' }}">
                                                        {{ ucfirst($subject->status) }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-muted mb-0">No subjects assigned to this semester yet.</p>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        @if($semester->gradeEncodingPeriods()->count() > 0)
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">Grade Encoding Periods ({{ $semester->gradeEncodingPeriods()->count() }})</h6>
                        <a href="{{ route('admin.grade-encoding-periods.index', ['semester' => $semester->id]) }}" class="btn btn-sm btn-outline-primary">
                            View All
                        </a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Period</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($semester->gradeEncodingPeriods()->limit(5)->get() as $period)
                                        <tr>
                                            <td>
                                                <a href="{{ route('admin.grade-encoding-periods.show', $period) }}" class="text-decoration-none">
                                                    {{ Str::limit($period->name, 25) }}
                                                </a>
                                            </td>
                                            <td>
                                                <small>
                                                    {{ $period->start_date->format('M d') }} - {{ $period->end_date->format('M d') }}
                                                </small>
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $period->status === 'active' ? 'success' : ($period->status === 'scheduled' ? 'info' : 'secondary') }}">
                                                    {{ ucfirst($period->status) }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

@push('styles')
<style>
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline-item {
    position: relative;
    margin-bottom: 20px;
}

.timeline-item:not(:last-child)::before {
    content: '';
    position: absolute;
    left: -22px;
    top: 20px;
    width: 2px;
    height: calc(100% + 10px);
    background-color: #dee2e6;
}

.timeline-marker {
    position: absolute;
    left: -26px;
    top: 4px;
    width: 10px;
    height: 10px;
    border-radius: 50%;
    background-color: #6c757d;
    border: 2px solid #fff;
    box-shadow: 0 0 0 2px #dee2e6;
}

.timeline-item.completed .timeline-marker {
    background-color: #198754;
    box-shadow: 0 0 0 2px #198754;
}

.timeline-item.upcoming .timeline-marker {
    background-color: #0d6efd;
    box-shadow: 0 0 0 2px #0d6efd;
}

.timeline-content h6 {
    font-size: 0.9rem;
    margin-bottom: 2px;
}
</style>
@endpush
@endsection