@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Incident Report Details</h1>

    <div class="card">
        <div class="card-body">
            <h4>{{ $incidentReport->title }}</h4>
            <p><strong>Description:</strong><br>{{ $incidentReport->description }}</p>
            <p><strong>Location:</strong> {{ $incidentReport->location }}</p>
            <p><strong>Reported By:</strong> {{ $incidentReport->reported_by }}</p>
            <p><strong>Status:</strong> {{ ucfirst($incidentReport->status) }}</p>
            <p><strong>Incident Time:</strong> {{ $incidentReport->incident_time }}</p>
        </div>
    </div>

    <a href="{{ route('incident-reports.index') }}" class="btn btn-secondary mt-3">Back to Reports</a>
</div>
@endsection
