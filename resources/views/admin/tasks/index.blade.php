@extends('layouts.app')

@section('content')
<div class="container-fluid mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Tasks Management</h1>
        <a href="{{ route('admin.tasks.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Create New Task
        </a>
    </div>
    
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">All Tasks</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Title</th>
                            <th>Assigned To</th>
                            <th>Priority</th>
                            <th>Status</th>
                            <th>Due Date</th>
                            <th>Created By</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tasks as $task)
                            <tr>
                                <td>{{ $task->id }}</td>
                                <td>{{ $task->title }}</td>
                                <td>{{ $task->assignee->name ?? 'Unassigned' }}</td>
                                <td>
                                    <span class="badge bg-{{ $task->priority === 'low' ? 'secondary' : ($task->priority === 'medium' ? 'info' : ($task->priority === 'high' ? 'warning' : 'danger')) }}">
                                        {{ ucfirst($task->priority) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $task->status === 'pending' ? 'secondary' : ($task->status === 'in_progress' ? 'primary' : ($task->status === 'completed' ? 'success' : 'danger')) }}">
                                        {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                                    </span>
                                </td>
                                <td>{{ $task->due_date ? $task->due_date->format('d M Y') : 'No deadline' }}</td>
                                <td>{{ $task->creator->name ?? 'System' }}</td>
                                <td>
                                    <a href="{{ route('admin.tasks.show', $task) }}" class="btn btn-sm btn-primary">View</a>
                                    <a href="{{ route('admin.tasks.edit', $task) }}" class="btn btn-sm btn-info">Edit</a>
                                    <form action="{{ route('admin.tasks.destroy', $task) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">No tasks found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                
                {{ $tasks->links() }}
            </div>
        </div>
    </div>
</div>
@endsection