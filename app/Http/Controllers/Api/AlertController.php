<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Alert;

class AlertController extends Controller
{
    // ✅ Send emergency alert
    public function sendAlert(Request $request)
    {
        $request->validate([
            'message' => 'required|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        $user = $request->user();

        $alert = Alert::create([
            'user_id' => $user->id,
            'message' => $request->message,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
        ]);

        return response()->json([
            'message' => 'Alert sent successfully 🚨',
            'alert' => $alert,
        ]);
    }

    // ✅ Get all alerts
    public function listAlerts()
    {
        $alerts = Alert::with('user:id,name,email')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'alerts' => $alerts
        ]);
    }
}
