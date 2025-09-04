<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Attendance;
use App\Models\Congestion;
use App\Models\Dispatch;
use App\Models\EmergencyNotification;
use App\Models\Squad;
use App\Models\User;
use App\Models\Shift;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class DashboardController extends Controller
{
    /**
     * Display the dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Wardens on duty
        $onDutyWardens = User::role('warden')->where('status', 'on-duty')->count();
        
        // Congestion data
        $reportedCongestions = Congestion::where('status', 'reported')->count();
        $resolvedCongestions = Congestion::where('status', 'cleared')->count();
        $recentCongestions = Congestion::latest()->take(5)->get();
        
        // Dispatch data
        $pendingDispatches = Dispatch::where('status', 'pending')->count();
        
        // Squad data
        $totalSquads = Squad::count();
        $availableSquads = Squad::where('status', 'available')->get();
        
        // Emergencies
        $today = Carbon::today();
        $emergencyCount = EmergencyNotification::whereDate('created_at', $today)->count();
        $recentEmergencies = EmergencyNotification::latest()->take(5)->get();
        
        // Recent activities
        $recentActivities = ActivityLog::latest()->take(5)->get();
        
        // Wardens with location for map
        $wardensWithLocation = User::role('warden')
            ->whereHas('location')
            ->with('location')
            ->get();
        
        // Attendance data
        $wardens = User::role('warden')->get();
        $attendances = Attendance::whereDate('date', $today)
            ->get()
            ->keyBy('user_id');
        
        $latestAttendances = $wardens->map(function ($warden) use ($attendances) {
            $a = $attendances->get($warden->id);
            return [
                'user' => [
                    'id' => $warden->id,
                    'name' => $warden->name,
                ],
                'status' => $a->status ?? 'not_checked_in',
                'check_in_time' => $a->check_in_time ?? null,
                'check_out_time' => $a->check_out_time ?? null,
            ];
        })->take(5);
        
        // Shifts information
        $shifts = Shift::orderBy('start_time')->get();
        $shifts->each(function ($shift) {
            $shift->formatted_start_time = Carbon::parse($shift->start_time)->format('g:i A');
            $shift->formatted_end_time = Carbon::parse($shift->end_time)->format('g:i A');
        });
        
        // Traffic data for charts
        $trafficData = Congestion::select(DB::raw('count(*) as count'), 'zone')
            ->groupBy('zone')
            ->orderBy('count', 'desc')
            ->take(4)
            ->pluck('count', 'zone');
        
        return view('admin.dashboard', compact(
            'onDutyWardens',
            'reportedCongestions',
            'resolvedCongestions',
            'recentCongestions',
            'pendingDispatches',
            'totalSquads',
            'availableSquads',
            'emergencyCount',
            'recentEmergencies',
            'recentActivities',
            'wardensWithLocation',
            'latestAttendances',
            'shifts',
            'trafficData'
        ));
    }
    
    /**
     * Get real-time dashboard data for AJAX updates
     * 
     * @return JsonResponse
     */
    public function realtimeData()
    {
        try {
            // Wardens on duty
            $onDutyWardens = User::role('warden')->where('status', 'on-duty')->count();
            
            // Congestion data
            $reportedCongestions = Congestion::where('status', 'reported')->count();
            $resolvedCongestions = Congestion::where('status', 'cleared')->count();
            $recentCongestions = Congestion::latest()->take(5)->get();
            
            // Dispatch data
            $pendingDispatches = Dispatch::where('status', 'pending')->count();
            
            // Squad data
            $totalSquads = Squad::count();
            
            // Emergencies
            $today = Carbon::today();
            $emergencyCount = EmergencyNotification::whereDate('created_at', $today)->count();
            $recentEmergencies = EmergencyNotification::latest()->take(5)->get();
            
            // Recent activities
            $recentActivities = ActivityLog::latest()->take(5)->get();
            
            // Wardens with location for map
            $wardensWithLocation = User::role('warden')
                ->whereHas('location')
                ->with('location')
                ->get();
            
            // Attendance data
            $wardens = User::role('warden')->get();
            $attendances = Attendance::whereDate('date', $today)
                ->get()
                ->keyBy('user_id');
            
            $latestAttendances = $wardens->map(function ($warden) use ($attendances) {
                $a = $attendances->get($warden->id);
                return [
                    'user' => [
                        'id' => $warden->id,
                        'name' => $warden->name,
                    ],
                    'status' => $a->status ?? 'not_checked_in',
                    'check_in_time' => $a->check_in_time ?? null,
                    'check_out_time' => $a->check_out_time ?? null,
                ];
            })->take(5);
            
            // Shifts information (optional for real-time updates)
            $shifts = Shift::orderBy('start_time')->get();
            $shifts->each(function ($shift) {
                $shift->formatted_start_time = Carbon::parse($shift->start_time)->format('g:i A');
                $shift->formatted_end_time = Carbon::parse($shift->end_time)->format('g:i A');
            });
            
            return response()->json([
                'success' => true,
                'data' => compact(
                    'onDutyWardens',
                    'reportedCongestions',
                    'resolvedCongestions',
                    'recentCongestions',
                    'pendingDispatches',
                    'totalSquads',
                    'emergencyCount',
                    'recentEmergencies',
                    'recentActivities',
                    'wardensWithLocation',
                    'latestAttendances',
                    'shifts'
                )
            ]);
        } catch (\Exception $e) {
            Log::error('Dashboard real-time data error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch real-time data: ' . $e->getMessage()
            ], 500);
        }
    }
}