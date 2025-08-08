@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-cog me-2"></i>System Configuration Panel
        </h1>
        <div class="btn-group">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#quickHelpModal">
                <i class="fas fa-question-circle me-1"></i> Quick Help
            </button>
            <button type="button" class="btn btn-success" id="saveAllBtn">
                <i class="fas fa-save me-1"></i> Save All Changes
            </button>
            <button type="button" class="btn btn-outline-secondary" id="resetDefaultsBtn">
                <i class="fas fa-undo me-1"></i> Reset Defaults
            </button>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <!-- Left Navigation -->
        <div class="col-md-3 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-bars me-1"></i>Settings Categories
                    </h6>
                </div>
                <div class="list-group list-group-flush" id="settingsNav">
                    <a class="list-group-item list-group-item-action active" href="#trafficSettings" data-bs-toggle="tab">
                        <i class="fas fa-traffic-light me-2"></i>Traffic Control
                    </a>
                    <a class="list-group-item list-group-item-action" href="#dispatchSettings" data-bs-toggle="tab">
                        <i class="fas fa-truck-fast me-2"></i>Dispatch Config
                    </a>
                    <a class="list-group-item list-group-item-action" href="#shiftSettings" data-bs-toggle="tab">
                        <i class="fas fa-user-clock me-2"></i>Shift Management
                    </a>
                    <a class="list-group-item list-group-item-action" href="#notificationSettings" data-bs-toggle="tab">
                        <i class="fas fa-bell me-2"></i>Alerts & Notifications
                    </a>
                    <a class="list-group-item list-group-item-action" href="#mapSettings" data-bs-toggle="tab">
                        <i class="fas fa-map-location-dot me-2"></i>Map & Tracking
                    </a>
                    <a class="list-group-item list-group-item-action" href="#systemSettings" data-bs-toggle="tab">
                        <i class="fas fa-server me-2"></i>System Config
                    </a>
                </div>
            </div>
            
            <div class="card shadow mt-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-chart-line me-1"></i>Quick Stats
                    </h6>
                </div>
                <div class="card-body small">
                    <div class="mb-3">
                        <span class="fw-bold">Active Units:</span> 
                        <span class="badge bg-success float-end">24/30</span>
                    </div>
                    <div class="mb-3">
                        <span class="fw-bold">Current Alerts:</span> 
                        <span class="badge bg-warning text-dark float-end">5</span>
                    </div>
                    <div class="mb-3">
                        <span class="fw-bold">Avg Response Time:</span> 
                        <span class="badge bg-info float-end">12.4 mins</span>
                    </div>
                    <div>
                        <span class="fw-bold">System Uptime:</span> 
                        <span class="badge bg-primary float-end">99.87%</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Content -->
        <div class="col-md-9">
            <div class="tab-content" id="settingsContent">
                <!-- Traffic Settings Tab -->
                <div class="tab-pane fade show active" id="trafficSettings">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3 d-flex justify-content-between align-items-center">
                            <h6 class="m-0 font-weight-bold text-primary">
                                <i class="fas fa-traffic-light me-2"></i>Traffic Control Parameters
                            </h6>
                            <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="collapse" data-bs-target="#trafficHelp">
                                <i class="fas fa-info-circle"></i>
                            </button>
                        </div>
                        <div class="collapse" id="trafficHelp">
                            <div class="card-body bg-light">
                                <p class="small mb-0">Configure thresholds for automatic traffic congestion detection and alert generation.</p>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Congestion Threshold (veh/km)</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" name="congestion_threshold" value="{{ $settings['congestion_threshold'] ?? '50' }}">
                                        <span class="input-group-text">veh/km</span>
                                    </div>
                                    <small class="text-muted">Minimum vehicle density to trigger alert</small>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Severe Congestion Level</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" name="severe_congestion" value="{{ $settings['severe_congestion'] ?? '100' }}">
                                        <span class="input-group-text">veh/km</span>
                                    </div>
                                    <small class="text-muted">Density for high-priority alerts</small>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Auto Alert Generation</label>
                                    <select class="form-select" name="auto_alerts">
                                        <option value="1" {{ ($settings['auto_alerts'] ?? '1') == '1' ? 'selected' : '' }}>Enabled</option>
                                        <option value="0" {{ ($settings['auto_alerts'] ?? '1') == '0' ? 'selected' : '' }}>Disabled</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Minimum Incident Duration</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" name="min_incident_duration" value="{{ $settings['min_incident_duration'] ?? '5' }}">
                                        <span class="input-group-text">minutes</span>
                                    </div>
                                    <small class="text-muted">Before triggering alert</small>
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
                                        <input type="number" class="form-control" name="response_time_target" value="{{ $settings['response_time_target'] ?? '15' }}">
                                        <span class="input-group-text">minutes</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Max Units Per Incident</label>
                                    <input type="number" class="form-control" name="max_units_per_incident" value="{{ $settings['max_units_per_incident'] ?? '3' }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Auto Dispatch</label>
                                    <select class="form-select" name="auto_dispatch">
                                        <option value="1" {{ ($settings['auto_dispatch'] ?? '1') == '1' ? 'selected' : '' }}>Enabled</option>
                                        <option value="0" {{ ($settings['auto_dispatch'] ?? '1') == '0' ? 'selected' : '' }}>Disabled</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Dispatch Priority Algorithm</label>
                                    <select class="form-select" name="dispatch_algorithm">
                                        <option value="proximity" {{ ($settings['dispatch_algorithm'] ?? 'proximity') == 'proximity' ? 'selected' : '' }}>Proximity Based</option>
                                        <option value="availability" {{ ($settings['dispatch_algorithm'] ?? 'proximity') == 'availability' ? 'selected' : '' }}>Availability Based</option>
                                        <option value="hybrid" {{ ($settings['dispatch_algorithm'] ?? 'proximity') == 'hybrid' ? 'selected' : '' }}>Hybrid Approach</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Shift Settings Tab -->
                <div class="tab-pane fade" id="shiftSettings">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">
                                <i class="fas fa-user-clock me-2"></i>Shift Management
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label">Morning Shift Start</label>
                                    <input type="time" class="form-control" name="morning_shift_start" value="{{ $settings['morning_shift_start'] ?? '08:00' }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Evening Shift Start</label>
                                    <input type="time" class="form-control" name="evening_shift_start" value="{{ $settings['evening_shift_start'] ?? '16:00' }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Night Shift Start</label>
                                    <input type="time" class="form-control" name="night_shift_start" value="{{ $settings['night_shift_start'] ?? '00:00' }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Shift Duration</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" name="shift_duration" value="{{ $settings['shift_duration'] ?? '8' }}">
                                        <span class="input-group-text">hours</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Minimum Staff Per Shift</label>
                                    <input type="number" class="form-control" name="min_staff_per_shift" value="{{ $settings['min_staff_per_shift'] ?? '5' }}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Notification Settings Tab -->
                <div class="tab-pane fade" id="notificationSettings">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">
                                <i class="fas fa-bell me-2"></i>Alerts & Notifications
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Notification Methods</label>
                                    <select class="form-select" name="notification_methods[]" multiple>
                                        <option value="sms" {{ in_array('sms', explode(',', $settings['notification_methods'] ?? 'sms,email')) ? 'selected' : '' }}>SMS</option>
                                        <option value="email" {{ in_array('email', explode(',', $settings['notification_methods'] ?? 'sms,email')) ? 'selected' : '' }}>Email</option>
                                        <option value="app" {{ in_array('app', explode(',', $settings['notification_methods'] ?? 'sms,email')) ? 'selected' : '' }}>In-App</option>
                                        <option value="push" {{ in_array('push', explode(',', $settings['notification_methods'] ?? 'sms,email')) ? 'selected' : '' }}>Push Notification</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Emergency Contact Numbers</label>
                                    <textarea class="form-control" name="emergency_contacts" rows="3">{{ $settings['emergency_contacts'] ?? '' }}</textarea>
                                    <small class="text-muted">Comma-separated numbers</small>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Alert Volume</label>
                                    <input type="range" class="form-range" name="alert_volume" min="0" max="100" value="{{ $settings['alert_volume'] ?? '80' }}">
                                    <div class="d-flex justify-content-between small">
                                        <span>Low</span>
                                        <span>High</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Alert Sound</label>
                                    <select class="form-select" name="alert_sound">
                                        <option value="siren" {{ ($settings['alert_sound'] ?? 'siren') == 'siren' ? 'selected' : '' }}>Siren</option>
                                        <option value="beep" {{ ($settings['alert_sound'] ?? 'siren') == 'beep' ? 'selected' : '' }}>Beep</option>
                                        <option value="chime" {{ ($settings['alert_sound'] ?? 'siren') == 'chime' ? 'selected' : '' }}>Chime</option>
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
                                        <input type="number" class="form-control" name="map_refresh_rate" value="{{ $settings['map_refresh_rate'] ?? '30' }}">
                                        <span class="input-group-text">seconds</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Unit Tracking Interval</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" name="tracking_interval" value="{{ $settings['tracking_interval'] ?? '60' }}">
                                        <span class="input-group-text">seconds</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Default Map Provider</label>
                                    <select class="form-select" name="map_provider">
                                        <option value="google" {{ ($settings['map_provider'] ?? 'google') == 'google' ? 'selected' : '' }}>Google Maps</option>
                                        <option value="mapbox" {{ ($settings['map_provider'] ?? 'google') == 'mapbox' ? 'selected' : '' }}>Mapbox</option>
                                        <option value="osm" {{ ($settings['map_provider'] ?? 'google') == 'osm' ? 'selected' : '' }}>OpenStreetMap</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Traffic Layer</label>
                                    <select class="form-select" name="traffic_layer">
                                        <option value="1" {{ ($settings['traffic_layer'] ?? '1') == '1' ? 'selected' : '' }}>Enabled</option>
                                        <option value="0" {{ ($settings['traffic_layer'] ?? '1') == '0' ? 'selected' : '' }}>Disabled</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- System Settings Tab -->
                <div class="tab-pane fade" id="systemSettings">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">
                                <i class="fas fa-server me-2"></i>System Configuration
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Timezone</label>
                                    <select class="form-select" name="timezone">
                                        <option value="UTC+5" {{ ($settings['timezone'] ?? 'UTC+5') == 'UTC+5' ? 'selected' : '' }}>UTC+5 (PKT)</option>
                                        <option value="UTC+0" {{ ($settings['timezone'] ?? 'UTC+5') == 'UTC+0' ? 'selected' : '' }}>UTC+0 (GMT)</option>
                                        <option value="UTC-5" {{ ($settings['timezone'] ?? 'UTC+5') == 'UTC-5' ? 'selected' : '' }}>UTC-5 (EST)</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Data Retention</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" name="data_retention" value="{{ $settings['data_retention'] ?? '30' }}">
                                        <span class="input-group-text">days</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">System Mode</label>
                                    <select class="form-select" name="system_mode">
                                        <option value="production" {{ ($settings['system_mode'] ?? 'production') == 'production' ? 'selected' : '' }}>Production</option>
                                        <option value="maintenance" {{ ($settings['system_mode'] ?? 'production') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                                        <option value="debug" {{ ($settings['system_mode'] ?? 'production') == 'debug' ? 'selected' : '' }}>Debug</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Auto Backup</label>
                                    <select class="form-select" name="auto_backup">
                                        <option value="daily" {{ ($settings['auto_backup'] ?? 'daily') == 'daily' ? 'selected' : '' }}>Daily</option>
                                        <option value="weekly" {{ ($settings['auto_backup'] ?? 'daily') == 'weekly' ? 'selected' : '' }}>Weekly</option>
                                        <option value="monthly" {{ ($settings['auto_backup'] ?? 'daily') == 'monthly' ? 'selected' : '' }}>Monthly</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow mt-4">
                <div class="card-body text-center">
                    <button type="submit" class="btn btn-primary px-5 me-3">
                        <i class="fas fa-save me-2"></i>Save All Settings
                    </button>
                    <button type="button" class="btn btn-outline-secondary px-5" id="discardChangesBtn">
                        <i class="fas fa-times me-2"></i>Discard Changes
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Help Modal -->
<div class="modal fade" id="quickHelpModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-question-circle me-2"></i>Settings Panel Help
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6><i class="fas fa-traffic-light me-2"></i>Traffic Settings</h6>
                        <p class="small">Configure thresholds for automatic traffic detection and alert generation.</p>
                        
                        <h6 class="mt-3"><i class="fas fa-truck-fast me-2"></i>Dispatch Config</h6>
                        <p class="small">Set response targets and unit assignment rules.</p>
                    </div>
                    <div class="col-md-6">
                        <h6><i class="fas fa-user-clock me-2"></i>Shift Management</h6>
                        <p class="small">Configure shift patterns and staff requirements.</p>
                        
                        <h6 class="mt-3"><i class="fas fa-bell me-2"></i>Notifications</h6>
                        <p class="small">Manage alert delivery methods and emergency contacts.</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Tab navigation
    $('#settingsNav a').on('click', function(e) {
        e.preventDefault();
        $(this).tab('show');
    });

    // Save all button
    $('#saveAllBtn').click(function() {
        $('form').submit();
    });

    // Reset defaults
    $('#resetDefaultsBtn').click(function() {
        if(confirm('Are you sure you want to reset all settings to default values?')) {
            // AJAX call to reset defaults
            alert('Settings reset to defaults');
        }
    });

    // Discard changes
    $('#discardChangesBtn').click(function() {
        if(confirm('Discard all unsaved changes?')) {
            location.reload();
        }
    });
});
</script>
@endsection