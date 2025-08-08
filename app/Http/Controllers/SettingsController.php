<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;

class SettingsController extends Controller
{
    public function index()
    {
        $settings = Setting::all()->pluck('value', 'key');
        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $fields = [
            'organization_name',
            'emergency_dispatch_number',
            'default_shift_start',
            'default_shift_end',
            'enable_shift_change_notifications',
            'enable_emergency_alerts',
            'timezone',
            'sms_gateway_enabled',
            'sms_gateway_provider',
            'map_refresh_interval',
            'max_squad_per_zone',
            'auto_assign_nearest_unit',
            'incident_urgency_threshold',
            'congestion_data_source',
            'broadcast_area_radius',
            'alert_tone',
            'night_mode_enabled',
            'auto_logout_timer',
            'language_preference',
            'backup_frequency',
            'enable_gps_tracking',
            'allow_manual_override',
        ];

        foreach ($fields as $field) {
            Setting::updateOrCreate(
                ['key' => $field],
                ['value' => $request->input($field) ?? '']
            );
        }

        return redirect()->back()->with('success', 'Settings updated successfully!');
    }
}
