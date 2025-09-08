@extends('layouts.admin')

@section('title', 'Configuration Management')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0 text-gray-800">Configuration Management</h1>
                <div class="btn-group" role="group">
                    <a href="{{ route('admin.configurations.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Add Configuration
                    </a>
                    <form action="{{ route('admin.configurations.clear-cache') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-warning" onclick="return confirm('Are you sure you want to clear the configuration cache?')">
                            <i class="fas fa-trash"></i> Clear Cache
                        </button>
                    </form>
                    <a href="{{ route('admin.configurations.export') }}" class="btn btn-success">
                        <i class="fas fa-download"></i> Export
                    </a>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Filters -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.configurations.index') }}" class="row g-3">
                        <div class="col-md-4">
                            <label for="search" class="form-label">Search</label>
                            <input type="text" class="form-control" id="search" name="search" 
                                   value="{{ request('search') }}" placeholder="Search by key, description, or group...">
                        </div>
                        <div class="col-md-3">
                            <label for="group" class="form-label">Group</label>
                            <select class="form-select" id="group" name="group">
                                <option value="">All Groups</option>
                                @foreach($groups as $group)
                                    <option value="{{ $group }}" {{ request('group') == $group ? 'selected' : '' }}>
                                        {{ ucfirst($group) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">&nbsp;</label>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-outline-primary">
                                    <i class="fas fa-search"></i> Filter
                                </button>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">&nbsp;</label>
                            <div class="d-grid">
                                <a href="{{ route('admin.configurations.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-times"></i> Clear
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Configurations Table -->
            <div class="card">
                <div class="card-body">
                    @if($configurations->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Key</th>
                                        <th>Value</th>
                                        <th>Type</th>
                                        <th>Group</th>
                                        <th>Description</th>
                                        <th>Public</th>
                                        <th>Encrypted</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($configurations as $config)
                                        <tr>
                                            <td>
                                                <code class="text-primary">{{ $config->key }}</code>
                                            </td>
                                            <td>
                                                @if($config->is_encrypted)
                                                    <span class="text-muted"><i class="fas fa-lock"></i> Encrypted</span>
                                                @else
                                                    <div class="text-truncate" style="max-width: 200px;" title="{{ is_array($config->value) || is_object($config->value) ? json_encode($config->value) : $config->value }}">
                                                        @if(is_array($config->value) || is_object($config->value))
                                                            <code>{{ json_encode($config->value) }}</code>
                                                        @elseif(is_bool($config->value))
                                                            <span class="badge bg-{{ $config->value ? 'success' : 'danger' }}">
                                                                {{ $config->value ? 'true' : 'false' }}
                                                            </span>
                                                        @else
                                                            {{ $config->value }}
                                                        @endif
                                                    </div>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-info">{{ $config->type }}</span>
                                            </td>
                                            <td>
                                                @if($config->group)
                                                    <span class="badge bg-secondary">{{ ucfirst($config->group) }}</span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="text-truncate" style="max-width: 250px;" title="{{ $config->description }}">
                                                    {{ $config->description ?: '-' }}
                                                </div>
                                            </td>
                                            <td>
                                                @if($config->is_public)
                                                    <i class="fas fa-eye text-success" title="Public"></i>
                                                @else
                                                    <i class="fas fa-eye-slash text-muted" title="Private"></i>
                                                @endif
                                            </td>
                                            <td>
                                                @if($config->is_encrypted)
                                                    <i class="fas fa-lock text-warning" title="Encrypted"></i>
                                                @else
                                                    <i class="fas fa-unlock text-muted" title="Not Encrypted"></i>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm" role="group">
                                                    <a href="{{ route('admin.configurations.show', $config) }}" 
                                                       class="btn btn-outline-info" title="View">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('admin.configurations.edit', $config) }}" 
                                                       class="btn btn-outline-primary" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <form action="{{ route('admin.configurations.destroy', $config) }}" 
                                                          method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-outline-danger" 
                                                                onclick="return confirm('Are you sure you want to delete this configuration?')" 
                                                                title="Delete">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div class="text-muted">
                                Showing {{ $configurations->firstItem() }} to {{ $configurations->lastItem() }} 
                                of {{ $configurations->total() }} configurations
                            </div>
                            {{ $configurations->appends(request()->query())->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-cogs fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No configurations found</h5>
                            <p class="text-muted">Start by creating your first configuration setting.</p>
                            <a href="{{ route('admin.configurations.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Add Configuration
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.table th {
    border-top: none;
    font-weight: 600;
}

.btn-group-sm .btn {
    padding: 0.25rem 0.5rem;
}

.text-truncate {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
</style>
@endpush