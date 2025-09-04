@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-cog me-2"></i>System Settings
        </h1>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#quickHelpModal">
            <i class="fas fa-question-circle me-1"></i> Help
        </button>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <form method="POST" action="{{ route('settings.update') }}">
        @csrf
        @method('PUT')
        
        <div class="row">
            <!-- Left Navigation -->
            <div class="col-md-3 mb-4">
                <div class="card shadow">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-bars me-1"></i>Settings
                        </h6>
                    </div>
                    <div class="list-group list-group-flush" id="settingsNav">
                        <a class="list-group-item list-group-item-action active" href="#trafficSettings" data-bs-toggle="tab">
                            <i class="fas fa-traffic-light me-2"></i>Traffic Control
                        </a>
                        <a class="list-group-item list-group-item-action" href="#dispatchSettings" data-bs-toggle="tab">
                            <i class="fas fa-truck-fast me-2"></i>Dispatch Config
                        </a>
                        <a class="list-group-item list-group-item-action" href="#mapSettings" data-bs-toggle="tab">
                            <i class="fas fa-map-location-dot me-2"></i>Map & Tracking
                        </a>
                    </div>
                </div>
            </div>

            <!-- Right Content -->
            <div class="col-md-9">
                <div class="tab-content" id="settingsContent">
                    <!-- Traffic Settings Tab -->
                    <div class="tab-pane fade show active" id="trafficSettings">
                        <div class="card shadow mb-4">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">
                                    <i class="fas fa-traffic-light me-2"></i>Traffic Control Parameters
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Congestion Threshold (veh/km)</label>
                                        <div class="input-group">
                                            <input type="number" class="form-control" name="settings[congestion_threshold]" value="{{ $settings['congestion_threshold'] ?? '50' }}">
                                            <span class="input-group-text">veh/km</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Severe Congestion Level</label>
                                        <div class="input-group">
                                            <input type="number" class="form-control" name="settings[severe_congestion]" value="{{ $settings['severe_congestion'] ?? '100' }}">
                                            <span class="input-group-text">veh/km</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Auto Alert Generation</label>
                                        <select class="form-select" name="settings[auto_alerts]">
                                            <option value="1" {{ ($settings['auto_alerts'] ?? '1') == '1' ? 'selected' : '' }}>Enabled</option>
                                            <option value="0" {{ ($settings['auto_alerts'] ?? '1') == '0' ? 'selected' : '' }}>Disabled</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Dispatch Settings Tab -->
                    <div class="tab-pane fade" id="dispatchSettings">
                        <div class="card shadow mb-4">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">
                                    <i class="fas fa-truck-fast me-2"></i>Dispatch Configuration
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Response Time Target</label>
                                        <div class="input-group">
                                            <input type="number" class="form-control" name="settings[response_time_target]" value="{{ $settings['response_time_target'] ?? '15' }}">
                                            <span class="input-group-text">minutes</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Max Units Per Incident</label>
                                        <input type="number" class="form-control" name="settings[max_units_per_incident]" value="{{ $settings['max_units_per_incident'] ?? '3' }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Auto Dispatch</label>
                                        <select class="form-select" name="settings[auto_dispatch]">
                                            <option value="1" {{ ($settings['auto_dispatch'] ?? '1') == '1' ? 'selected' : '' }}>Enabled</option>
                                            <option value="0" {{ ($settings['auto_dispatch'] ?? '1') == '0' ? 'selected' : '' }}>Disabled</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Map Settings Tab -->
                    <div class="tab-pane fade" id="mapSettings">
                        <div class="card shadow mb-4">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">
                                    <i class="fas fa-map-location-dot me-2"></i>Map & Tracking
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Map Refresh Rate</label>
                                        <div class="input-group">
                                            <input type="number" class="form-control" name="settings[map_refresh_rate]" value="{{ $settings['map_refresh_rate'] ?? '30' }}">
                                            <span class="input-group-text">seconds</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Unit Tracking Interval</label>
                                        <div class="input-group">
                                            <input type="number" class="form-control" name="settings[tracking_interval]" value="{{ $settings['tracking_interval'] ?? '60' }}">
                                            <span class="input-group-text">seconds</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Default Map Provider</label>
                                        <select class="form-select" name="settings[map_provider]">
                                            <option value="google" {{ ($settings['map_provider'] ?? 'google') == 'google' ? 'selected' : '' }}>Google Maps</option>
                                            <option value="mapbox" {{ ($settings['map_provider'] ?? 'google') == 'mapbox' ? 'selected' : '' }}>Mapbox</option>
                                            <option value="osm" {{ ($settings['map_provider'] ?? 'google') == 'osm' ? 'selected' : '' }}>OpenStreetMap</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Traffic Layer</label>
                                        <select class="form-select" name="settings[traffic_layer]">
                                            <option value="1" {{ ($settings['traffic_layer'] ?? '1') == '1' ? 'selected' : '' }}>Enabled</option>
                                            <option value="0" {{ ($settings['traffic_layer'] ?? '1') == '0' ? 'selected' : '' }}>Disabled</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card shadow mt-4">
                    <div class="card-body text-center">
                        <button type="submit" class="btn btn-primary px-5">
                            <i class="fas fa-save me-2"></i>Save Settings
                        </button>
                        <a href="{{ route('settings.index') }}" class="btn btn-outline-secondary px-5 ms-2">
                            <i class="fas fa-times me-2"></i>Cancel
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Quick Help Modal -->
<div class="modal fade" id="quickHelpModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-question-circle me-2"></i>Settings Help
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h6><i class="fas fa-traffic-light me-2"></i>Traffic Settings</h6>
                <p class="small">Configure thresholds for traffic congestion detection and alerts.</p>
                
                <h6 class="mt-3"><i class="fas fa-truck-fast me-2"></i>Dispatch Config</h6>
                <p class="small">Set response targets and unit assignment rules for traffic management teams.</p>
                
                <h6 class="mt-3"><i class="fas fa-map-location-dot me-2"></i>Map Settings</h6>
                <p class="small">Configure map display options and warden location tracking intervals.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Tab navigation
    $('#settingsNav a').on('click', function(e) {
        e.preventDefault();
        $(this).tab('show');
        
        // Update active class
        $('#settingsNav a').removeClass('active');
        $(this).addClass('active');
    });
});
</script>
@endpush