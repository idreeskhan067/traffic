<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Congestion;
use App\Models\Dispatch;
use App\Models\ActivityLog;
use App\Models\Squad;
use App\Models\Attendance;
use App\Models\EmergencyNotification;
use App\Models\Shift;
use App\Models\Warden;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Wardens stats using Spatie role and status
        $onDutyWardens = User::role('warden')->where('status', 'on-duty')->count();
        $totalWardens = User::role('warden')->count();

        // Attendance stats
        $today = Carbon::today();
        $presentToday = Attendance::whereDate('date', $today)->count();
        $absentToday = $totalWardens - $presentToday;
        $latestAttendances = Attendance::with('user')->latest()->take(5)->get();

        // Congestion stats
        $reportedCongestions = Congestion::where('status', 'reported')->count();
        $recentCongestions = Congestion::latest()->take(5)->get();
        $trafficData = Congestion::selectRaw('zone, COUNT(*) as total')->groupBy('zone')->pluck('total', 'zone');

        // Dispatch stats
        $pendingDispatches = Dispatch::where('status', 'pending')->count();

        // Squad stats
        $totalSquads = Squad::count();
        $awaitingDeploymentTeams = Squad::where('status', 'ready')->whereNull('dispatched_at')->count();
        $availableSquads = Squad::where('status', 'ready')->whereNull('dispatched_at')->get();

        // Emergency notifications
        $recentEmergencies = EmergencyNotification::latest()->take(5)->get();

        // Activities
        $recentActivities = ActivityLog::latest()->take(5)->get();

        // Shifts
        $totalShifts = Shift::count();
        $shifts = Shift::orderBy('start_time')->get();

        // Wardens with valid coordinates for map pins
        $wardensWithLocation = Warden::whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->select('id', 'name', 'latitude', 'longitude', 'last_logged_in_at')
            ->get();

        // Congestions to show on map
        $congestionLocations = Congestion::where('status', 'reported')
            ->select('id', 'location', 'status')
            ->get();

        return view('admin.dashboard', compact(
            'onDutyWardens',
            'reportedCongestions',
            'pendingDispatches',
            'totalSquads',
            'awaitingDeploymentTeams',
            'recentActivities',
            'trafficData',
            'recentCongestions',
            'availableSquads',
            'presentToday',
            'absentToday',
            'totalWardens',
            'latestAttendances',
            'recentEmergencies',
            'totalShifts',
            'shifts',
            'wardensWithLocation',
            'congestionLocations'
        ));
    }
}
