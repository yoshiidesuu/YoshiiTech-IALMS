@extends('layouts.admin')

@section('page-title', 'Create Grade Encoding Period')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Create New Grade Encoding Period</h6>
                    <a href="{{ route('admin.grade-encoding-periods.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left"></i> Back to List
                    </a>
                </div>
                
                <form action="{{ route('admin.grade-encoding-periods.store') }}" method="POST" id="createPeriodForm">
                    @csrf
                    <div class="card-body">
                        <!-- Basic Information -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="text-primary border-bottom pb-2 mb-3">
                                    <i class="fas fa-info-circle"></i> Basic Information
                                </h5>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Period Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       id="name" name="name" value="{{ old('name') }}" 
                                       placeholder="e.g., Midterm Grades - First Semester 2024-2025" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Enter a descriptive name for this encoding period</div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="period_type" class="form-label">Period Type <span class="text-danger">*</span></label>
                                <select class="form-select @error('period_type') is-invalid @enderror" 
                                        id="period_type" name="period_type" required>
                                    <option value="">Select Period Type</option>
                                    <option value="midterm" {{ old('period_type') === 'midterm' ? 'selected' : '' }}>Midterm</option>
                                    <option value="final" {{ old('period_type') === 'final' ? 'selected' : '' }}>Final</option>
                                    <option value="makeup" {{ old('period_type') === 'makeup' ? 'selected' : '' }}>Makeup</option>
                                    <option value="special" {{ old('period_type') === 'special' ? 'selected' : '' }}>Special</option>
                                </select>
                                @error('period_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="academic_year_id" class="form-label">Academic Year <span class="text-danger">*</span></label>
                                <select class="form-select @error('academic_year_id') is-invalid @enderror" 
                                        id="academic_year_id" name="academic_year_id" required>
                                    <option value="">Select Academic Year</option>
                                    @foreach($academicYears ?? [] as $year)
                                        <option value="{{ $year->id }}" {{ old('academic_year_id') == $year->id ? 'selected' : '' }}>
                                            {{ $year->name }} 
                                            @if($year->is_current)
                                                <span class="badge badge-primary">Current</span>
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                                @error('academic_year_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="semester_id" class="form-label">Semester <span class="text-danger">*</span></label>
                                <select class="form-select @error('semester_id') is-invalid @enderror" 
                                        id="semester_id" name="semester_id" required>
                                    <option value="">Select Semester</option>
                                    @foreach($semesters ?? [] as $semester)
                                        <option value="{{ $semester->id }}" {{ old('semester_id') == $semester->id ? 'selected' : '' }}
                                                data-academic-year="{{ $semester->academic_year_id }}">
                                            {{ $semester->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('semester_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-12 mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control @error('description') is-invalid @enderror" 
                                          id="description" name="description" rows="3" 
                                          placeholder="Optional description for this encoding period...">{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Provide additional details about this encoding period</div>
                            </div>
                        </div>
                        
                        <!-- Period Duration -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="text-primary border-bottom pb-2 mb-3">
                                    <i class="fas fa-calendar-alt"></i> Period Duration
                                </h5>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="start_date" class="form-label">Start Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('start_date') is-invalid @enderror" 
                                       id="start_date" name="start_date" value="{{ old('start_date') }}" required>
                                @error('start_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">When the encoding period begins</div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="end_date" class="form-label">End Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('end_date') is-invalid @enderror" 
                                       id="end_date" name="end_date" value="{{ old('end_date') }}" required>
                                @error('end_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">When the encoding period ends</div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="deadline_date" class="form-label">Deadline Date</label>
                                <input type="date" class="form-control @error('deadline_date') is-invalid @enderror" 
                                       id="deadline_date" name="deadline_date" value="{{ old('deadline_date') }}">
                                @error('deadline_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Final deadline for grade submission (optional)</div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="deadline_time" class="form-label">Deadline Time</label>
                                <input type="time" class="form-control @error('deadline_time') is-invalid @enderror" 
                                       id="deadline_time" name="deadline_time" value="{{ old('deadline_time', '23:59') }}">
                                @error('deadline_time')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Time of day for the deadline</div>
                            </div>
                        </div>
                        
                        <!-- Period Settings -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="text-primary border-bottom pb-2 mb-3">
                                    <i class="fas fa-cogs"></i> Period Settings
                                </h5>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                <select class="form-select @error('status') is-invalid @enderror" 
                                        id="status" name="status" required>
                                    <option value="inactive" {{ old('status', 'inactive') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                    <option value="active" {{ old('status') === 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="upcoming" {{ old('status') === 'upcoming' ? 'selected' : '' }}>Upcoming</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Current status of the encoding period</div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="priority" class="form-label">Priority Level</label>
                                <select class="form-select @error('priority') is-invalid @enderror" 
                                        id="priority" name="priority">
                                    <option value="normal" {{ old('priority', 'normal') === 'normal' ? 'selected' : '' }}>Normal</option>
                                    <option value="high" {{ old('priority') === 'high' ? 'selected' : '' }}>High</option>
                                    <option value="urgent" {{ old('priority') === 'urgent' ? 'selected' : '' }}>Urgent</option>
                                </select>
                                @error('priority')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Priority level for this encoding period</div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="max_extensions" class="form-label">Maximum Extensions Allowed</label>
                                <input type="number" class="form-control @error('max_extensions') is-invalid @enderror" 
                                       id="max_extensions" name="max_extensions" value="{{ old('max_extensions', 2) }}" 
                                       min="0" max="10">
                                @error('max_extensions')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">How many times the deadline can be extended</div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="extension_days" class="form-label">Default Extension Days</label>
                                <input type="number" class="form-control @error('extension_days') is-invalid @enderror" 
                                       id="extension_days" name="extension_days" value="{{ old('extension_days', 3) }}" 
                                       min="1" max="30">
                                @error('extension_days')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Default number of days for each extension</div>
                            </div>
                        </div>
                        
                        <!-- Notification Settings -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="text-primary border-bottom pb-2 mb-3">
                                    <i class="fas fa-bell"></i> Notification Settings
                                </h5>
                            </div>
                            
                            <div class="col-12 mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="send_notifications" 
                                           name="send_notifications" value="1" {{ old('send_notifications', true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="send_notifications">
                                        Send notifications to faculty members
                                    </label>
                                </div>
                                <div class="form-text">Notify faculty when the encoding period starts</div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="reminder_days" class="form-label">Reminder Days Before Deadline</label>
                                <input type="text" class="form-control @error('reminder_days') is-invalid @enderror" 
                                       id="reminder_days" name="reminder_days" value="{{ old('reminder_days', '7,3,1') }}" 
                                       placeholder="7,3,1">
                                @error('reminder_days')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Comma-separated list of days before deadline to send reminders</div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <div class="form-check form-switch mt-4">
                                    <input class="form-check-input" type="checkbox" id="auto_close" 
                                           name="auto_close" value="1" {{ old('auto_close', true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="auto_close">
                                        Automatically close period after deadline
                                    </label>
                                </div>
                                <div class="form-text">Automatically set status to inactive after deadline passes</div>
                            </div>
                        </div>
                        
                        <!-- Additional Options -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="text-primary border-bottom pb-2 mb-3">
                                    <i class="fas fa-plus-circle"></i> Additional Options
                                </h5>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="is_current" 
                                           name="is_current" value="1" {{ old('is_current') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_current">
                                        Set as current encoding period
                                    </label>
                                </div>
                                <div class="form-text">This will replace any existing current period</div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="allow_late_submission" 
                                           name="allow_late_submission" value="1" {{ old('allow_late_submission') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="allow_late_submission">
                                        Allow late submissions
                                    </label>
                                </div>
                                <div class="form-text">Allow grade submissions after the deadline</div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="late_penalty" class="form-label">Late Submission Penalty (%)</label>
                                <input type="number" class="form-control @error('late_penalty') is-invalid @enderror" 
                                       id="late_penalty" name="late_penalty" value="{{ old('late_penalty', 0) }}" 
                                       min="0" max="100" step="0.1" disabled>
                                @error('late_penalty')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Penalty percentage for late submissions</div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="grace_period_hours" class="form-label">Grace Period (Hours)</label>
                                <input type="number" class="form-control @error('grace_period_hours') is-invalid @enderror" 
                                       id="grace_period_hours" name="grace_period_hours" value="{{ old('grace_period_hours', 0) }}" 
                                       min="0" max="72" disabled>
                                @error('grace_period_hours')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Grace period after deadline before penalties apply</div>
                            </div>
                        </div>
                        
                        <!-- Instructions/Notes -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="text-primary border-bottom pb-2 mb-3">
                                    <i class="fas fa-sticky-note"></i> Instructions & Notes
                                </h5>
                            </div>
                            
                            <div class="col-12 mb-3">
                                <label for="instructions" class="form-label">Instructions for Faculty</label>
                                <textarea class="form-control @error('instructions') is-invalid @enderror" 
                                          id="instructions" name="instructions" rows="4" 
                                          placeholder="Provide specific instructions for faculty members regarding this encoding period...">{{ old('instructions') }}</textarea>
                                @error('instructions')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Instructions that will be shown to faculty members</div>
                            </div>
                            
                            <div class="col-12 mb-3">
                                <label for="admin_notes" class="form-label">Admin Notes</label>
                                <textarea class="form-control @error('admin_notes') is-invalid @enderror" 
                                          id="admin_notes" name="admin_notes" rows="3" 
                                          placeholder="Internal notes for administrators...">{{ old('admin_notes') }}</textarea>
                                @error('admin_notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Internal notes visible only to administrators</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card-footer bg-light">
                        <div class="row">
                            <div class="col-md-6">
                                <button type="submit" class="btn btn-primary me-2" id="saveBtn">
                                    <i class="fas fa-save"></i> Create Period
                                </button>
                                <button type="submit" class="btn btn-success me-2" id="saveAndActivateBtn" 
                                        name="save_and_activate" value="1">
                                    <i class="fas fa-play"></i> Create & Activate
                                </button>
                                <a href="{{ route('admin.grade-encoding-periods.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Cancel
                                </a>
                            </div>
                            <div class="col-md-6 text-end">
                                <small class="text-muted">
                                    <i class="fas fa-info-circle"></i> 
                                    Fields marked with <span class="text-danger">*</span> are required
                                </small>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.form-label {
    font-weight: 600;
    color: #5a5c69;
}

.form-control:focus, .form-select:focus {
    border-color: #4e73df;
    box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
}

.form-check-input:checked {
    background-color: #4e73df;
    border-color: #4e73df;
}

.form-switch .form-check-input {
    width: 2em;
    margin-left: -2.5em;
}

.card-footer {
    background-color: #f8f9fc !important;
    border-top: 1px solid #e3e6f0;
}

.border-bottom {
    border-bottom: 2px solid #e3e6f0 !important;
}

.text-primary {
    color: #4e73df !important;
}

.btn-primary {
    background-color: #4e73df;
    border-color: #4e73df;
}

.btn-primary:hover {
    background-color: #2e59d9;
    border-color: #2653d4;
}

.btn-success {
    background-color: #1cc88a;
    border-color: #1cc88a;
}

.btn-success:hover {
    background-color: #17a673;
    border-color: #169b6b;
}

.invalid-feedback {
    display: block;
}

.form-text {
    font-size: 0.875rem;
    color: #6c757d;
}

.shadow {
    box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15) !important;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const academicYearSelect = document.getElementById('academic_year_id');
    const semesterSelect = document.getElementById('semester_id');
    const startDateInput = document.getElementById('start_date');
    const endDateInput = document.getElementById('end_date');
    const deadlineDateInput = document.getElementById('deadline_date');
    const allowLateSubmissionCheckbox = document.getElementById('allow_late_submission');
    const latePenaltyInput = document.getElementById('late_penalty');
    const gracePeriodInput = document.getElementById('grace_period_hours');
    const periodTypeSelect = document.getElementById('period_type');
    const nameInput = document.getElementById('name');
    
    // Filter semesters based on selected academic year
    academicYearSelect.addEventListener('change', function() {
        const selectedYearId = this.value;
        const semesterOptions = semesterSelect.querySelectorAll('option');
        
        semesterOptions.forEach(option => {
            if (option.value === '') {
                option.style.display = 'block';
                return;
            }
            
            const academicYearId = option.getAttribute('data-academic-year');
            if (selectedYearId === '' || academicYearId === selectedYearId) {
                option.style.display = 'block';
            } else {
                option.style.display = 'none';
            }
        });
        
        // Reset semester selection if current selection is not valid
        const currentSemesterOption = semesterSelect.querySelector(`option[value="${semesterSelect.value}"]`);
        if (currentSemesterOption && currentSemesterOption.style.display === 'none') {
            semesterSelect.value = '';
        }
        
        updatePeriodName();
    });
    
    // Update period name based on selections
    function updatePeriodName() {
        const periodType = periodTypeSelect.value;
        const academicYearText = academicYearSelect.options[academicYearSelect.selectedIndex]?.text || '';
        const semesterText = semesterSelect.options[semesterSelect.selectedIndex]?.text || '';
        
        if (periodType && academicYearText && semesterText && !nameInput.value) {
            const suggestedName = `${periodType.charAt(0).toUpperCase() + periodType.slice(1)} Grades - ${semesterText} ${academicYearText}`;
            nameInput.placeholder = suggestedName;
        }
    }
    
    periodTypeSelect.addEventListener('change', updatePeriodName);
    semesterSelect.addEventListener('change', updatePeriodName);
    
    // Validate date ranges
    startDateInput.addEventListener('change', function() {
        if (this.value) {
            endDateInput.min = this.value;
            deadlineDateInput.min = this.value;
            
            // If end date is before start date, reset it
            if (endDateInput.value && endDateInput.value < this.value) {
                endDateInput.value = '';
            }
            
            // If deadline date is before start date, reset it
            if (deadlineDateInput.value && deadlineDateInput.value < this.value) {
                deadlineDateInput.value = '';
            }
        }
    });
    
    endDateInput.addEventListener('change', function() {
        if (this.value) {
            startDateInput.max = this.value;
            
            // Set deadline date to end date if not set
            if (!deadlineDateInput.value) {
                deadlineDateInput.value = this.value;
            }
        }
    });
    
    // Enable/disable late submission fields
    allowLateSubmissionCheckbox.addEventListener('change', function() {
        latePenaltyInput.disabled = !this.checked;
        gracePeriodInput.disabled = !this.checked;
        
        if (!this.checked) {
            latePenaltyInput.value = 0;
            gracePeriodInput.value = 0;
        }
    });
    
    // Form validation
    document.getElementById('createPeriodForm').addEventListener('submit', function(e) {
        let isValid = true;
        const errors = [];
        
        // Check if end date is after start date
        if (startDateInput.value && endDateInput.value && endDateInput.value <= startDateInput.value) {
            errors.push('End date must be after start date');
            isValid = false;
        }
        
        // Check if deadline date is within the period range
        if (deadlineDateInput.value) {
            if (startDateInput.value && deadlineDateInput.value < startDateInput.value) {
                errors.push('Deadline date cannot be before start date');
                isValid = false;
            }
            if (endDateInput.value && deadlineDateInput.value > endDateInput.value) {
                errors.push('Deadline date cannot be after end date');
                isValid = false;
            }
        }
        
        // Validate reminder days format
        const reminderDays = document.getElementById('reminder_days').value;
        if (reminderDays && !/^\d+(,\d+)*$/.test(reminderDays.trim())) {
            errors.push('Reminder days must be comma-separated numbers (e.g., 7,3,1)');
            isValid = false;
        }
        
        if (!isValid) {
            e.preventDefault();
            alert('Please fix the following errors:\n\n' + errors.join('\n'));
        }
    });
    
    // Auto-suggest period name
    nameInput.addEventListener('focus', function() {
        if (!this.value && this.placeholder && this.placeholder !== 'e.g., Midterm Grades - First Semester 2024-2025') {
            this.value = this.placeholder;
        }
    });
    
    // Initialize form
    updatePeriodName();
    
    // Set default dates if academic year is selected
    if (academicYearSelect.value) {
        academicYearSelect.dispatchEvent(new Event('change'));
    }
});
</script>
@endpush
@endsection