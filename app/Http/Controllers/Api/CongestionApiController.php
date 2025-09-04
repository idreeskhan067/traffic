<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Congestion;

class CongestionApiController extends Controller
{
    public function report(Request $request)
    {
        $request->validate([
            'location'    => 'required|string|max:255',
            'zone'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'severity'    => 'nullable|in:low,medium,high',
        ]);

        $congestion = Congestion::create([
            'location'    => $request->location,
            'zone'        => $request->zone,
            'status'      => 'reported',
            'description' => $request->description,
            'severity'    => $request->severity,
            'reported_by' => $request->user()->id,
        ]);

        return response()->json([
            'message' => 'Congestion reported successfully.',
            'data'    => $congestion,
        ], 201);
    }
}
