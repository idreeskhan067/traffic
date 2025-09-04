<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\EmergencyNotification;

class EmergencyNotificationController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'nullable|string',
            'priority' => 'required|in:low,medium,high,critical',
        ]);

        $alert = EmergencyNotification::create([
            'title' => $validated['title'],
            'message' => $validated['message'] ?? '',
            'priority' => $validated['priority'],
            'notified_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'data' => $alert,
            'message' => 'Emergency alert created successfully.'
        ], 201);
    }
}
