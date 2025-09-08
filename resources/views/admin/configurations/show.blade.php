@extends('layouts.admin')

@section('title', 'Configuration Details')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0 text-gray-800">Configuration Details</h1>
                <div class="btn-group" role="group">
                    <a href="{{ route('admin.configurations.edit', $configuration) }}" class="btn btn-primary">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                    <a href="{{ route('admin.configurations.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Configurations
                    </a>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-8">
                    <!-- Main Configuration Details -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <code class="text-primary">{{ $configuration->key }}</code>
                            </h5>
                            @if($configuration->group)
                                <small class="text-muted">
                                    Group: <span class="badge bg-secondary">{{ ucfirst($configuration->group) }}</span>
                                </small>
                            @endif
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <strong>Type:</strong>
                                </div>
                                <div class="col-md-9">
                                    <span class="badge bg-info">{{ $configuration->type }}</span>
                                </div>
                            </div>
                            <hr>
                            
                            <div class="row">
                                <div class="col-md-3">
                                    <strong>Value:</strong>
                                </div>
                                <div class="col-md-9">
                                    @if($configuration->is_encrypted)
                                        <div class="alert alert-warning mb-0">
                                            <i class="fas fa-lock"></i> This value is encrypted and cannot be displayed for security reasons.
                                        </div>
                                    @else
                                        <div class="value-display">
                                            @if(is_array($configuration->value) || is_object($configuration->value))
                                                <pre class="bg-light p-3 rounded"><code>{{ json_encode($configuration->value, JSON_PRETTY_PRINT) }}</code></pre>
                                            @elseif(is_bool($configuration->value))
                                                <span class="badge bg-{{ $configuration->value ? 'success' : 'danger' }} fs-6">
                                                    {{ $configuration->value ? 'true' : 'false' }}
                                                </span>
                                            @elseif(is_null($configuration->value))
                                                <span class="text-muted fst-italic">null</span>
                                            @else
                                                <code class="fs-6">{{ $configuration->value }}</code>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </div>
                            
                            @if($configuration->description)
                                <hr>
                                <div class="row">
                                    <div class="col-md-3">
                                        <strong>Description:</strong>
                                    </div>
                                    <div class="col-md-9">
                                        <p class="mb-0">{{ $configuration->description }}</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Usage Examples -->
                    @if(!$configuration->is_encrypted)
                        <div class="card mt-4">
                            <div class="card-header">
                                <h6 class="card-title mb-0">Usage Examples</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <h6>Using helper function</h6>
                                            <pre class="bg-light p-3 rounded"><code>&lt;?php echo dynamic_config('{{ $configuration->key }}', '{{ addslashes($configuration->default_value ?? '') }}'); ?&gt;</code></pre>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <h6>Using Blade directive</h6>
                                            <pre class="bg-light p-3 rounded"><code>{{ '@' }}config('{{ $configuration->key }}', '{{ addslashes($configuration->default_value ?? '') }}')</code></pre>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <h6>Using Configuration model</h6>
                                            <pre class="bg-light p-3 rounded"><code>&lt;?php 
use App\Models\Configuration;

$value = Configuration::get('{{ $configuration->key }}', '{{ addslashes($configuration->default_value ?? '') }}');
echo $value;
?&gt;</code></pre>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <h6>Using Blade conditional</h6>
                                            <pre class="bg-light p-3 rounded"><code>@if(dynamic_config('{{ $configuration->key }}'))
    &#123;&#123; dynamic_config('{{ $configuration->key }}') &#125;&#125;
@endif</code></pre>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
                
                <div class="col-lg-4">
                    <!-- Configuration Properties -->
                    <div class="card">
                        <div class="card-header">
                            <h6 class="card-title mb-0">Properties</h6>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span>Public Access:</span>
                                @if($configuration->is_public)
                                    <span class="badge bg-success">
                                        <i class="fas fa-eye"></i> Public
                                    </span>
                                @else
                                    <span class="badge bg-secondary">
                                        <i class="fas fa-eye-slash"></i> Private
                                    </span>
                                @endif
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span>Encryption:</span>
                                @if($configuration->is_encrypted)
                                    <span class="badge bg-warning">
                                        <i class="fas fa-lock"></i> Encrypted
                                    </span>
                                @else
                                    <span class="badge bg-light text-dark">
                                        <i class="fas fa-unlock"></i> Not Encrypted
                                    </span>
                                @endif
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span>Sort Order:</span>
                                <span class="badge bg-info">{{ $configuration->sort_order }}</span>
                            </div>
                            
                            @if($configuration->validation_rules)
                                <div class="mb-3">
                                    <span class="fw-bold">Validation Rules:</span>
                                    <div class="mt-1">
                                        <code class="small">{{ $configuration->validation_rules }}</code>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Timestamps -->
                    <div class="card mt-4">
                        <div class="card-header">
                            <h6 class="card-title mb-0">Timestamps</h6>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <small class="text-muted">Created:</small><br>
                                <span>{{ $configuration->created_at->format('M d, Y H:i:s') }}</span><br>
                                <small class="text-muted">{{ $configuration->created_at->diffForHumans() }}</small>
                            </div>
                            
                            <div class="mb-0">
                                <small class="text-muted">Last Updated:</small><br>
                                <span>{{ $configuration->updated_at->format('M d, Y H:i:s') }}</span><br>
                                <small class="text-muted">{{ $configuration->updated_at->diffForHumans() }}</small>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Actions -->
                    <div class="card mt-4">
                        <div class="card-header">
                            <h6 class="card-title mb-0">Actions</h6>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <a href="{{ route('admin.configurations.edit', $configuration) }}" class="btn btn-primary btn-sm">
                                    <i class="fas fa-edit"></i> Edit Configuration
                                </a>
                                
                                <form action="{{ route('admin.configurations.clear-cache') }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-warning btn-sm w-100" 
                                            onclick="return confirm('Clear configuration cache?')">
                                        <i class="fas fa-trash"></i> Clear Cache
                                    </button>
                                </form>
                                
                                <hr>
                                
                                <form action="{{ route('admin.configurations.destroy', $configuration) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm w-100" 
                                            onclick="return confirm('Are you sure you want to delete this configuration? This action cannot be undone.')">
                                        <i class="fas fa-trash"></i> Delete Configuration
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.value-display pre {
    max-height: 300px;
    overflow-y: auto;
}

.card-title code {
    font-size: 1.1em;
}

.badge.fs-6 {
    font-size: 1rem !important;
}

pre code {
    font-size: 0.875rem;
}

.btn-sm {
    font-size: 0.875rem;
}
</style>
@endpush