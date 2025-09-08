@extends('layouts.admin')

@section('page-title', 'Grade Encoding Periods')

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
                                Total Periods
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $statistics['total'] ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-alt fa-2x text-gray-300"></i>
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
                                Active Periods
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $statistics['active'] ?? 0 }}</div>
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
                                Current Period
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $statistics['current'] ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
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
                                Upcoming Deadlines
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $statistics['upcoming_deadlines'] ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Main Content -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Grade Encoding Periods Management</h6>
                    @can('grade_encoding_periods.create')
                        <a href="{{ route('admin.grade-encoding-periods.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Create New Period
                        </a>
                    @endcan
                </div>
                
                <!-- Search and Filter Form -->
                <div class="card-body border-bottom">
                    <form method="GET" action="{{ route('admin.grade-encoding-periods.index') }}" id="filterForm">
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label for="search" class="form-label">Search</label>
                                <input type="text" class="form-control" id="search" name="search" 
                                       value="{{ request('search') }}" placeholder="Search periods...">
                            </div>
                            <div class="col-md-2 mb-3">
                                <label for="academic_year" class="form-label">Academic Year</label>
                                <select class="form-select" id="academic_year" name="academic_year">
                                    <option value="">All Years</option>
                                    @foreach($academicYears ?? [] as $year)
                                        <option value="{{ $year->id }}" {{ request('academic_year') == $year->id ? 'selected' : '' }}>
                                            {{ $year->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2 mb-3">
                                <label for="semester" class="form-label">Semester</label>
                                <select class="form-select" id="semester" name="semester">
                                    <option value="">All Semesters</option>
                                    @foreach($semesters ?? [] as $semester)
                                        <option value="{{ $semester->id }}" {{ request('semester') == $semester->id ? 'selected' : '' }}>
                                            {{ $semester->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2 mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="">All Status</option>
                                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                    <option value="current" {{ request('status') === 'current' ? 'selected' : '' }}>Current</option>
                                    <option value="upcoming" {{ request('status') === 'upcoming' ? 'selected' : '' }}>Upcoming</option>
                                    <option value="expired" {{ request('status') === 'expired' ? 'selected' : '' }}>Expired</option>
                                </select>
                            </div>
                            <div class="col-md-2 mb-3">
                                <label for="period_type" class="form-label">Period Type</label>
                                <select class="form-select" id="period_type" name="period_type">
                                    <option value="">All Types</option>
                                    <option value="midterm" {{ request('period_type') === 'midterm' ? 'selected' : '' }}>Midterm</option>
                                    <option value="final" {{ request('period_type') === 'final' ? 'selected' : '' }}>Final</option>
                                    <option value="makeup" {{ request('period_type') === 'makeup' ? 'selected' : '' }}>Makeup</option>
                                    <option value="special" {{ request('period_type') === 'special' ? 'selected' : '' }}>Special</option>
                                </select>
                            </div>
                            <div class="col-md-1 mb-3 d-flex align-items-end">
                                <button type="submit" class="btn btn-outline-primary w-100">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                        
                        @if(request()->hasAny(['search', 'academic_year', 'semester', 'status', 'period_type']))
                            <div class="row">
                                <div class="col-12">
                                    <a href="{{ route('admin.grade-encoding-periods.index') }}" class="btn btn-link btn-sm p-0">
                                        <i class="fas fa-times"></i> Clear Filters
                                    </a>
                                </div>
                            </div>
                        @endif
                    </form>
                </div>
                
                <!-- Periods Table -->
                <div class="card-body">
                    @if($gradeEncodingPeriods->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover" id="periodsTable">
                                <thead class="table-light">
                                    <tr>
                                        <th>Period Name</th>
                                        <th>Academic Year</th>
                                        <th>Semester</th>
                                        <th>Type</th>
                                        <th>Duration</th>
                                        <th>Deadline</th>
                                        <th>Status</th>
                                        <th>Progress</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($gradeEncodingPeriods as $period)
                                        <tr class="{{ $period->is_current ? 'table-warning' : '' }}">
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    @if($period->is_current)
                                                        <i class="fas fa-star text-warning me-2" title="Current Period"></i>
                                                    @endif
                                                    <div>
                                                        <strong>{{ $period->name }}</strong>
                                                        @if($period->description)
                                                            <br><small class="text-muted">{{ Str::limit($period->description, 50) }}</small>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge badge-primary">{{ $period->academicYear->name ?? 'N/A' }}</span>
                                            </td>
                                            <td>
                                                <span class="badge badge-info">{{ $period->semester->name ?? 'N/A' }}</span>
                                            </td>
                                            <td>
                                                <span class="badge badge-{{ $period->period_type === 'final' ? 'danger' : ($period->period_type === 'midterm' ? 'warning' : ($period->period_type === 'makeup' ? 'info' : 'secondary')) }}">
                                                    {{ ucfirst($period->period_type ?? 'Regular') }}
                                                </span>
                                            </td>
                                            <td>
                                                <small>
                                                    <strong>Start:</strong> {{ $period->start_date->format('M d, Y') }}<br>
                                                    <strong>End:</strong> {{ $period->end_date->format('M d, Y') }}
                                                </small>
                                            </td>
                                            <td>
                                                @if($period->deadline_date)
                                                    <div class="text-center">
                                                        <div class="{{ $period->deadline_date->isPast() ? 'text-danger' : ($period->deadline_date->diffInDays() <= 3 ? 'text-warning' : 'text-success') }}">
                                                            <strong>{{ $period->deadline_date->format('M d, Y') }}</strong>
                                                        </div>
                                                        <small class="text-muted">
                                                            @if($period->deadline_date->isPast())
                                                                Expired {{ $period->deadline_date->diffForHumans() }}
                                                            @else
                                                                {{ $period->deadline_date->diffForHumans() }}
                                                            @endif
                                                        </small>
                                                    </div>
                                                @else
                                                    <span class="text-muted">No deadline</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge badge-{{ $period->status === 'active' ? 'success' : ($period->status === 'inactive' ? 'secondary' : ($period->status === 'current' ? 'primary' : 'warning')) }}">
                                                    {{ ucfirst($period->status) }}
                                                </span>
                                                @if($period->is_extended)
                                                    <br><small class="badge badge-info mt-1">Extended</small>
                                                @endif
                                            </td>
                                            <td>
                                                @php
                                                    $totalSubjects = $period->total_subjects ?? 0;
                                                    $encodedSubjects = $period->encoded_subjects ?? 0;
                                                    $percentage = $totalSubjects > 0 ? ($encodedSubjects / $totalSubjects) * 100 : 0;
                                                @endphp
                                                <div class="progress" style="height: 20px;">
                                                    <div class="progress-bar bg-{{ $percentage >= 100 ? 'success' : ($percentage >= 75 ? 'info' : ($percentage >= 50 ? 'warning' : 'danger')) }}" 
                                                         role="progressbar" style="width: {{ $percentage }}%" 
                                                         aria-valuenow="{{ $percentage }}" aria-valuemin="0" aria-valuemax="100">
                                                        {{ number_format($percentage, 1) }}%
                                                    </div>
                                                </div>
                                                <small class="text-muted">{{ $encodedSubjects }}/{{ $totalSubjects }} subjects</small>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('admin.grade-encoding-periods.show', $period) }}" 
                                                       class="btn btn-info btn-sm" title="View Details">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    
                                                    @can('grade_encoding_periods.edit')
                                                        <a href="{{ route('admin.grade-encoding-periods.edit', $period) }}" 
                                                           class="btn btn-warning btn-sm" title="Edit Period">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                    @endcan
                                                    
                                                    @can('grade_encoding_periods.manage')
                                                        @if(!$period->is_current)
                                                            <form action="{{ route('admin.grade-encoding-periods.set-current', $period) }}" method="POST" class="d-inline">
                                                                @csrf
                                                                @method('PATCH')
                                                                <button type="submit" class="btn btn-primary btn-sm" title="Set as Current"
                                                                        onclick="return confirm('Set this as the current encoding period?')">
                                                                    <i class="fas fa-star"></i>
                                                                </button>
                                                            </form>
                                                        @endif
                                                        
                                                        @if($period->deadline_date && !$period->deadline_date->isPast())
                                                            <button type="button" class="btn btn-secondary btn-sm" title="Extend Deadline"
                                                                    onclick="showExtendModal({{ $period->id }}, '{{ $period->name }}', '{{ $period->deadline_date->format('Y-m-d') }}')">
                                                                <i class="fas fa-clock"></i>
                                                            </button>
                                                        @endif
                                                        
                                                        <form action="{{ route('admin.grade-encoding-periods.toggle-status', $period) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            @method('PATCH')
                                                            <button type="submit" class="btn btn-outline-{{ $period->status === 'active' ? 'secondary' : 'success' }} btn-sm" 
                                                                    title="{{ $period->status === 'active' ? 'Deactivate' : 'Activate' }} Period"
                                                                    onclick="return confirm('{{ $period->status === 'active' ? 'Deactivate' : 'Activate' }} this period?')">
                                                                <i class="fas fa-{{ $period->status === 'active' ? 'pause' : 'play' }}"></i>
                                                            </button>
                                                        </form>
                                                    @endcan
                                                    
                                                    @can('grade_encoding_periods.delete')
                                                        @if($period->status !== 'current' && !$period->is_current)
                                                            <form action="{{ route('admin.grade-encoding-periods.destroy', $period) }}" method="POST" class="d-inline">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-danger btn-sm" title="Delete Period"
                                                                        onclick="return confirm('Are you sure you want to delete this period? This action cannot be undone.')">
                                                                    <i class="fas fa-trash"></i>
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
                        <div class="d-flex justify-content-center mt-4">
                            {{ $gradeEncodingPeriods->appends(request()->query())->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No grade encoding periods found</h5>
                            <p class="text-muted">No periods match your current filters.</p>
                            @can('grade_encoding_periods.create')
                                <a href="{{ route('admin.grade-encoding-periods.create') }}" class="btn btn-primary">
                                    <i class="fas fa-plus"></i> Create First Period
                                </a>
                            @endcan
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Extend Deadline Modal -->
<div class="modal fade" id="extendDeadlineModal" tabindex="-1" aria-labelledby="extendDeadlineModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="extendDeadlineModalLabel">Extend Deadline</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="extendDeadlineForm" method="POST">
                @csrf
                @method('PATCH')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="periodName" class="form-label">Period</label>
                        <input type="text" class="form-control" id="periodName" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="currentDeadline" class="form-label">Current Deadline</label>
                        <input type="date" class="form-control" id="currentDeadline" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="newDeadline" class="form-label">New Deadline <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="newDeadline" name="deadline_date" required>
                        <div class="form-text">The new deadline must be after the current deadline.</div>
                    </div>
                    <div class="mb-3">
                        <label for="extensionReason" class="form-label">Reason for Extension</label>
                        <textarea class="form-control" id="extensionReason" name="extension_reason" rows="3" 
                                  placeholder="Provide a reason for extending the deadline..."></textarea>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="notifyUsers" name="notify_users" checked>
                        <label class="form-check-label" for="notifyUsers">
                            Notify all users about the deadline extension
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-clock"></i> Extend Deadline
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('styles')
<style>
.table th {
    border-top: none;
    font-weight: 600;
    font-size: 0.9rem;
    color: #5a5c69;
}

.table td {
    vertical-align: middle;
    font-size: 0.9rem;
}

.progress {
    border-radius: 10px;
}

.progress-bar {
    font-size: 0.8rem;
    font-weight: 600;
}

.badge {
    font-size: 0.8em;
}

.btn-group .btn {
    border-radius: 0;
}

.btn-group .btn:first-child {
    border-top-left-radius: 0.25rem;
    border-bottom-left-radius: 0.25rem;
}

.btn-group .btn:last-child {
    border-top-right-radius: 0.25rem;
    border-bottom-right-radius: 0.25rem;
}

.table-warning {
    background-color: rgba(255, 193, 7, 0.1) !important;
}

.card-header {
    background-color: #f8f9fc;
    border-bottom: 1px solid #e3e6f0;
}

.border-left-primary {
    border-left: 0.25rem solid #4e73df !important;
}

.border-left-success {
    border-left: 0.25rem solid #1cc88a !important;
}

.border-left-warning {
    border-left: 0.25rem solid #f6c23e !important;
}

.border-left-info {
    border-left: 0.25rem solid #36b9cc !important;
}

.text-xs {
    font-size: 0.7rem;
}

.shadow {
    box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15) !important;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const extendModal = new bootstrap.Modal(document.getElementById('extendDeadlineModal'));
    const extendForm = document.getElementById('extendDeadlineForm');
    const newDeadlineInput = document.getElementById('newDeadline');
    
    // Auto-submit form on filter changes
    const filterSelects = document.querySelectorAll('#academic_year, #semester, #status, #period_type');
    filterSelects.forEach(select => {
        select.addEventListener('change', function() {
            document.getElementById('filterForm').submit();
        });
    });
    
    // Show extend deadline modal
    window.showExtendModal = function(periodId, periodName, currentDeadline) {
        document.getElementById('periodName').value = periodName;
        document.getElementById('currentDeadline').value = currentDeadline;
        
        // Set minimum date for new deadline (must be after current deadline)
        const currentDate = new Date(currentDeadline);
        currentDate.setDate(currentDate.getDate() + 1);
        newDeadlineInput.min = currentDate.toISOString().split('T')[0];
        
        // Update form action
        extendForm.action = `/admin/grade-encoding-periods/${periodId}/extend-deadline`;
        
        extendModal.show();
    };
    
    // Validate new deadline
    newDeadlineInput.addEventListener('change', function() {
        const currentDeadline = new Date(document.getElementById('currentDeadline').value);
        const newDeadline = new Date(this.value);
        
        if (newDeadline <= currentDeadline) {
            this.setCustomValidity('New deadline must be after the current deadline');
        } else {
            this.setCustomValidity('');
        }
    });
    
    // Initialize tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[title]'));
    const tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Highlight current period row
    const currentPeriodRows = document.querySelectorAll('.table-warning');
    currentPeriodRows.forEach(row => {
        row.style.animation = 'pulse 2s infinite';
    });
});

// Add pulse animation for current period
const style = document.createElement('style');
style.textContent = `
    @keyframes pulse {
        0% { box-shadow: 0 0 0 0 rgba(255, 193, 7, 0.4); }
        70% { box-shadow: 0 0 0 10px rgba(255, 193, 7, 0); }
        100% { box-shadow: 0 0 0 0 rgba(255, 193, 7, 0); }
    }
`;
document.head.appendChild(style);
</script>
@endpush
@endsection