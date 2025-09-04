@extends('layouts.app')

@section('content')
<div class="container-fluid mt-4">
    <!-- Real-time Status Indicator -->
    <div class="row mb-2">
        <div class="col-12">
            <div class="alert alert-info d-flex justify-content-between align-items-center" id="realtime-status">
                <span>📡 <strong>Real-time Dashboard</strong> - Updates every 30 seconds</span>
                <span class="badge bg-success" id="last-updated">Connected</span>
            </div>
        </div>
    </div>

    {{-- Top Tiles --}}
    <div class="row">
        <div class="col-md-2">
            <div class="card text-white bg-primary mb-3">
                <div class="card-header">On-Duty Wardens</div>
                <div class="card-body">
                    <h5 class="card-title" id="on-duty-wardens">{{ $onDutyWardens }}</h5>
                    <p class="card-text">Currently active in the field.</p>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card text-white bg-danger mb-3">
                <div class="card-header">Reported Congestions</div>
                <div class="card-body">
                    <h5 class="card-title" id="reported-congestions">{{ $reportedCongestions }}</h5>
                    <p class="card-text">Areas need immediate response.</p>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card text-white bg-warning mb-3">
                <div class="card-header">Pending Dispatches</div>
                <div class="card-body">
                    <h5 class="card-title" id="pending-dispatches">{{ $pendingDispatches }}</h5>
                    <p class="card-text">Teams awaiting deployment.</p>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card text-white bg-success mb-3">
                <div class="card-header">Total Squads</div>
                <div class="card-body">
                    <h5 class="card-title" id="total-squads">{{ $totalSquads }}</h5>
                    <p class="card-text">Deployed across city zones.</p>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card text-white bg-secondary mb-3">
                <div class="card-header">Resolved Congestions</div>
                <div class="card-body">
                    <h5 class="card-title" id="resolved-congestions">{{ $resolvedCongestions ?? 0 }}</h5>
                    <p class="card-text">Cleared or redirected areas.</p>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card text-white bg-dark mb-3">
                <div class="card-header">Active Emergencies</div>
                <div class="card-body">
                    <h5 class="card-title" id="emergency-count">{{ $emergencyCount ?? 0 }}</h5>
                    <p class="card-text">Critical alerts needing response.</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Charts & Map --}}
    <div class="row">
        <!-- Recent Congestion Reports -->
        <div class="col-md-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <span>Recent Congestion Reports</span>
                    <span class="badge bg-light text-dark" id="congestion-count">{{ $recentCongestions->count() }}</span>
                </div>
                <div class="card-body p-0" style="height: 300px; overflow-y: auto;">
                    <ul class="list-group list-group-flush" id="congestion-list">
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
                <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                    <span>Live Map (Warden Locations)</span>
                    <span class="badge bg-light text-dark" id="warden-count">{{ $wardensWithLocation->count() }}</span>
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
                <div class="card-header bg-danger text-white d-flex justify-content-between align-items-center">
                    <span>Emergency Alerts</span>
                    <span class="badge bg-light text-dark" id="emergency-alerts-count">{{ $recentEmergencies->count() }}</span>
                </div>
                <div class="card-body" id="emergency-alerts-container">
                    @if($recentEmergencies->isEmpty())
                        <p class="text-muted">No emergency alerts at the moment.</p>
                    @else
                        <ul class="list-group" id="emergency-alerts-list">
                            @foreach($recentEmergencies as $alert)
                                @php
                                    $titleLower = strtolower($alert->title);
                                    if (str_contains($titleLower, 'fire') || str_contains($titleLower, 'accident') || str_contains($titleLower, 'road block')) {
                                        $itemClass = 'list-group-item-danger';
                                    } elseif (str_contains($titleLower, 'protest') || str_contains($titleLower, 'hazard')) {
                                        $itemClass = 'list-group-item-warning';
                                    } else {
                                        $itemClass = 'list-group-item-secondary';
                                    }
                                @endphp
                                <li class="list-group-item {{ $itemClass }}">
                                    {{ $alert->title }}
                                    <small class="text-muted d-block">{{ $alert->created_at->diffForHumans() }}</small>
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
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>Attendance Feed (Today)</span>
                    <span class="badge bg-primary" id="attendance-count">{{ $latestAttendances->count() }}</span>
                </div>
                <div class="card-body">
                    <ul class="list-group" id="attendance-feed">
                        @forelse($latestAttendances as $attendance)
                            <li class="list-group-item">
                                👮 {{ $attendance['user']['name'] ?? 'Warden' }}
                                @if($attendance['status'] === 'in')
                                    checked in at {{ $attendance['check_in_time'] ? \Carbon\Carbon::parse($attendance['check_in_time'])->format('g:i A') : 'Time not recorded' }}
                                @elseif($attendance['status'] === 'out')
                                    checked out at {{ $attendance['check_out_time'] ? \Carbon\Carbon::parse($attendance['check_out_time'])->format('g:i A') : 'Time not recorded' }}
                                @else
                                    ❌ Not checked in yet
                                @endif
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
                <div class="card-body" id="shifts-container">
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
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>Recent Activities</span>
                    <span class="badge bg-info" id="activities-count">{{ $recentActivities->count() }}</span>
                </div>
                <div class="card-body" id="activities-container">
                    @if($recentActivities->isNotEmpty())
                        <ul class="list-unstyled" id="activities-list">
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

