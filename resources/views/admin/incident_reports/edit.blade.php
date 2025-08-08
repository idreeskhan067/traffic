@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Edit Incident Report</h1>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    <form action="{{ route('incident-reports.update', $incidentReport->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label class="form-label">Title</label>
            <input type="text" name="title" class="form-control" value="{{ $incidentReport->title }}" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-control" rows="4" required>{{ $incidentReport->description }}</textarea>
        </div>
        <div class="mb-3">
            <label class="form-label">Location</label>
            <input type="text" name="location" class="form-control" value="{{ $incidentReport->location }}" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Reported By</label>
            <input type="text" name="reported_by" class="form-control" value="{{ $incidentReport->reported_by }}" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Status</label>
            <select name="status" class="form-select" required>
                @foreach ($statuses as $status)
                    <option value="{{ $status }}" {{ $incidentReport->status === $status ? 'selected' : '' }}>
                        {{ ucfirst(str_replace('_', ' ', $status)) }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Incident Time</label>
            <input type="datetime-local" name="incident_time" class="form-control"
                value="{{ \Carbon\Carbon::parse($incidentReport->incident_time)->format('Y-m-d\TH:i') }}" required>
        </div>
        <button class="btn btn-primary">Update Report</button>
        <a href="{{ route('incident-reports.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection
