<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Validator;

// Add these use statements
use App\Models\Task;
use App\Models\EmergencyAlert;
use App\Models\Attendance;

class WardenController extends Controller
{
    /**
     * Display a listing of wardens.
     */
    public function index(Request $request)
    {
        // Use the same approach as DashboardController
        $query = User::role('warden'); // Using Spatie Permission
        
        // Apply filters
        if ($request->has('filter')) {
            switch ($request->filter) {
                case 'on-duty':
                    $query->where('status', 'on-duty');
                    break;
                case 'off-duty':
                    $query->where('status', 'off-duty');
                    break;
            }
        }
        
        // Apply search
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%")
                  ->orWhere('phone', 'LIKE', "%{$search}%");
            });
        }
        
        $wardens = $query->orderBy('created_at', 'desc')->paginate(10);
        
        return view('admin.wardens.index', compact('wardens'));
    }

    /**
     * Show the form for creating a new warden.
     */
    public function create()
    {
        return view('admin.wardens.create');
    }

    /**
     * Store a newly created warden in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'badge_number' => 'nullable|string|max:50|unique:users',
            'status' => 'required|in:on-duty,off-duty',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $warden = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'badge_number' => $request->badge_number,
            'status' => $request->status ?? 'off-duty',
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
        ]);

        // Assign warden role using Spatie Permission
        $warden->assignRole('warden');

        ActivityLog::create([
            'performed_by' => auth()->user()->name ?? 'System',
            'target' => 'Warden: ' . $warden->name,
            'description' => 'A new warden (' . $warden->name . ') was added by ' . (auth()->user()->name ?? 'System'),
        ]);

        return redirect()->route('admin.wardens.index')->with('success', 'Warden created successfully.');
    }

    /**
     * Display the specified warden.
     */
    public function show($id)
    {
        $warden = User::role('warden')->findOrFail($id);
        
        // You can add additional data here like assigned areas, tasks, etc.
        // $assignedAreas = $warden->areas;
        // $recentTasks = $warden->tasks()->latest()->take(5)->get();
        
        return view('admin.wardens.show', compact('warden'));
    }

    /**
     * Show the form for editing the specified warden.
     */
    public function edit($id)
    {
        $warden = User::role('warden')->findOrFail($id);
        return view('admin.wardens.edit', compact('warden'));
    }

    /**
     * Update the specified warden in storage.
     */
    public function update(Request $request, $id)
    {
        $warden = User::role('warden')->findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $id,
            'password' => 'nullable|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'badge_number' => 'nullable|string|max:50|unique:users,badge_number,' . $id,
            'status' => 'required|in:on-duty,off-duty',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $updateData = [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'badge_number' => $request->badge_number,
            'status' => $request->status,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
        ];

        // Only update password if provided
        if (!empty($request->password)) {
            $updateData['password'] = Hash::make($request->password);
        }

        $warden->update($updateData);

        ActivityLog::create([
            'performed_by' => auth()->user()->name ?? 'System',
            'target' => 'Warden: ' . $warden->name,
            'description' => 'Warden (' . $warden->name . ') was updated by ' . (auth()->user()->name ?? 'System'),
        ]);

        return redirect()->route('admin.wardens.index')
            ->with('success', 'Warden updated successfully.');
    }

    /**
     * Remove the specified warden from storage.
     */
    public function destroy($id)
    {
        $warden = User::role('warden')->findOrFail($id);
        
        $wardenName = $warden->name;
        
        ActivityLog::create([
            'performed_by' => auth()->user()->name ?? 'System',
            'target' => 'Warden: ' . $warden->name,
            'description' => 'Warden (' . $warden->name . ') was deleted by ' . (auth()->user()->name ?? 'System'),
        ]);
        
        $warden->delete();

        return redirect()->route('admin.wardens.index')
            ->with('success', "Warden {$wardenName} deleted successfully.");
    }

    /**
     * Toggle warden status between on-duty and off-duty.
     */
    public function toggleStatus($id)
    {
        $warden = User::role('warden')->findOrFail($id);
        
        $oldStatus = $warden->status;
        $newStatus = $warden->status === 'on-duty' ? 'off-duty' : 'on-duty';
        $warden->update(['status' => $newStatus]);

        ActivityLog::create([
            'performed_by' => auth()->user()->name ?? 'System',
            'target' => 'Warden: ' . $warden->name,
            'description' => "Status changed from {$oldStatus} to {$newStatus} by " . (auth()->user()->name ?? 'System'),
        ]);

        return redirect()->back()
            ->with('success', "Warden status changed to {$newStatus}.");
    }

    /**
     * Flutter API for Warden Dashboard
     */
    public function dashboardStats(Request $request)
    {
        $user = $request->user(); // Authenticated warden

        // Adjust relationships as needed
        $assignedAreas = $user->areas()->count();

        $pendingTasks = Task::where('warden_id', $user->id)
                            ->where('status', 'pending')
                            ->count();

        $alerts = EmergencyAlert::where('status', 'active')->count();

        $onDuty = Attendance::whereDate('created_at', now()->toDateString())
                            ->where('status', 'in')
                            ->count();

        return response()->json([
            'assigned_areas' => $assignedAreas,
            'pending_tasks' => $pendingTasks,
            'alerts' => $alerts,
            'on_duty' => $onDuty,
        ]);
    }
}