<!-- Real-time Dashboard Script -->
<script>
let wardenMap;
let wardenMarkers = [];

document.addEventListener('DOMContentLoaded', function () {
    // Initialize map
    initializeMap();
    
    // Start real-time updates
    startRealTimeUpdates();
});

function initializeMap() {
    console.log('Wardens with location data:', @json($wardensWithLocation));
    
    wardenMap = L.map('wardenMap').setView([33.6844, 73.0479], 13);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors',
        maxZoom: 19
    }).addTo(wardenMap);

    // Add initial markers
    updateWardenMarkers(@json($wardensWithLocation));
    
    setTimeout(() => wardenMap.invalidateSize(), 500);
}

function updateWardenMarkers(wardens) {
    // Clear existing markers
    wardenMarkers.forEach(marker => wardenMap.removeLayer(marker));
    wardenMarkers = [];
    
    // Add new markers
    wardens.forEach(warden => {
        if (warden.location) {
            console.log('Adding marker for warden:', warden.name, 'at', warden.location.latitude, warden.location.longitude);
            const marker = L.marker([warden.location.latitude, warden.location.longitude])
                .addTo(wardenMap)
                .bindPopup(`${warden.name} (${warden.status})`);
            wardenMarkers.push(marker);
        }
    });
    
    // Update counter
    document.getElementById('warden-count').textContent = wardens.length;
    console.log('Total markers added:', wardenMarkers.length);
}

function startRealTimeUpdates() {
    // Update every 30 seconds
    setInterval(fetchRealTimeData, 30000);
    
    // Initial fetch after 5 seconds
    setTimeout(fetchRealTimeData, 5000);
}

