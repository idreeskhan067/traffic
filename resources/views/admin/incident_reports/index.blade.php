@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Incident Reports</h1>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <a href="{{ route('incident-reports.create') }}" class="btn btn-primary mb-3">Create New Report</a>

    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>Title</th>
                <th>Location</th>
                <th>Reported By</th>
                <th>Status</th>
                <th>Incident Time</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($reports as $report)
                <tr>
                    <td>{{ $report->title }}</td>
                    <td>{{ $report->location }}</td>
                    <td>{{ $report->reported_by }}</td>
                    <td>{{ ucfirst($report->status) }}</td>
                    <td>{{ $report->incident_time }}</td>
                    <td>
                        <a href="{{ route('incident-reports.show', $report->id) }}" class="btn btn-sm btn-info">View</a>
                        <a href="{{ route('incident-reports.edit', $report->id) }}" class="btn btn-sm btn-warning">Edit</a>
                        <form action="{{ route('incident-reports.destroy', $report->id) }}" method="POST" style="display:inline-block;">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6">No incident reports found.</td></tr>
            @endforelse
        </tbody>
    </table>

    {{ $reports->links() }}
</div>
@endsection
