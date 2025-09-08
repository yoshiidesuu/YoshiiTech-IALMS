@extends('layouts.admin')

@section('page-title', 'Edit Subject')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Edit Subject: {{ $subject->code }}</h5>
                    <div class="btn-group">
                        <a href="{{ route('admin.subjects.show', $subject) }}" class="btn btn-outline-info">
                            <i class="bi bi-eye"></i> View Details
                        </a>
                        <a href="{{ route('admin.subjects.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left"></i> Back to Subjects
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <h6>Please correct the following errors:</h6>
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('admin.subjects.update', $subject) }}" method="POST" id="subjectForm">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <!-- Basic Information -->
                            <div class="col-md-6">
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <h6 class="mb-0">Basic Information</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="code" class="form-label">Subject Code <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('code') is-invalid @enderror" 
                                                   id="code" name="code" value="{{ old('code', $subject->code) }}" 
                                                   placeholder="e.g., CS101, MATH201" required>
                                            @error('code')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <div class="form-text">Unique identifier for the subject</div>
                                        </div>

                                        <div class="mb-3">
                                            <label for="name" class="form-label">Subject Name <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                                   id="name" name="name" value="{{ old('name', $subject->name) }}" 
                                                   placeholder="e.g., Introduction to Computer Science" required>
                                            @error('name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="description" class="form-label">Description</label>
                                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                                      id="description" name="description" rows="4" 
                                                      placeholder="Brief description of the subject content and objectives">{{ old('description', $subject->description) }}</textarea>
                                            @error('description')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="category" class="form-label">Category <span class="text-danger">*</span></label>
                                                    <select class="form-select @error('category') is-invalid @enderror" 
                                                            id="category" name="category" required>
                                                        <option value="">Select Category</option>
                                                        <option value="core" {{ old('category', $subject->category) === 'core' ? 'selected' : '' }}>Core</option>
                                                        <option value="major" {{ old('category', $subject->category) === 'major' ? 'selected' : '' }}>Major</option>
                                                        <option value="minor" {{ old('category', $subject->category) === 'minor' ? 'selected' : '' }}>Minor</option>
                                                        <option value="elective" {{ old('category', $subject->category) === 'elective' ? 'selected' : '' }}>Elective</option>
                                                        <option value="general_education" {{ old('category', $subject->category) === 'general_education' ? 'selected' : '' }}>General Education</option>
                                                    </select>
                                                    @error('category')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="department" class="form-label">Department</label>
                                                    <input type="text" class="form-control @error('department') is-invalid @enderror" 
                                                           id="department" name="department" value="{{ old('department', $subject->department) }}" 
                                                           placeholder="e.g., Computer Science">
                                                    @error('department')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Academic Details -->
                            <div class="col-md-6">
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <h6 class="mb-0">Academic Details</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="credits" class="form-label">Credits <span class="text-danger">*</span></label>
                                                    <input type="number" class="form-control @error('credits') is-invalid @enderror" 
                                                           id="credits" name="credits" value="{{ old('credits', $subject->credits) }}" 
                                                           min="1" max="10" step="0.5" required>
                                                    @error('credits')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="year_level" class="form-label">Year Level</label>
                                                    <select class="form-select @error('year_level') is-invalid @enderror" 
                                                            id="year_level" name="year_level">
                                                        <option value="">Any Year</option>
                                                        <option value="1" {{ old('year_level', $subject->year_level) == '1' ? 'selected' : '' }}>1st Year</option>
                                                        <option value="2" {{ old('year_level', $subject->year_level) == '2' ? 'selected' : '' }}>2nd Year</option>
                                                        <option value="3" {{ old('year_level', $subject->year_level) == '3' ? 'selected' : '' }}>3rd Year</option>
                                                        <option value="4" {{ old('year_level', $subject->year_level) == '4' ? 'selected' : '' }}>4th Year</option>
                                                    </select>
                                                    @error('year_level')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="lecture_hours" class="form-label">Lecture Hours/Week</label>
                                                    <input type="number" class="form-control @error('lecture_hours') is-invalid @enderror" 
                                                           id="lecture_hours" name="lecture_hours" value="{{ old('lecture_hours', $subject->lecture_hours ?? 3) }}" 
                                                           min="0" max="20" step="0.5">
                                                    @error('lecture_hours')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="laboratory_hours" class="form-label">Laboratory Hours/Week</label>
                                                    <input type="number" class="form-control @error('laboratory_hours') is-invalid @enderror" 
                                                           id="laboratory_hours" name="laboratory_hours" value="{{ old('laboratory_hours', $subject->laboratory_hours ?? 0) }}" 
                                                           min="0" max="20" step="0.5">
                                                    @error('laboratory_hours')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label for="capacity" class="form-label">Class Capacity</label>
                                            <input type="number" class="form-control @error('capacity') is-invalid @enderror" 
                                                   id="capacity" name="capacity" value="{{ old('capacity', $subject->capacity) }}" 
                                                   min="1" max="200" placeholder="Leave empty for unlimited">
                                            @error('capacity')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <div class="form-text">Maximum number of students per class</div>
                                        </div>

                                        <div class="mb-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="has_laboratory" 
                                                       name="has_laboratory" value="1" 
                                                       {{ old('has_laboratory', $subject->has_laboratory) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="has_laboratory">
                                                    Has Laboratory Component
                                                </label>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label for="status" class="form-label">Status</label>
                                            <select class="form-select @error('status') is-invalid @enderror" 
                                                    id="status" name="status">
                                                <option value="active" {{ old('status', $subject->status) === 'active' ? 'selected' : '' }}>Active</option>
                                                <option value="inactive" {{ old('status', $subject->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                            </select>
                                            @error('status')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Prerequisites Section -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h6 class="mb-0">Prerequisites</h6>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="prerequisite_search" class="form-label">Search and Add Prerequisites</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="prerequisite_search" 
                                               placeholder="Search subjects by code or name...">
                                        <button type="button" class="btn btn-outline-secondary" id="search_prerequisites">
                                            <i class="bi bi-search"></i> Search
                                        </button>
                                    </div>
                                </div>

                                <div id="prerequisite_results" class="mb-3" style="display: none;">
                                    <label class="form-label">Available Subjects:</label>
                                    <div class="border rounded p-3 bg-light" style="max-height: 200px; overflow-y: auto;">
                                        <div id="prerequisite_list"></div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Selected Prerequisites:</label>
                                    <div id="selected_prerequisites" class="border rounded p-3 min-height-100">
                                        @if($subject->prerequisites && $subject->prerequisites->count() > 0)
                                            @foreach($subject->prerequisites as $prereq)
                                                <span class="selected-prerequisite">
                                                    <strong>{{ $prereq->code }}</strong> - {{ $prereq->name }}
                                                    <button type="button" class="remove-btn" onclick="removePrerequisite('{{ $prereq->id }}')">
                                                        <i class="bi bi-x"></i>
                                                    </button>
                                                </span>
                                            @endforeach
                                        @else
                                            <p class="text-muted mb-0">No prerequisites selected</p>
                                        @endif
                                    </div>
                                </div>

                                <!-- Hidden inputs for selected prerequisites -->
                                <div id="prerequisite_inputs">
                                    @if($subject->prerequisites && $subject->prerequisites->count() > 0)
                                        @foreach($subject->prerequisites as $prereq)
                                            <input type="hidden" name="prerequisites[]" value="{{ $prereq->id }}">
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div class="btn-group">
                                        <a href="{{ route('admin.subjects.show', $subject) }}" class="btn btn-outline-info">
                                            <i class="bi bi-eye"></i> View Subject
                                        </a>
                                        <a href="{{ route('admin.subjects.index') }}" class="btn btn-secondary">
                                            <i class="bi bi-arrow-left"></i> Back to List
                                        </a>
                                    </div>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-check-circle"></i> Update Subject
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

@push('styles')
<style>
.min-height-100 {
    min-height: 100px;
}

.prerequisite-item {
    cursor: pointer;
    transition: background-color 0.2s;
}

.prerequisite-item:hover {
    background-color: #e9ecef;
}

.selected-prerequisite {
    display: inline-flex;
    align-items: center;
    margin: 2px;
    padding: 4px 8px;
    background-color: #e3f2fd;
    border: 1px solid #2196f3;
    border-radius: 4px;
    font-size: 0.875rem;
}

.selected-prerequisite .remove-btn {
    margin-left: 8px;
    background: none;
    border: none;
    color: #f44336;
    cursor: pointer;
    padding: 0;
    font-size: 1rem;
    line-height: 1;
}

.selected-prerequisite .remove-btn:hover {
    color: #d32f2f;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const prerequisiteSearch = document.getElementById('prerequisite_search');
    const searchBtn = document.getElementById('search_prerequisites');
    const resultsDiv = document.getElementById('prerequisite_results');
    const resultsList = document.getElementById('prerequisite_list');
    const selectedDiv = document.getElementById('selected_prerequisites');
    const inputsDiv = document.getElementById('prerequisite_inputs');
    const labHoursInput = document.getElementById('laboratory_hours');
    const hasLabCheckbox = document.getElementById('has_laboratory');
    
    // Initialize with existing prerequisites
    let selectedPrerequisites = [];
    @if($subject->prerequisites && $subject->prerequisites->count() > 0)
        selectedPrerequisites = [
            @foreach($subject->prerequisites as $prereq)
                {
                    id: '{{ $prereq->id }}',
                    code: '{{ $prereq->code }}',
                    name: '{{ addslashes($prereq->name) }}'
                },
            @endforeach
        ];
    @endif
    
    // Auto-check laboratory checkbox when lab hours > 0
    labHoursInput.addEventListener('input', function() {
        hasLabCheckbox.checked = parseFloat(this.value) > 0;
    });
    
    // Search prerequisites
    function searchPrerequisites() {
        const query = prerequisiteSearch.value.trim();
        if (query.length < 2) {
            resultsDiv.style.display = 'none';
            return;
        }
        
        // Simulate AJAX call - replace with actual endpoint
        fetch(`/admin/subjects/search?q=${encodeURIComponent(query)}&exclude={{ $subject->id }}`)
            .then(response => response.json())
            .then(data => {
                displayPrerequisiteResults(data);
            })
            .catch(error => {
                console.error('Error searching prerequisites:', error);
                // Fallback with mock data
                displayPrerequisiteResults([
                    { id: 1, code: 'MATH101', name: 'College Algebra' },
                    { id: 2, code: 'ENG101', name: 'English Composition' },
                    { id: 3, code: 'CS100', name: 'Introduction to Computing' }
                ]);
            });
    }
    
    function displayPrerequisiteResults(subjects) {
        if (subjects.length === 0) {
            resultsList.innerHTML = '<p class="text-muted mb-0">No subjects found</p>';
        } else {
            resultsList.innerHTML = subjects.map(subject => `
                <div class="prerequisite-item p-2 border-bottom" data-id="${subject.id}" data-code="${subject.code}" data-name="${subject.name}">
                    <strong>${subject.code}</strong> - ${subject.name}
                </div>
            `).join('');
            
            // Add click handlers
            resultsList.querySelectorAll('.prerequisite-item').forEach(item => {
                item.addEventListener('click', function() {
                    addPrerequisite({
                        id: this.dataset.id,
                        code: this.dataset.code,
                        name: this.dataset.name
                    });
                });
            });
        }
        resultsDiv.style.display = 'block';
    }
    
    function addPrerequisite(subject) {
        // Check if already selected
        if (selectedPrerequisites.find(p => p.id === subject.id)) {
            return;
        }
        
        selectedPrerequisites.push(subject);
        updateSelectedPrerequisites();
    }
    
    function removePrerequisite(subjectId) {
        selectedPrerequisites = selectedPrerequisites.filter(p => p.id !== subjectId);
        updateSelectedPrerequisites();
    }
    
    function updateSelectedPrerequisites() {
        if (selectedPrerequisites.length === 0) {
            selectedDiv.innerHTML = '<p class="text-muted mb-0">No prerequisites selected</p>';
            inputsDiv.innerHTML = '';
        } else {
            selectedDiv.innerHTML = selectedPrerequisites.map(subject => `
                <span class="selected-prerequisite">
                    <strong>${subject.code}</strong> - ${subject.name}
                    <button type="button" class="remove-btn" onclick="removePrerequisite('${subject.id}')">
                        <i class="bi bi-x"></i>
                    </button>
                </span>
            `).join('');
            
            inputsDiv.innerHTML = selectedPrerequisites.map(subject => 
                `<input type="hidden" name="prerequisites[]" value="${subject.id}">`
            ).join('');
        }
    }
    
    // Make removePrerequisite globally accessible
    window.removePrerequisite = removePrerequisite;
    
    // Event listeners
    searchBtn.addEventListener('click', searchPrerequisites);
    prerequisiteSearch.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            searchPrerequisites();
        }
    });
});
</script>
@endpush
@endsection