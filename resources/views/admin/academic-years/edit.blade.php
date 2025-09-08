@extends('layouts.admin')

@section('page-title', 'Edit Academic Year')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Edit Academic Year: {{ $academicYear->name }}</h5>
                    <div>
                        <a href="{{ route('admin.academic-years.show', $academicYear) }}" class="btn btn-info btn-sm">
                            <i class="bi bi-eye"></i> View
                        </a>
                        <a href="{{ route('admin.academic-years.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Back to Academic Years
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if($academicYear->is_current)
                        <div class="alert alert-info mb-3">
                            <i class="bi bi-info-circle"></i>
                            <strong>Current Academic Year:</strong> This is the currently active academic year. Changes may affect system-wide operations.
                        </div>
                    @endif
                    
                    <form action="{{ route('admin.academic-years.update', $academicYear) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Academic Year Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                           id="name" name="name" value="{{ old('name', $academicYear->name) }}" 
                                           placeholder="e.g., 2024-2025" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Enter the academic year in format: YYYY-YYYY</div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                    <select class="form-select @error('status') is-invalid @enderror" 
                                            id="status" name="status" required>
                                        <option value="">Select status...</option>
                                        <option value="active" {{ old('status', $academicYear->status) == 'active' ? 'selected' : '' }}>Active</option>
                                        <option value="inactive" {{ old('status', $academicYear->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
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
                                           id="start_date" name="start_date" 
                                           value="{{ old('start_date', $academicYear->start_date?->format('Y-m-d')) }}" required>
                                    @error('start_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="end_date" class="form-label">End Date <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control @error('end_date') is-invalid @enderror" 
                                           id="end_date" name="end_date" 
                                           value="{{ old('end_date', $academicYear->end_date?->format('Y-m-d')) }}" required>
                                    @error('end_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea class="form-control @error('description') is-invalid @enderror" 
                                              id="description" name="description" rows="3" 
                                              placeholder="Optional description for this academic year...">{{ old('description', $academicYear->description) }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input @error('is_current') is-invalid @enderror" 
                                               type="checkbox" id="is_current" name="is_current" value="1" 
                                               {{ old('is_current', $academicYear->is_current) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_current">
                                            Set as Current Academic Year
                                        </label>
                                        @error('is_current')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <div class="form-text">Only one academic year can be current at a time.</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Additional Information -->
                        <div class="row">
                            <div class="col-12">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6 class="card-title">Additional Information</h6>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <small class="text-muted">Created:</small><br>
                                                <span class="fw-bold">{{ $academicYear->created_at->format('M d, Y g:i A') }}</span>
                                            </div>
                                            <div class="col-md-4">
                                                <small class="text-muted">Last Updated:</small><br>
                                                <span class="fw-bold">{{ $academicYear->updated_at->format('M d, Y g:i A') }}</span>
                                            </div>
                                            <div class="col-md-4">
                                                <small class="text-muted">Total Semesters:</small><br>
                                                <span class="fw-bold">{{ $academicYear->semesters_count ?? $academicYear->semesters->count() }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('admin.academic-years.index') }}" class="btn btn-secondary">
                                        <i class="bi bi-x-circle"></i> Cancel
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-check-circle"></i> Update Academic Year
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Auto-generate academic year name -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const startDateInput = document.getElementById('start_date');
    const endDateInput = document.getElementById('end_date');
    const nameInput = document.getElementById('name');
    
    function updateAcademicYearName() {
        const startDate = startDateInput.value;
        const endDate = endDateInput.value;
        
        if (startDate && endDate) {
            const startYear = new Date(startDate).getFullYear();
            const endYear = new Date(endDate).getFullYear();
            
            if (startYear && endYear) {
                // Only update if name field is empty or follows the YYYY-YYYY pattern
                const currentName = nameInput.value;
                if (!currentName || /^\d{4}-\d{4}$/.test(currentName)) {
                    nameInput.value = `${startYear}-${endYear}`;
                }
            }
        }
    }
    
    startDateInput.addEventListener('change', updateAcademicYearName);
    endDateInput.addEventListener('change', updateAcademicYearName);
    
    // Validate end date is after start date
    endDateInput.addEventListener('change', function() {
        const startDate = new Date(startDateInput.value);
        const endDate = new Date(endDateInput.value);
        
        if (startDate && endDate && endDate <= startDate) {
            endDateInput.setCustomValidity('End date must be after start date');
        } else {
            endDateInput.setCustomValidity('');
        }
    });
});
</script>
@endsection