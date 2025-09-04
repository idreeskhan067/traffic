<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Location;

class LocationController extends Controller
{
    // Update current authenticated user's location
    public function updateLocation(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $user = $request->user();

        $location = Location::updateOrCreate(
            ['user_id' => $user->id],
            [
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
            ]
        );

        return response()->json([
            'success' => true,
            'location' => $location
        ]);
    }

    // Get all wardens’ locations
    public function getWardensLocations()
    {
        $locations = Location::with('user')->get();

        return response()->json([
            'wardens_locations' => $locations
        ]);
    }

    // Create or update warden location by user_id (admin or authorized user)
    public function storeWarden(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $location = Location::updateOrCreate(
            ['user_id' => $request->user_id],
            [
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
            ]
        );

        return response()->json([
            'success' => true,
            'location' => $location
        ]);
    }
}
