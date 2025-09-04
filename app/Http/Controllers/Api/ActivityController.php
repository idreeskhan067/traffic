<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class ActivityController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        try {
            $userId = auth()->id();
            if (!$userId) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }

            $activities = Activity::where(function($query) use ($userId) {
                    $query->where('warden_id', $userId)
                          ->orWhere('user_id', $userId)
                          ->orWhere('performed_by', $userId);
                })
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get()
                ->map(function($activity) {
                    return [
                        'id' => $activity->id,
                        'type' => $activity->type ?? 'activity',
                        'description' => $activity->description ?? $activity->action ?? 'Activity logged',
                        'timestamp' => ($activity->timestamp ?? $activity->created_at)->toISOString(),
                        'location' => $activity->location,
                        'metadata' => $activity->metadata
                    ];
                });

            return response()->json([
                'success' => true,
                'activities' => $activities
            ], 200);
            
        } catch (\Exception $e) {
            Log::error('Activities index error', [
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch activities: ' . $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $userId = auth()->id();
            if (!$userId) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }

            $validated = $request->validate([
                'type' => 'required|string|max:50',
                'description' => 'required|string|max:500',
                'timestamp' => 'required|date',
                'location' => 'nullable|string|max:100',
                'metadata' => 'nullable|array'
            ]);

            $activity = Activity::create([
                'user_id' => $userId,
                'warden_id' => $userId,
                'performed_by' => $userId,
                'description' => $validated['description'],
                'action' => $validated['description'],
                'type' => $validated['type'],
                'details' => $validated['description'],
                'timestamp' => $validated['timestamp'],
                'location' => $validated['location'],
                'metadata' => $validated['metadata'],
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Activity logged successfully',
                'activity' => [
                    'id' => $activity->id,
                    'type' => $activity->type,
                    'description' => $activity->description,
                    'timestamp' => $activity->timestamp->toISOString(),
                    'location' => $activity->location,
                    'metadata' => $activity->metadata
                ]
            ], 201);
            
        } catch (\Exception $e) {
            Log::error('Activity store error', [
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to log activity: ' . $e->getMessage()
            ], 500);
        }
    }
}