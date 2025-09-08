@extends('layouts.admin')

@section('page-title', 'Create New Semester')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Create New Semester</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.semesters.store') }}" method="POST" id="semesterForm">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Semester Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                           id="name" name="name" value="{{ old('name') }}" 
                                           placeholder="e.g., First Semester 2024-2025" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="academic_year_id" class="form-label">Academic Year <span class="text-danger">*</span></label>
                                    <select class="form-select @error('academic_year_id') is-invalid @enderror" 
                                            id="academic_year_id" name="academic_year_id" required>
                                        <option value="">Select Academic Year</option>
                                        @foreach($academicYears as $year)
                                            <option value="{{ $year->id }}" {{ old('academic_year_id') == $year->id ? 'selected' : '' }}>
                                                {{ $year->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('academic_year_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="term_number" class="form-label">Term Number <span class="text-danger">*</span></label>
                                    <select class="form-select @error('term_number') is-invalid @enderror" 
                                            id="term_number" name="term_number" required>
                                        <option value="">Select Term</option>
                                        <option value="1" {{ old('term_number') == '1' ? 'selected' : '' }}>1st Term</option>
                                        <option value="2" {{ old('term_number') == '2' ? 'selected' : '' }}>2nd Term</option>
                                        <option value="3" {{ old('term_number') == '3' ? 'selected' : '' }}>3rd Term</option>
                                        <option value="summer" {{ old('term_number') == 'summer' ? 'selected' : '' }}>Summer Term</option>
                                    </select>
                                    @error('term_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                    <select class="form-select @error('status') is-invalid @enderror" 
                                            id="status" name="status" required>
                                        <option value="active" {{ old('status', 'active') === 'active' ? 'selected' : '' }}>Active</option>
                                        <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="start_date" class="form-label">Start Date <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control @error('start_date') is-invalid @enderror" 
                                           id="start_date" name="start_date" value="{{ old('start_date') }}" required>
                                    @error('start_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="end_date" class="form-label">End Date <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control @error('end_date') is-invalid @enderror" 
                                           id="end_date" name="end_date" value="{{ old('end_date') }}" required>
                                    @error('end_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="enrollment_start" class="form-label">Enrollment Start Date</label>
                                    <input type="date" class="form-control @error('enrollment_start') is-invalid @enderror" 
                                           id="enrollment_start" name="enrollment_start" value="{{ old('enrollment_start') }}">
                                    @error('enrollment_start')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">When students can start enrolling</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="enrollment_end" class="form-label">Enrollment End Date</label>
                                    <input type="date" class="form-control @error('enrollment_end') is-invalid @enderror" 
                                           id="enrollment_end" name="enrollment_end" value="{{ old('enrollment_end') }}">
                                    @error('enrollment_end')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">When enrollment period ends</small>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="3" 
                                      placeholder="Optional description for this semester">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input @error('is_current') is-invalid @enderror" 
                                       type="checkbox" id="is_current" name="is_current" value="1" 
                                       {{ old('is_current') ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_current">
                                    Set as Current Semester
                                </label>
                                @error('is_current')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted d-block">This will unset any existing current semester</small>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.semesters.index') }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Back to Semesters
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check"></i> Create Semester
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Auto-generate semester name based on academic year and term
    function generateSemesterName() {
        const academicYear = $('#academic_year_id option:selected').text();
        const termNumber = $('#term_number').val();
        
        if (academicYear && academicYear !== 'Select Academic Year' && termNumber) {
            let termName = '';
            switch(termNumber) {
                case '1':
                    termName = 'First Semester';
                    break;
                case '2':
                    termName = 'Second Semester';
                    break;
                case '3':
                    termName = 'Third Semester';
                    break;
                case 'summer':
                    termName = 'Summer Term';
                    break;
            }
            
            if (termName) {
                const generatedName = `${termName} ${academicYear}`;
                $('#name').val(generatedName);
            }
        }
    }
    
    // Trigger name generation when academic year or term changes
    $('#academic_year_id, #term_number').on('change', generateSemesterName);
    
    // Date validation
    $('#start_date, #end_date').on('change', function() {
        const startDate = new Date($('#start_date').val());
        const endDate = new Date($('#end_date').val());
        
        if (startDate && endDate && startDate >= endDate) {
            $('#end_date')[0].setCustomValidity('End date must be after start date');
        } else {
            $('#end_date')[0].setCustomValidity('');
        }
    });
    
    // Enrollment date validation
    $('#enrollment_start, #enrollment_end').on('change', function() {
        const enrollmentStart = new Date($('#enrollment_start').val());
        const enrollmentEnd = new Date($('#enrollment_end').val());
        
        if (enrollmentStart && enrollmentEnd && enrollmentStart >= enrollmentEnd) {
            $('#enrollment_end')[0].setCustomValidity('Enrollment end date must be after start date');
        } else {
            $('#enrollment_end')[0].setCustomValidity('');
        }
    });
    
    // Validate enrollment dates are within semester dates
    $('#enrollment_start, #enrollment_end, #start_date, #end_date').on('change', function() {
        const semesterStart = new Date($('#start_date').val());
        const semesterEnd = new Date($('#end_date').val());
        const enrollmentStart = new Date($('#enrollment_start').val());
        const enrollmentEnd = new Date($('#enrollment_end').val());
        
        if (semesterStart && enrollmentStart && enrollmentStart < semesterStart) {
            $('#enrollment_start')[0].setCustomValidity('Enrollment start must be within semester dates');
        } else {
            $('#enrollment_start')[0].setCustomValidity('');
        }
        
        if (semesterEnd && enrollmentEnd && enrollmentEnd > semesterEnd) {
            $('#enrollment_end')[0].setCustomValidity('Enrollment end must be within semester dates');
        } else {
            $('#enrollment_end')[0].setCustomValidity('');
        }
    });
    
    // Form submission validation
    $('#semesterForm').on('submit', function(e) {
        const startDate = new Date($('#start_date').val());
        const endDate = new Date($('#end_date').val());
        
        if (startDate >= endDate) {
            e.preventDefault();
            alert('Please ensure the end date is after the start date.');
            return false;
        }
        
        const enrollmentStart = $('#enrollment_start').val();
        const enrollmentEnd = $('#enrollment_end').val();
        
        if (enrollmentStart && enrollmentEnd) {
            const enrollStartDate = new Date(enrollmentStart);
            const enrollEndDate = new Date(enrollmentEnd);
            
            if (enrollStartDate >= enrollEndDate) {
                e.preventDefault();
                alert('Please ensure the enrollment end date is after the enrollment start date.');
                return false;
            }
        }
    });
});
</script>
@endpush
@endsection