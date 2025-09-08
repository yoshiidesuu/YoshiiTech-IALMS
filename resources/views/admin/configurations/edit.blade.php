@extends('layouts.admin')

@section('title', 'Edit Configuration')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0 text-gray-800">Edit Configuration</h1>
                <div class="btn-group" role="group">
                    <a href="{{ route('admin.configurations.show', $configuration) }}" class="btn btn-info">
                        <i class="fas fa-eye"></i> View
                    </a>
                    <a href="{{ route('admin.configurations.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Configurations
                    </a>
                </div>
            </div>

            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Configuration Details</h5>
                            <small class="text-muted">Key: <code>{{ $configuration->key }}</code></small>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('admin.configurations.update', $configuration) }}" method="POST">
                                @csrf
                                @method('PUT')
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="key" class="form-label">Key <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('key') is-invalid @enderror" 
                                                   id="key" name="key" value="{{ old('key', $configuration->key) }}" 
                                                   placeholder="e.g., app.name, mail.driver" required>
                                            @error('key')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <div class="form-text">Unique identifier for this configuration (use dot notation)</div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="type" class="form-label">Type <span class="text-danger">*</span></label>
                                            <select class="form-select @error('type') is-invalid @enderror" 
                                                    id="type" name="type" required>
                                                <option value="">Select Type</option>
                                                <option value="string" {{ old('type', $configuration->type) == 'string' ? 'selected' : '' }}>String</option>
                                                <option value="integer" {{ old('type', $configuration->type) == 'integer' ? 'selected' : '' }}>Integer</option>
                                                <option value="float" {{ old('type', $configuration->type) == 'float' ? 'selected' : '' }}>Float</option>
                                                <option value="boolean" {{ old('type', $configuration->type) == 'boolean' ? 'selected' : '' }}>Boolean</option>
                                                <option value="json" {{ old('type', $configuration->type) == 'json' ? 'selected' : '' }}>JSON</option>
                                                <option value="array" {{ old('type', $configuration->type) == 'array' ? 'selected' : '' }}>Array</option>
                                            </select>
                                            @error('type')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="value" class="form-label">Value</label>
                                    <div id="value-input-container">
                                        <!-- Dynamic input will be inserted here -->
                                    </div>
                                    @error('value')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text" id="value-help">Enter the configuration value</div>
                                    
                                    @if($configuration->is_encrypted)
                                        <div class="alert alert-warning mt-2">
                                            <i class="fas fa-lock"></i> This value is encrypted. Leave empty to keep current value, or enter new value to update.
                                        </div>
                                    @endif
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="group" class="form-label">Group</label>
                                            <input type="text" class="form-control @error('group') is-invalid @enderror" 
                                                   id="group" name="group" value="{{ old('group', $configuration->group) }}" 
                                                   placeholder="e.g., application, mail, system" 
                                                   list="existing-groups">
                                            <datalist id="existing-groups">
                                                @foreach($groups as $group)
                                                    <option value="{{ $group }}">{{ ucfirst($group) }}</option>
                                                @endforeach
                                            </datalist>
                                            @error('group')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <div class="form-text">Optional grouping for organization</div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="sort_order" class="form-label">Sort Order</label>
                                            <input type="number" class="form-control @error('sort_order') is-invalid @enderror" 
                                                   id="sort_order" name="sort_order" value="{{ old('sort_order', $configuration->sort_order) }}" 
                                                   min="0" step="1">
                                            @error('sort_order')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <div class="form-text">Display order (0 = first)</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea class="form-control tinymce-editor @error('description') is-invalid @enderror" 
                                              id="description" name="description" rows="3" 
                                              placeholder="Describe what this configuration controls...">{{ old('description', $configuration->description) }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" 
                                                       id="is_public" name="is_public" value="1" 
                                                       {{ old('is_public', $configuration->is_public) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="is_public">
                                                    Public Configuration
                                                </label>
                                            </div>
                                            <div class="form-text">Allow access from frontend/public areas</div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" 
                                                       id="is_encrypted" name="is_encrypted" value="1" 
                                                       {{ old('is_encrypted', $configuration->is_encrypted) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="is_encrypted">
                                                    Encrypt Value
                                                </label>
                                            </div>
                                            <div class="form-text">Store value encrypted in database</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('admin.configurations.index') }}" class="btn btn-secondary">
                                        Cancel
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Update Configuration
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                    
                    <!-- Configuration History/Info -->
                    <div class="card mt-4">
                        <div class="card-header">
                            <h6 class="card-title mb-0">Configuration Information</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <small class="text-muted">Created:</small><br>
                                    <span>{{ $configuration->created_at->format('M d, Y H:i:s') }}</span>
                                </div>
                                <div class="col-md-6">
                                    <small class="text-muted">Last Updated:</small><br>
                                    <span>{{ $configuration->updated_at->format('M d, Y H:i:s') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const typeSelect = document.getElementById('type');
    const valueContainer = document.getElementById('value-input-container');
    const valueHelp = document.getElementById('value-help');
    const configuration = @json($configuration);
    
    function updateValueInput() {
        const type = typeSelect.value;
        let inputHtml = '';
        let helpText = 'Enter the configuration value';
        let currentValue = '';
        
        // Get current value based on type and encryption status
        if (!configuration.is_encrypted) {
            if (type === 'json' || type === 'array') {
                currentValue = typeof configuration.value === 'object' 
                    ? JSON.stringify(configuration.value, null, 2)
                    : configuration.value;
            } else if (type === 'boolean') {
                currentValue = configuration.value ? '1' : '0';
            } else {
                currentValue = configuration.value;
            }
        }
        
        switch(type) {
            case 'string':
                inputHtml = `<input type="text" class="form-control" name="value" value="${currentValue}" placeholder="Enter string value">`;
                helpText = 'Enter a text string value';
                break;
                
            case 'integer':
                inputHtml = `<input type="number" class="form-control" name="value" value="${currentValue}" step="1" placeholder="Enter integer value">`;
                helpText = 'Enter a whole number (e.g., 42, -10)';
                break;
                
            case 'float':
                inputHtml = `<input type="number" class="form-control" name="value" value="${currentValue}" step="any" placeholder="Enter decimal value">`;
                helpText = 'Enter a decimal number (e.g., 3.14, -2.5)';
                break;
                
            case 'boolean':
                inputHtml = `
                    <select class="form-select" name="value">
                        <option value="">Select boolean value</option>
                        <option value="1" ${currentValue == '1' ? 'selected' : ''}>True</option>
                        <option value="0" ${currentValue == '0' ? 'selected' : ''}>False</option>
                    </select>
                `;
                helpText = 'Select true or false';
                break;
                
            case 'json':
            case 'array':
                inputHtml = `<textarea class="form-control" name="value" rows="6" placeholder='Enter JSON object or array'>${currentValue}</textarea>`;
                helpText = 'Enter valid JSON (e.g., {"key": "value"} or ["item1", "item2"])';
                break;
                
            default:
                inputHtml = `<input type="text" class="form-control" name="value" value="${currentValue}" placeholder="Enter value">`;
                break;
        }
        
        valueContainer.innerHTML = inputHtml;
        valueHelp.textContent = helpText;
    }
    
    // Initialize on page load
    updateValueInput();
    
    // Update when type changes
    typeSelect.addEventListener('change', updateValueInput);
});
</script>
@endpush