async function fetchRealTimeData() {
    try {
        const response = await fetch('{{ route("admin.dashboard.realtime") }}', {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });
        
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}`);
        }
        
        const result = await response.json();
        
        if (result.success) {
            updateDashboard(result.data);
            updateLastUpdated();
        } else {
            console.error('Failed to fetch real-time data:', result.message);
            updateConnectionStatus(false);
        }
        
    } catch (error) {
        console.error('Real-time update error:', error);
        updateConnectionStatus(false);
    }
}

function updateDashboard(data) {
    // Update top tiles
    document.getElementById('on-duty-wardens').textContent = data.onDutyWardens || 0;
    document.getElementById('reported-congestions').textContent = data.reportedCongestions || 0;
    document.getElementById('pending-dispatches').textContent = data.pendingDispatches || 0;
    document.getElementById('total-squads').textContent = data.totalSquads || 0;
    document.getElementById('resolved-congestions').textContent = data.resolvedCongestions || 0;
    document.getElementById('emergency-count').textContent = data.emergencyCount || 0;
    
    // Update counters
    document.getElementById('congestion-count').textContent = data.recentCongestions?.length || 0;
    document.getElementById('emergency-alerts-count').textContent = data.recentEmergencies?.length || 0;
    document.getElementById('attendance-count').textContent = data.latestAttendances?.length || 0;
    document.getElementById('activities-count').textContent = data.recentActivities?.length || 0;
    
    // Update warden locations on map
    if (data.wardensWithLocation && wardenMap) {
        updateWardenMarkers(data.wardensWithLocation);
    }
    
    // Update congestion list
    if (data.recentCongestions) {
        updateCongestionList(data.recentCongestions);
    }
    
    // Update emergency alerts
    if (data.recentEmergencies) {
        updateEmergencyAlerts(data.recentEmergencies);
    }
    
    // Update attendance feed
    if (data.latestAttendances) {
        updateAttendanceFeed(data.latestAttendances);
    }
    
    // Update activities
    if (data.recentActivities) {
        updateActivitiesList(data.recentActivities);
    }
}

function updateCongestionList(congestions) {
    const listElement = document.getElementById('congestion-list');
    if (!listElement) return;
    
    const statusLabels = {
        'active': 'Ongoing',
        'reported': 'Reported',
        'cleared': 'Cleared',
        'blocked': 'Blocked'
    };
    
    const statusClasses = {
        'reported': 'bg-warning',
        'cleared': 'bg-success',
        'blocked': 'bg-danger',
        'active': 'bg-primary'
    };
    
    if (congestions.length === 0) {
        listElement.innerHTML = '<li class="list-group-item text-muted">No congestion reports found.</li>';
        return;
    }
    
    const html = congestions.map(congestion => `
        <li class="list-group-item d-flex justify-content-between align-items-center">
            <div>
                <strong>${congestion.location}</strong>
                <small class="text-muted">(${congestion.zone || 'Unknown zone'})</small>
            </div>
            <span class="badge ${statusClasses[congestion.status] || 'bg-secondary'}">
                ${statusLabels[congestion.status] || congestion.status}
            </span>
        </li>
    `).join('');
    
    listElement.innerHTML = html;
}

function updateEmergencyAlerts(emergencies) {
    const container = document.getElementById('emergency-alerts-container');
    if (!container) return;
    
    if (emergencies.length === 0) {
        container.innerHTML = '<p class="text-muted">No emergency alerts at the moment.</p>';
        return;
    }
    
    const html = `
        <ul class="list-group" id="emergency-alerts-list">
            ${emergencies.map(alert => {
                const titleLower = alert.title.toLowerCase();
                let itemClass = 'list-group-item-secondary';
                
                if (titleLower.includes('fire') || titleLower.includes('accident') || titleLower.includes('road block')) {
                    itemClass = 'list-group-item-danger';
                } else if (titleLower.includes('protest') || titleLower.includes('hazard')) {
                    itemClass = 'list-group-item-warning';
                }
                
                return `
                    <li class="list-group-item ${itemClass}">
                        ${alert.title}
                        <small class="text-muted d-block">${formatTimeAgo(alert.created_at)}</small>
                    </li>
                `;
            }).join('')}
        </ul>
    `;
    
    container.innerHTML = html;
}

function updateAttendanceFeed(attendances) {
    const feedElement = document.getElementById('attendance-feed');
    if (!feedElement) return;
    
    if (attendances.length === 0) {
        feedElement.innerHTML = '<li class="list-group-item text-muted">No check-ins yet today.</li>';
        return;
    }
    
    const html = attendances.map(attendance => {
        const userName = attendance.user?.name || 'Warden';
        let statusText = '❌ Not checked in yet';
        
        if (attendance.status === 'in' && attendance.check_in_time) {
            const time = new Date(attendance.check_in_time).toLocaleTimeString('en-US', {
                hour: 'numeric', 
                minute: '2-digit', 
                hour12: true
            });
            statusText = `checked in at ${time}`;
        } else if (attendance.status === 'out' && attendance.check_out_time) {
            const time = new Date(attendance.check_out_time).toLocaleTimeString('en-US', {
                hour: 'numeric', 
                minute: '2-digit', 
                hour12: true
            });
            statusText = `checked out at ${time}`;
        }
        
        return `
            <li class="list-group-item">
                👮 ${userName} ${statusText}
            </li>
        `;
    }).join('');
    
    feedElement.innerHTML = html;
}

function updateActivitiesList(activities) {
    const container = document.getElementById('activities-container');
    if (!container) return;
    
    if (activities.length === 0) {
        container.innerHTML = '<p>No recent activity recorded.</p>';
        return;
    }
    
    const html = `
        <ul class="list-unstyled" id="activities-list">
            ${activities.map(log => `
                <li class="mb-2">
                    <strong>${log.action}</strong> by 
                    ${log.performed_by || 'System'} 
                    on ${formatDate(log.created_at)} — 
                    ${log.description || log.target || 'No description'}
                </li>
            `).join('')}
        </ul>
    `;
    
    container.innerHTML = html;
}

function updateLastUpdated() {
    const badge = document.getElementById('last-updated');
    badge.textContent = `Updated ${new Date().toLocaleTimeString()}`;
    badge.className = 'badge bg-success';
    updateConnectionStatus(true);
}

function updateConnectionStatus(connected) {
    const statusElement = document.getElementById('realtime-status');
    const badge = document.getElementById('last-updated');
    
    if (connected) {
        statusElement.className = 'alert alert-info d-flex justify-content-between align-items-center';
        badge.className = 'badge bg-success';
    } else {
        statusElement.className = 'alert alert-warning d-flex justify-content-between align-items-center';
        badge.textContent = 'Connection Error';
        badge.className = 'badge bg-danger';
    }
}

function formatTimeAgo(dateString) {
    const date = new Date(dateString);
    const now = new Date();
    const diffInMinutes = Math.floor((now - date) / (1000 * 60));
    
    if (diffInMinutes < 1) return 'Just now';
    if (diffInMinutes < 60) return `${diffInMinutes}m ago`;
    if (diffInMinutes < 1440) return `${Math.floor(diffInMinutes / 60)}h ago`;
    return `${Math.floor(diffInMinutes / 1440)}d ago`;
}

function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', {
        day: 'numeric',
        month: 'short',
        hour: 'numeric',
        minute: '2-digit',
        hour12: false
    });
}
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