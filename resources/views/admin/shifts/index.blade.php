@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h2 class="mb-4">Shift Management</h2>

    <a href="{{ route('shifts.create') }}" class="btn btn-primary mb-3">+ Create New Shift</a>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>#</th>
                <th>Shift Name</th>
                <th>Start Time</th>
                <th>End Time</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        @forelse ($shifts as $shift)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $shift->name }}</td>
                <td>{{ \Carbon\Carbon::parse($shift->start_time)->format('h:i A') }}</td>
                <td>{{ \Carbon\Carbon::parse($shift->end_time)->format('h:i A') }}</td>
                <td>
                    <a href="{{ route('shifts.edit', $shift->id) }}" class="btn btn-sm btn-warning">Edit</a>
                    <form action="{{ route('shifts.destroy', $shift->id) }}" method="POST" style="display:inline-block;">
                        @csrf @method('DELETE')
                        <button onclick="return confirm('Are you sure?')" class="btn btn-sm btn-danger">Delete</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td colspan="5">No shifts found.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
@endsection
