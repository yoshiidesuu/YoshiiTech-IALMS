@extends('layouts.admin')

@section('page-title', 'Grade Encoding Period Details')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Header Card -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">
                        {{ $gradeEncodingPeriod->name }}
                        @if($gradeEncodingPeriod->is_current)
                            <span class="badge badge-warning ms-2">Current Period</span>
                        @endif
                        <span class="badge badge-{{ $gradeEncodingPeriod->status === 'active' ? 'success' : ($gradeEncodingPeriod->status === 'expired' ? 'danger' : 'secondary') }} ms-2">
                            {{ ucfirst($gradeEncodingPeriod->status) }}
                        </span>
                        @if($gradeEncodingPeriod->is_extended)
                            <span class="badge badge-info ms-2">Extended</span>
                        @endif
                    </h6>
                    <div>
                        <a href="{{ route('admin.grade-encoding-periods.edit', $gradeEncodingPeriod) }}" class="btn btn-primary btn-sm me-2">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <a href="{{ route('admin.grade-encoding-periods.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Back to List
                        </a>
                    </div>
                </div>
                
                <!-- Status Alerts -->
                @if($gradeEncodingPeriod->deadline_date && $gradeEncodingPeriod->deadline_date->isPast())
                    <div class="alert alert-warning mx-3 mt-3 mb-0">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>Deadline Passed:</strong> This period's deadline was {{ $gradeEncodingPeriod->deadline_date->diffForHumans() }}.
                        @if($gradeEncodingPeriod->allow_late_submission)
                            Late submissions are allowed with {{ $gradeEncodingPeriod->late_penalty ?? 0 }}% penalty.
                        @else
                            No late submissions are allowed.
                        @endif
                    </div>
                @elseif($gradeEncodingPeriod->deadline_date && $gradeEncodingPeriod->deadline_date->isToday())
                    <div class="alert alert-danger mx-3 mt-3 mb-0">
                        <i class="fas fa-clock"></i>
                        <strong>Deadline Today:</strong> The deadline for this period is today at {{ $gradeEncodingPeriod->deadline_date->format('H:i') }}.
                    </div>
                @elseif($gradeEncodingPeriod->deadline_date && $gradeEncodingPeriod->deadline_date->isTomorrow())
                    <div class="alert alert-warning mx-3 mt-3 mb-0">
                        <i class="fas fa-clock"></i>
                        <strong>Deadline Tomorrow:</strong> The deadline for this period is tomorrow at {{ $gradeEncodingPeriod->deadline_date->format('H:i') }}.
                    </div>
                @elseif($gradeEncodingPeriod->is_current)
                    <div class="alert alert-info mx-3 mt-3 mb-0">
                        <i class="fas fa-info-circle"></i>
                        <strong>Current Period:</strong> This is the active encoding period.
                        @if($gradeEncodingPeriod->deadline_date)
                            Deadline is {{ $gradeEncodingPeriod->deadline_date->diffForHumans() }}.
                        @endif
                    </div>
                @endif
                
                <!-- Quick Actions -->
                <div class="card-body border-bottom">
                    <div class="row">
                        <div class="col-12">
                            <h6 class="text-primary mb-3"><i class="fas fa-bolt"></i> Quick Actions</h6>
                            <div class="btn-group me-2" role="group">
                                @if(!$gradeEncodingPeriod->is_current && $gradeEncodingPeriod->status !== 'expired')
                                    <form action="{{ route('admin.grade-encoding-periods.set-current', $gradeEncodingPeriod) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-success btn-sm" 
                                                onclick="return confirm('Set this as the current encoding period? This will deactivate any other current period.')">
                                            <i class="fas fa-play"></i> Set as Current
                                        </button>
                                    </form>
                                @endif
                                
                                @if($gradeEncodingPeriod->deadline_date && !$gradeEncodingPeriod->deadline_date->isPast() && ($gradeEncodingPeriod->extensions_count ?? 0) < ($gradeEncodingPeriod->max_extensions ?? 2))
                                    <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#extendDeadlineModal">
                                        <i class="fas fa-clock"></i> Extend Deadline
                                    </button>
                                @endif
                                
                                <form action="{{ route('admin.grade-encoding-periods.toggle-status', $gradeEncodingPeriod) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-{{ $gradeEncodingPeriod->status === 'active' ? 'secondary' : 'success' }} btn-sm" 
                                            onclick="return confirm('{{ $gradeEncodingPeriod->status === 'active' ? 'Deactivate' : 'Activate' }} this encoding period?')">
                                        <i class="fas fa-{{ $gradeEncodingPeriod->status === 'active' ? 'pause' : 'play' }}"></i> 
                                        {{ $gradeEncodingPeriod->status === 'active' ? 'Deactivate' : 'Activate' }}
                                    </button>
                                </form>
                            </div>
                            
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-info btn-sm" onclick="window.print()">
                                    <i class="fas fa-print"></i> Print
                                </button>
                                <button type="button" class="btn btn-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#exportModal">
                                    <i class="fas fa-download"></i> Export
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <!-- Basic Information -->
                <div class="col-lg-8">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">
                                <i class="fas fa-info-circle"></i> Basic Information
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <strong class="text-muted">Period Name:</strong><br>
                                    <span class="h6">{{ $gradeEncodingPeriod->name }}</span>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <strong class="text-muted">Period Type:</strong><br>
                                    <span class="badge badge-primary">{{ ucfirst($gradeEncodingPeriod->period_type) }}</span>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <strong class="text-muted">Academic Year:</strong><br>
                                    <span>{{ $gradeEncodingPeriod->academicYear->name ?? 'N/A' }}</span>
                                    @if($gradeEncodingPeriod->academicYear && $gradeEncodingPeriod->academicYear->is_current)
                                        <span class="badge badge-info ms-1">Current</span>
                                    @endif
                                </div>
                                <div class="col-md-6 mb-3">
                                    <strong class="text-muted">Semester:</strong><br>
                                    <span>{{ $gradeEncodingPeriod->semester->name ?? 'N/A' }}</span>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <strong class="text-muted">Status:</strong><br>
                                    <span class="badge badge-{{ $gradeEncodingPeriod->status === 'active' ? 'success' : ($gradeEncodingPeriod->status === 'expired' ? 'danger' : 'secondary') }}">
                                        {{ ucfirst($gradeEncodingPeriod->status) }}
                                    </span>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <strong class="text-muted">Priority:</strong><br>
                                    <span class="badge badge-{{ ($gradeEncodingPeriod->priority ?? 'normal') === 'urgent' ? 'danger' : (($gradeEncodingPeriod->priority ?? 'normal') === 'high' ? 'warning' : 'secondary') }}">
                                        {{ ucfirst($gradeEncodingPeriod->priority ?? 'Normal') }}
                                    </span>
                                </div>
                                @if($gradeEncodingPeriod->description)
                                    <div class="col-12 mb-3">
                                        <strong class="text-muted">Description:</strong><br>
                                        <p class="mb-0">{{ $gradeEncodingPeriod->description }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <!-- Period Duration -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">
                                <i class="fas fa-calendar-alt"></i> Period Duration
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <strong class="text-muted">Start Date:</strong><br>
                                    <span class="h6">{{ $gradeEncodingPeriod->start_date->format('M d, Y') }}</span><br>
                                    <small class="text-muted">{{ $gradeEncodingPeriod->start_date->diffForHumans() }}</small>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <strong class="text-muted">End Date:</strong><br>
                                    <span class="h6">{{ $gradeEncodingPeriod->end_date->format('M d, Y') }}</span><br>
                                    <small class="text-muted">{{ $gradeEncodingPeriod->end_date->diffForHumans() }}</small>
                                </div>
                                @if($gradeEncodingPeriod->deadline_date)
                                    <div class="col-md-4 mb-3">
                                        <strong class="text-muted">Deadline:</strong><br>
                                        <span class="h6 {{ $gradeEncodingPeriod->deadline_date->isPast() ? 'text-danger' : ($gradeEncodingPeriod->deadline_date->isToday() ? 'text-warning' : '') }}">
                                            {{ $gradeEncodingPeriod->deadline_date->format('M d, Y H:i') }}
                                        </span><br>
                                        <small class="text-muted">{{ $gradeEncodingPeriod->deadline_date->diffForHumans() }}</small>
                                        @if($gradeEncodingPeriod->original_deadline_date && $gradeEncodingPeriod->deadline_date != $gradeEncodingPeriod->original_deadline_date)
                                            <br><small class="text-info">Original: {{ $gradeEncodingPeriod->original_deadline_date->format('M d, Y H:i') }}</small>
                                        @endif
                                    </div>
                                @endif
                            </div>
                            
                            <!-- Duration Progress Bar -->
                            @php
                                $now = now();
                                $start = $gradeEncodingPeriod->start_date;
                                $end = $gradeEncodingPeriod->end_date;
                                $total = $start->diffInDays($end);
                                $elapsed = $start->diffInDays($now);
                                $progress = $total > 0 ? min(100, max(0, ($elapsed / $total) * 100)) : 0;
                            @endphp
                            <div class="mt-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <strong class="text-muted">Period Progress:</strong>
                                    <span class="text-muted">{{ number_format($progress, 1) }}%</span>
                                </div>
                                <div class="progress" style="height: 10px;">
                                    <div class="progress-bar {{ $progress >= 100 ? 'bg-danger' : ($progress >= 80 ? 'bg-warning' : 'bg-success') }}" 
                                         role="progressbar" style="width: {{ $progress }}%" 
                                         aria-valuenow="{{ $progress }}" aria-valuemin="0" aria-valuemax="100">
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between mt-1">
                                    <small class="text-muted">{{ $start->format('M d') }}</small>
                                    <small class="text-muted">{{ $end->format('M d') }}</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Extension History -->
                    @if($gradeEncodingPeriod->extensions && $gradeEncodingPeriod->extensions->count() > 0)
                        <div class="card shadow mb-4">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">
                                    <i class="fas fa-history"></i> Extension History
                                    <span class="badge badge-info ms-2">{{ $gradeEncodingPeriod->extensions->count() }} Extension(s)</span>
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Extension #</th>
                                                <th>Previous Deadline</th>
                                                <th>New Deadline</th>
                                                <th>Days Extended</th>
                                                <th>Reason</th>
                                                <th>Extended By</th>
                                                <th>Date</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($gradeEncodingPeriod->extensions as $extension)
                                                <tr>
                                                    <td>
                                                        <span class="badge badge-primary">{{ $loop->iteration }}</span>
                                                    </td>
                                                    <td>{{ $extension->previous_deadline->format('M d, Y H:i') }}</td>
                                                    <td>{{ $extension->new_deadline->format('M d, Y H:i') }}</td>
                                                    <td>
                                                        <span class="badge badge-warning">+{{ $extension->days_extended }} days</span>
                                                    </td>
                                                    <td>{{ $extension->reason ?? 'No reason provided' }}</td>
                                                    <td>{{ $extension->extendedBy->name ?? 'System' }}</td>
                                                    <td>{{ $extension->created_at->format('M d, Y H:i') }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endif
                    
                    <!-- Instructions -->
                    @if($gradeEncodingPeriod->instructions)
                        <div class="card shadow mb-4">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">
                                    <i class="fas fa-clipboard-list"></i> Instructions for Faculty
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i>
                                    {{ $gradeEncodingPeriod->instructions }}
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
                
                <!-- Sidebar -->
                <div class="col-lg-4">
                    <!-- Statistics -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">
                                <i class="fas fa-chart-bar"></i> Statistics
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row text-center">
                                <div class="col-6 mb-3">
                                    <div class="border-right">
                                        <h4 class="text-primary">{{ $gradeEncodingPeriod->total_submissions ?? 0 }}</h4>
                                        <small class="text-muted">Total Submissions</small>
                                    </div>
                                </div>
                                <div class="col-6 mb-3">
                                    <h4 class="text-success">{{ $gradeEncodingPeriod->completed_submissions ?? 0 }}</h4>
                                    <small class="text-muted">Completed</small>
                                </div>
                                <div class="col-6 mb-3">
                                    <div class="border-right">
                                        <h4 class="text-warning">{{ $gradeEncodingPeriod->pending_submissions ?? 0 }}</h4>
                                        <small class="text-muted">Pending</small>
                                    </div>
                                </div>
                                <div class="col-6 mb-3">
                                    <h4 class="text-danger">{{ $gradeEncodingPeriod->late_submissions ?? 0 }}</h4>
                                    <small class="text-muted">Late</small>
                                </div>
                            </div>
                            
                            @if(($gradeEncodingPeriod->total_submissions ?? 0) > 0)
                                @php
                                    $completionRate = (($gradeEncodingPeriod->completed_submissions ?? 0) / ($gradeEncodingPeriod->total_submissions ?? 1)) * 100;
                                @endphp
                                <div class="mt-3">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <strong class="text-muted">Completion Rate:</strong>
                                        <span class="text-muted">{{ number_format($completionRate, 1) }}%</span>
                                    </div>
                                    <div class="progress" style="height: 8px;">
                                        <div class="progress-bar {{ $completionRate >= 80 ? 'bg-success' : ($completionRate >= 50 ? 'bg-warning' : 'bg-danger') }}" 
                                             role="progressbar" style="width: {{ $completionRate }}%" 
                                             aria-valuenow="{{ $completionRate }}" aria-valuemin="0" aria-valuemax="100">
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Settings Summary -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">
                                <i class="fas fa-cogs"></i> Settings
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <strong class="text-muted">Extensions:</strong><br>
                                <span>{{ $gradeEncodingPeriod->extensions_count ?? 0 }} / {{ $gradeEncodingPeriod->max_extensions ?? 2 }} used</span>
                                @if(($gradeEncodingPeriod->extensions_count ?? 0) >= ($gradeEncodingPeriod->max_extensions ?? 2))
                                    <span class="badge badge-danger ms-1">Max Reached</span>
                                @endif
                            </div>
                            
                            <div class="mb-3">
                                <strong class="text-muted">Default Extension:</strong><br>
                                <span>{{ $gradeEncodingPeriod->extension_days ?? 3 }} days</span>
                            </div>
                            
                            <div class="mb-3">
                                <strong class="text-muted">Late Submissions:</strong><br>
                                @if($gradeEncodingPeriod->allow_late_submission)
                                    <span class="text-success">Allowed</span>
                                    @if($gradeEncodingPeriod->late_penalty > 0)
                                        <br><small class="text-muted">Penalty: {{ $gradeEncodingPeriod->late_penalty }}%</small>
                                    @endif
                                    @if($gradeEncodingPeriod->grace_period_hours > 0)
                                        <br><small class="text-muted">Grace Period: {{ $gradeEncodingPeriod->grace_period_hours }} hours</small>
                                    @endif
                                @else
                                    <span class="text-danger">Not Allowed</span>
                                @endif
                            </div>
                            
                            <div class="mb-3">
                                <strong class="text-muted">Notifications:</strong><br>
                                <span class="{{ $gradeEncodingPeriod->send_notifications ? 'text-success' : 'text-danger' }}">
                                    {{ $gradeEncodingPeriod->send_notifications ? 'Enabled' : 'Disabled' }}
                                </span>
                                @if($gradeEncodingPeriod->send_notifications && $gradeEncodingPeriod->reminder_days)
                                    <br><small class="text-muted">Reminders: {{ $gradeEncodingPeriod->reminder_days }} days before</small>
                                @endif
                            </div>
                            
                            <div class="mb-3">
                                <strong class="text-muted">Auto Close:</strong><br>
                                <span class="{{ $gradeEncodingPeriod->auto_close ? 'text-success' : 'text-danger' }}">
                                    {{ $gradeEncodingPeriod->auto_close ? 'Enabled' : 'Disabled' }}
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Recent Activity -->
                    @if($gradeEncodingPeriod->activities && $gradeEncodingPeriod->activities->count() > 0)
                        <div class="card shadow mb-4">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">
                                    <i class="fas fa-clock"></i> Recent Activity
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="timeline">
                                    @foreach($gradeEncodingPeriod->activities->take(5) as $activity)
                                        <div class="timeline-item mb-3">
                                            <div class="timeline-marker bg-{{ $activity->type === 'created' ? 'success' : ($activity->type === 'extended' ? 'warning' : 'info') }}"></div>
                                            <div class="timeline-content">
                                                <h6 class="mb-1">{{ $activity->description }}</h6>
                                                <small class="text-muted">
                                                    {{ $activity->created_at->diffForHumans() }}
                                                    @if($activity->causer)
                                                        by {{ $activity->causer->name }}
                                                    @endif
                                                </small>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif
                    
                    <!-- Audit Information -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">
                                <i class="fas fa-info"></i> Audit Information
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <strong class="text-muted">Created:</strong><br>
                                <span>{{ $gradeEncodingPeriod->created_at->format('M d, Y H:i') }}</span><br>
                                <small class="text-muted">by {{ $gradeEncodingPeriod->createdBy->name ?? 'System' }}</small>
                            </div>
                            
                            <div class="mb-3">
                                <strong class="text-muted">Last Updated:</strong><br>
                                <span>{{ $gradeEncodingPeriod->updated_at->format('M d, Y H:i') }}</span><br>
                                <small class="text-muted">by {{ $gradeEncodingPeriod->updatedBy->name ?? 'System' }}</small>
                            </div>
                            
                            @if($gradeEncodingPeriod->admin_notes)
                                <div class="mb-3">
                                    <strong class="text-muted">Admin Notes:</strong><br>
                                    <div class="alert alert-secondary p-2 mt-2">
                                        <small>{{ $gradeEncodingPeriod->admin_notes }}</small>
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

<!-- Extend Deadline Modal -->
<div class="modal fade" id="extendDeadlineModal" tabindex="-1" aria-labelledby="extendDeadlineModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.grade-encoding-periods.extend-deadline', $gradeEncodingPeriod) }}" method="POST">
                @csrf
                @method('PATCH')
                <div class="modal-header">
                    <h5 class="modal-title" id="extendDeadlineModalLabel">
                        <i class="fas fa-clock"></i> Extend Deadline
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <strong>Current Deadline:</strong> {{ $gradeEncodingPeriod->deadline_date?->format('M d, Y H:i') ?? 'Not set' }}<br>
                        <strong>Extensions Used:</strong> {{ $gradeEncodingPeriod->extensions_count ?? 0 }} / {{ $gradeEncodingPeriod->max_extensions ?? 2 }}
                    </div>
                    
                    <div class="mb-3">
                        <label for="extension_days" class="form-label">Extension Days <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="extension_days" name="extension_days" 
                               value="{{ $gradeEncodingPeriod->extension_days ?? 3 }}" min="1" max="30" required>
                        <div class="form-text">Number of days to extend the deadline</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="extension_reason" class="form-label">Reason for Extension <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="extension_reason" name="extension_reason" rows="3" 
                                  placeholder="Provide a reason for extending the deadline..." required></textarea>
                        <div class="form-text">This will be logged for audit purposes</div>
                    </div>
                    
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="notify_faculty" name="notify_faculty" value="1" checked>
                        <label class="form-check-label" for="notify_faculty">
                            Notify faculty members about the extension
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-clock"></i> Extend Deadline
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Export Modal -->
<div class="modal fade" id="exportModal" tabindex="-1" aria-labelledby="exportModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exportModalLabel">
                    <i class="fas fa-download"></i> Export Period Data
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Choose the format to export the grade encoding period data:</p>
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.grade-encoding-periods.export', ['period' => $gradeEncodingPeriod, 'format' => 'pdf']) }}" 
                       class="btn btn-danger">
                        <i class="fas fa-file-pdf"></i> Export as PDF
                    </a>
                    <a href="{{ route('admin.grade-encoding-periods.export', ['period' => $gradeEncodingPeriod, 'format' => 'excel']) }}" 
                       class="btn btn-success">
                        <i class="fas fa-file-excel"></i> Export as Excel
                    </a>
                    <a href="{{ route('admin.grade-encoding-periods.export', ['period' => $gradeEncodingPeriod, 'format' => 'csv']) }}" 
                       class="btn btn-info">
                        <i class="fas fa-file-csv"></i> Export as CSV
                    </a>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times"></i> Close
                </button>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.timeline {
    position: relative;
    padding-left: 20px;
}

.timeline-item {
    position: relative;
    padding-left: 25px;
}

.timeline-marker {
    position: absolute;
    left: -8px;
    top: 5px;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    border: 2px solid #fff;
    box-shadow: 0 0 0 2px #e3e6f0;
}

.timeline::before {
    content: '';
    position: absolute;
    left: -2px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #e3e6f0;
}

.border-right {
    border-right: 1px solid #e3e6f0;
}

.progress {
    border-radius: 10px;
    overflow: hidden;
}

.card {
    border: none;
    box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
}

.card-header {
    background-color: #f8f9fc;
    border-bottom: 1px solid #e3e6f0;
}

.text-primary {
    color: #4e73df !important;
}

.badge {
    font-size: 0.8em;
}

.btn-group .btn {
    margin-right: 0;
}

.alert {
    border-radius: 0.35rem;
}

@media print {
    .btn, .modal, .card-header .btn-group {
        display: none !important;
    }
    
    .card {
        box-shadow: none;
        border: 1px solid #dee2e6;
    }
    
    .alert {
        border: 1px solid #dee2e6;
        background-color: #f8f9fa !important;
        color: #495057 !important;
    }
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Auto-calculate new deadline in extend modal
    const extensionDaysInput = document.getElementById('extension_days');
    if (extensionDaysInput) {
        extensionDaysInput.addEventListener('input', function() {
            const currentDeadline = new Date('{{ $gradeEncodingPeriod->deadline_date?->toISOString() }}');
            const extensionDays = parseInt(this.value) || 0;
            const newDeadline = new Date(currentDeadline.getTime() + (extensionDays * 24 * 60 * 60 * 1000));
            
            // Update modal info (you could add a preview element)
            console.log('New deadline would be:', newDeadline.toLocaleDateString());
        });
    }
    
    // Confirm dangerous actions
    document.querySelectorAll('form[action*="toggle-status"], form[action*="set-current"]').forEach(form => {
        form.addEventListener('submit', function(e) {
            const button = this.querySelector('button[type="submit"]');
            if (button && !confirm(button.getAttribute('onclick')?.replace('return confirm(', '').replace(')', '').replace(/'/g, ''))) {
                e.preventDefault();
            }
        });
    });
});
</script>
@endpush
@endsection