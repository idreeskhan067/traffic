<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AttendanceController;
use App\Http\Controllers\Api\LocationController;
use App\Http\Controllers\Api\AlertController;
use App\Http\Controllers\Api\CongestionApiController;
use App\Http\Controllers\Api\EmergencyNotificationController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\ActivityController;  // <-- ADD THIS IMPORT
use App\Models\Attendance;

// Public endpoint that doesn't require authentication
Route::get('/public-test-stats', [App\Http\Controllers\Api\DashboardController::class, 'publicTestStats']);

// --- Auth Routes ---
Route::post('/login', [AuthController::class, 'login']);
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);

// --- Protected routes (token required) ---
Route::middleware('auth:sanctum')->group(function () {

    // --- Attendance ---
    Route::post('/attendance/check-in', [AttendanceController::class, 'checkIn']);
    Route::post('/attendance/check-out', [AttendanceController::class, 'checkOut']);
    Route::get('/attendance/history', [AttendanceController::class, 'history']);
    Route::get('/attendance/status', [AttendanceController::class, 'todayStatus']);

    // --- Location ---
    Route::post('/location/update', [LocationController::class, 'updateLocation']);
    Route::get('/location/wardens', [LocationController::class, 'getWardensLocations']);
    Route::post('/location/wardens/store', [LocationController::class, 'storeWarden']);

    // --- Alerts ---
    Route::post('/alerts/send', [AlertController::class, 'sendAlert']);
    Route::get('/alerts', [AlertController::class, 'listAlerts']);

    // --- Congestion Reports ---
    Route::post('/congestions/report', [CongestionApiController::class, 'report']);

    // --- Emergency Alerts ---
    Route::post('/emergency-alerts', [EmergencyNotificationController::class, 'store']);

    // --- Dashboard Routes (UPDATED) ---
    Route::prefix('dashboard')->group(function () {
        // Main dashboard stats
        Route::get('/', [DashboardController::class, 'index']);
        Route::get('/stats', [DashboardController::class, 'index']); // Alias for backward compatibility
        
        // Real-time endpoints
        Route::get('/warden-locations', [DashboardController::class, 'wardenLocations']);
        Route::get('/recent-activities', [DashboardController::class, 'recentActivities']);
        Route::get('/emergency-alerts', [DashboardController::class, 'emergencyAlerts']);
    });

    // --- Warden specific dashboard (kept for backward compatibility) ---
    Route::get('/warden/dashboard-stats', [DashboardController::class, 'index']);
    Route::get('/test-stats', [App\Http\Controllers\Api\DashboardController::class, 'testStats']);

    // --- Activity Routes ---
    Route::get('/warden/activities', [ActivityController::class, 'index']);
    Route::post('/warden/activities', [ActivityController::class, 'store']);

    // Latest attendances feed for admin (today only) - ENHANCED
    Route::get('/latest-attendances', function () {
        return response()->json([
            'success' => true,
            'data' => Attendance::with('user')
                ->whereDate('date', now()->toDateString())
                ->latest()
                ->take(10)
                ->get()
                ->map(function($attendance) {
                    return [
                        'id' => $attendance->id,
                        'user' => [
                            'name' => $attendance->user->name ?? 'Unknown',
                            'id' => $attendance->user_id
                        ],
                        'status' => $attendance->status,
                        'check_in_time' => $attendance->check_in_time,
                        'check_out_time' => $attendance->check_out_time,
                        'date' => $attendance->date,
                        'updated_at' => $attendance->updated_at->toISOString()
                    ];
                }),
            'timestamp' => now()->toISOString()
        ]);
    });

    // --- Logout ---
    Route::post('/logout', [AuthController::class, 'logout']);
});