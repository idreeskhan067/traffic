@extends('layouts.app')

@section('content')
<div class="container-fluid mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Area Assignments</h1>
        <a href="{{ route('admin.areas.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Assign New Area
        </a>
    </div>
    
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Assigned Areas</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Area Name</th>
                            <th>Assigned To</th>
                            <th>Status</th>
                            <th>Assigned By</th>
                            <th>Assigned At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($areas as $area)
                            <tr>
                                <td>{{ $area->id }}</td>
                                <td>{{ $area->name }}</td>
                                <td>{{ $area->warden->name ?? 'Unknown' }}</td>
                                <td>
                                    <span class="badge bg-{{ $area->status === 'active' ? 'success' : 'secondary' }}">
                                        {{ ucfirst($area->status) }}
                                    </span>
                                </td>
                                <td>{{ $area->assigner->name ?? 'System' }}</td>
                                <td>{{ $area->assigned_at->format('d M Y, h:i A') }}</td>
                                <td>
                                    <a href="{{ route('admin.areas.edit', $area) }}" class="btn btn-sm btn-info">Edit</a>
                                    <form action="{{ route('admin.areas.destroy', $area) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">No assigned areas found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                
                {{ $areas->links() }}
            </div>
        </div>
    </div>
</div>
@endsection