<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Congestion;
use App\Models\Dispatch;
use App\Models\ActivityLog;
use App\Models\Activity;
use App\Models\Squad;
use App\Models\Attendance;
use App\Models\AssignedArea;
use App\Models\EmergencyNotification;
use App\Models\Shift;
use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    // Updated public test stats to use real database data
    public function publicTestStats()
    {
        Log::info("Public test stats endpoint called");
        
        try {
            $today = Carbon::today();
            
            // Get assigned areas count - using AssignedArea model now
            $assignedAreas = AssignedArea::where('status', 'active')->count();
            
            // Get pending tasks count - using dispatches that are pending
            $pendingTasks = Dispatch::where('status', 'pending')->count();
            
            // Get emergency alerts count from last 24 hours
            $alerts = EmergencyNotification::where('created_at', '>=', Carbon::now()->subHours(24))->count();
            
            // Get wardens on duty count
            $onDuty = User::where('role', 'warden')->where('status', 'on-duty')->count();
            
            Log::info("Public test stats generated: Areas: $assignedAreas, Tasks: $pendingTasks, Alerts: $alerts, OnDuty: $onDuty");
            
            return response()->json([
                "assigned_areas" => $assignedAreas,
                "pending_tasks" => $pendingTasks,
                "alerts" => $alerts,
                "on_duty" => $onDuty,
                "success" => true
            ]);
        } catch (\Exception $e) {
            Log::error("Public test stats error: " . $e->getMessage());
            
            // Fallback to some default values in case of error
            return response()->json([
                "assigned_areas" => 0,
                "pending_tasks" => 0,
                "alerts" => 0,
                "on_duty" => 0,
                "success" => false,
                "error" => "Database error: " . $e->getMessage()
            ]);
        }
    }
    
    public function index(Request $request)
    {
        try {
            $user = $request->user();
            $today = Carbon::today();
            
            // Add debugging for authentication
            Log::info("Dashboard request from user: " . ($user ? $user->id : 'NO USER - AUTHENTICATION FAILED'));
            
            // Handle case where authentication has failed
            if (!$user) {
                Log::warning("Authentication failed for dashboard request");
                
                // Return mock data for testing when authentication fails
                return response()->json([
                    "assigned_areas" => 50, // Distinctive test value
                    "pending_tasks" => 40,
                    "alerts" => 30,
                    "on_duty" => 20,
                    "auth_status" => "failed", // Flag to indicate auth failed
                    "message" => "Using mock data due to authentication failure"
                ]);
            }
            
            Log::info("User roles: " . ($user ? json_encode(['role' => $user->role]) : 'unknown'));
            
            // Check if the request is for a warden dashboard
            if ($user && ($user->role === 'warden' || (method_exists($user, 'hasRole') && $user->hasRole('warden')))) {
                Log::info("Redirecting to warden dashboard");
                return $this->wardenDashboard($user);
            }
            
            Log::info("Continuing with admin dashboard");
            
            // Continue with admin dashboard logic
            $onDutyWardens = User::where('role', 'warden')->where('status', 'on-duty')->count();
            $reportedCongestions = Congestion::where('status', 'reported')->count();
            $pendingDispatches = Dispatch::where('status', 'pending')->count();
            $totalAssignedAreas = AssignedArea::where('status', 'active')->count(); // Changed to use AssignedArea
            
            // Dynamic resolved congestions
            $resolvedCongestions = Congestion::where('status', 'cleared')
                ->whereDate('updated_at', $today)
                ->count();
            
            // Emergency count (using created_at since no status column)
            $emergencyCount = EmergencyNotification::where('created_at', '>=', Carbon::now()->subHours(24))
                ->count();
            
            // Attendance stats
            $attendanceToday = Attendance::whereDate('date', $today)->count();
            $totalWardens = User::where('role', 'warden')->count();
            
            return response()->json([
                'success' => true,
                'data' => [
                    'on_duty_wardens' => $onDutyWardens,
                    'reported_congestions' => $reportedCongestions,
                    'pending_dispatches' => $pendingDispatches,
                    'total_squads' => $totalAssignedAreas, // Kept the same key for backward compatibility
                    'resolved_congestions' => $resolvedCongestions,
                    'emergency_count' => $emergencyCount,
                    'attendance_today' => $attendanceToday,
                    'total_wardens' => $totalWardens,
                    'attendance_percentage' => $totalWardens > 0 ? round(($attendanceToday / $totalWardens) * 100, 2) : 0,
                ],
                'timestamp' => now()->toISOString()
            ]);
            
        } catch (\Exception $e) {
            Log::error("Dashboard error: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch dashboard data: ' . $e->getMessage(),
                'error' => $e->getMessage()
            ], 500);
        }
    }

    private function wardenDashboard($user)
    {
        try {
            Log::info("Generating warden dashboard for user: {$user->id}");
            $today = Carbon::today();
            
            // Get assigned areas count - now filtering by this specific warden
            $assignedAreas = AssignedArea::where('warden_id', $user->id)
                ->where('status', 'active')
                ->count();
            
            // Get pending tasks count - using Task model with correct field name
            $pendingTasks = Task::where('status', 'pending')
                ->where('assigned_to', $user->id)  // Fixed field name from assignee_id to assigned_to
                ->count();
            
            // Get emergency alerts count from last 24 hours
            $alerts = EmergencyNotification::where('created_at', '>=', Carbon::now()->subHours(24))->count();
            
            // Get wardens on duty count
            $onDuty = User::where('role', 'warden')->where('status', 'on-duty')->count();
            
            Log::info("Warden dashboard stats generated: Areas: $assignedAreas, Tasks: $pendingTasks, Alerts: $alerts, OnDuty: $onDuty");
            
            return response()->json([
                "assigned_areas" => $assignedAreas,
                "pending_tasks" => $pendingTasks,
                "alerts" => $alerts,
                "on_duty" => $onDuty,
                "user_status" => $user->status ?? 'active',
                "success" => true,
            ]);
            
        } catch (\Exception $e) {
            Log::error("Warden dashboard error: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch warden dashboard data: ' . $e->getMessage(),
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function wardenLocations()
    {
        try {
            $wardensWithLocation = User::where('role', 'warden')
                ->whereHas('location', function($query) {
                    $query->where('updated_at', '>=', Carbon::now()->subMinutes(30));
                })
                ->with(['location' => function($query) {
                    $query->latest();
                }])
                ->get()
                ->map(function($warden) {
                    return [
                        'id' => $warden->id,
                        'name' => $warden->name,
                        'status' => $warden->status,
                        'location' => $warden->location ? [
                            'latitude' => $warden->location->latitude,
                            'longitude' => $warden->location->longitude,
                            'updated_at' => $warden->location->updated_at->toISOString()
                        ] : null
                    ];
                });

            return response()->json([
                'success' => true,
                'wardens' => $wardensWithLocation,
                'total_count' => $wardensWithLocation->count(),
                'timestamp' => now()->toISOString()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch warden locations: ' . $e->getMessage()
            ], 500);
        }
    }

    public function recentActivities()
    {
        try {
            $activities = collect();
            
            try {
                if (class_exists(ActivityLog::class)) {
                    $activities = $activities->merge(
                        ActivityLog::latest()->take(5)->get()
                    );
                }
            } catch (\Exception $e) {
                // Skip if table doesn't exist
            }
            
            try {
                if (class_exists(\App\Models\Activity::class)) {
                    $activities = $activities->merge(
                        \App\Models\Activity::latest()->take(5)->get()
                    );
                }
            } catch (\Exception $e) {
                // Skip if table doesn't exist
            }
            
            $activities = $activities->sortByDesc('created_at')->take(5);

            return response()->json([
                'success' => true,
                'activities' => $activities->values(),
                'count' => $activities->count(),
                'timestamp' => now()->toISOString()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch activities: ' . $e->getMessage()
            ], 500);
        }
    }

    public function emergencyAlerts()
    {
        try {
            // No status column, so just get recent emergencies (limit to 5)
            $emergencies = EmergencyNotification::where('created_at', '>=', Carbon::now()->subHours(24))
                ->latest()
                ->take(5)
                ->get()
                ->map(function($emergency) {
                    return [
                        'id' => $emergency->id,
                        'title' => $emergency->title ?? 'Emergency Alert',
                        'message' => $emergency->message,
                        'status' => 'active', // Default since no status column
                        'created_at' => $emergency->created_at->toISOString(),
                        'priority' => $emergency->priority ?? 'medium'
                    ];
                });

            return response()->json([
                'success' => true,
                'emergencies' => $emergencies,
                'count' => $emergencies->count(),
                'timestamp' => now()->toISOString()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch emergency alerts: ' . $e->getMessage()
            ], 500);
        }
    }
}