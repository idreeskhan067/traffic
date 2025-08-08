@extends('layouts.app')

@section('content')
<div class="container-fluid">

    {{-- Top Tiles --}}
    <div class="row">
        <div class="col-md-3">
            <div class="card text-white bg-primary mb-3">
                <div class="card-header">On-Duty Wardens</div>
                <div class="card-body">
                    <h5 class="card-title">{{ $onDutyWardens }}</h5>
                    <p class="card-text">Currently active in the field.</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-danger mb-3">
                <div class="card-header">Reported Congestions</div>
                <div class="card-body">
                    <h5 class="card-title">{{ $reportedCongestions }}</h5>
                    <p class="card-text">Areas need immediate response.</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-warning mb-3">
                <div class="card-header">Pending Dispatches</div>
                <div class="card-body">
                    <h5 class="card-title">{{ $pendingDispatches }}</h5>
                    <p class="card-text">Teams awaiting deployment.</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-success mb-3">
                <div class="card-header">Total Squads</div>
                <div class="card-body">
                    <h5 class="card-title">{{ $totalSquads }}</h5>
                    <p class="card-text">Deployed across city zones.</p>
                </div>
            </div>
        </div>
    </div>

{{-- Charts & Map --}}
<div class="row">
    <!-- Recent Congestion Reports -->
    <div class="col-md-6 mb-4">
        <div class="card shadow h-100">
            <div class="card-header bg-primary text-white">
                Recent Congestion Reports
            </div>
            <div class="card-body p-0" style="height: 300px; overflow-y: auto;">
                <ul class="list-group list-group-flush">
                    @php
                        $statusLabels = [
                            'active' => 'Ongoing',
                            'reported' => 'Reported',
                            'cleared' => 'Cleared',
                            'blocked' => 'Blocked',
                        ];
                    @endphp
                    @forelse ($recentCongestions as $congestion)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <strong>{{ $congestion->location }}</strong>
                                <small class="text-muted">({{ $congestion->zone ?? 'Unknown zone' }})</small>
                            </div>
                            <span class="badge 
                                @if($congestion->status == 'reported') bg-warning
                                @elseif($congestion->status == 'cleared') bg-success
                                @elseif($congestion->status == 'blocked') bg-danger
                                @elseif($congestion->status == 'active') bg-primary
                                @else bg-secondary @endif">
                                {{ $statusLabels[$congestion->status] ?? ucfirst($congestion->status) }}
                            </span>
                        </li>
                    @empty
                        <li class="list-group-item text-muted">No congestion reports found.</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>

    <!-- Live Map (Leaflet) -->
    <div class="col-md-6 mb-4">
        <div class="card shadow h-100">
            <div class="card-header bg-dark text-white">
                Live Map (Warden Locations)
            </div>
            <div class="card-body p-0" style="height: 300px;">
                <div id="wardenMap" style="height: 100%; width: 100%;"></div>
            </div>
        </div>
    </div>
</div>


    {{-- Emergency Notifications --}}
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card mb-3">
                <div class="card-header bg-danger text-white">Emergency Alerts</div>
                <div class="card-body">
                    @if($recentEmergencies->isEmpty())
                        <p class="text-muted">No emergency alerts at the moment.</p>
                    @else
                        <ul class="list-group">
                            @foreach($recentEmergencies as $alert)
                                <li class="list-group-item 
                                    @if(str_contains(strtolower($alert->title), 'fire') || str_contains(strtolower($alert->title), 'accident') || str_contains(strtolower($alert->title), 'road block')) list-group-item-danger
                                    @elseif(str_contains(strtolower($alert->title), 'protest') || str_contains(strtolower($alert->title), 'hazard')) list-group-item-warning
                                    @else list-group-item-secondary
                                    @endif
                                ">
                                    {{ $alert->title }}
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Force Dispatch & Attendance --}}
    <div class="row">
        <div class="col-md-6">
            <div class="card mb-3">
                <div class="card-header">Force Dispatch Panel</div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success mb-3">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger mb-3">
                            <ul class="mb-0 pl-3">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('admin.dispatch.squad') }}">
                        @csrf
                        <div class="form-group">
                            <label for="squad_id">Select Squad</label>
                            <select class="form-control" name="squad_id" id="squad_id" required>
                                @foreach($availableSquads as $squad)
                                    <option value="{{ $squad->id }}">{{ $squad->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group mt-3">
                            <label for="zone">Assign Zone</label>
                            <input type="text" class="form-control" name="zone" id="zone" placeholder="e.g. Zone 4, Canal Rd" required>
                        </div>

                        <button type="submit" class="btn btn-primary mt-3 w-100">Dispatch</button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Live Attendance Feed --}}
        <div class="col-md-6">
            <div class="card mb-3">
                <div class="card-header">Attendance Feed (Today)</div>
                <div class="card-body">
                    <ul class="list-group">
                        @forelse($latestAttendances as $attendance)
                            <li class="list-group-item">
                                👮 {{ $attendance->user->name ?? 'Warden' }} checked in at {{ \Carbon\Carbon::parse($attendance->check_in)->format('g:i A') }}
                            </li>
                        @empty
                            <li class="list-group-item text-muted">No check-ins yet today.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>

    {{-- Shift Summary & Recent Activities --}}
    <div class="row">
        <div class="col-md-6">
            <div class="card mb-3">
                <div class="card-header">Shift Summary</div>
                <div class="card-body">
                    @foreach ($shifts as $shift)
                        <p>
                            <strong>{{ $shift->name }}:</strong>
                            {{ $shift->formatted_start_time }} - {{ $shift->formatted_end_time }}
                        </p>
                    @endforeach

                    @if ($shifts->isNotEmpty())
                        <p class="mt-3">
                            <strong>Next Dispatch Window:</strong>
                            in {{ \Carbon\Carbon::parse($shifts->first()->start_time)->diffForHumans(null, true) }}
                        </p>
                    @else
                        <p>No shifts configured.</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card mb-3">
                <div class="card-header">Recent Activities</div>
                <div class="card-body">
                    @if($recentActivities->isNotEmpty())
                        <ul class="list-unstyled">
                            @foreach($recentActivities as $log)
                                <li class="mb-2">
                                    <strong>{{ $log->action }}</strong> by 
                                    {{ $log->performed_by ?? 'System' }} 
                                    on {{ $log->created_at->format('d M, H:i') }} — 
                                    {{ $log->description ?? $log->target }}
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p>No recent activity recorded.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<!-- Leaflet Map CSS/JS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const map = L.map('wardenMap').setView([33.6844, 73.0479], 13);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors',
            maxZoom: 19
        }).addTo(map);

        L.marker([33.6844, 73.0479]).addTo(map)
            .bindPopup("Traffic Warden - Alpha Squad")
            .openPopup();

        setTimeout(() => map.invalidateSize(), 500);
    });
</script>

<!-- Chart.js Script -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const ctx = document.getElementById('trafficChart');
        if (ctx) {
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: {!! json_encode($trafficData->keys()) !!},
                    datasets: [{
                        label: 'Reported Congestions',
                        data: {!! json_encode($trafficData->values()) !!},
                        backgroundColor: ['#007bff', '#dc3545', '#ffc107', '#28a745'],
                    }]
                },
                options: {
                    scales: {
                        y: { beginAtZero: true }
                    }
                }
            });
        }
    });
</script>
@endpush
