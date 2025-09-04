<?php

namespace App\Http\Controllers;

use App\Models\AssignedArea;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class AreaAssignmentController extends Controller
{
    public function index()
    {
        $areas = AssignedArea::with(['warden', 'assigner'])->latest()->paginate(10);
        return view('admin.areas.index', compact('areas'));
    }

    public function create()
    {
        $wardens = User::role('warden')->get();
        return view('admin.areas.create', compact('wardens'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'warden_id' => 'required|exists:users,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'boundaries' => 'nullable|json',
            'status' => 'required|in:active,inactive',
        ]);

        $validated['assigned_by'] = auth()->id();
        $validated['assigned_at'] = now();

        AssignedArea::create($validated);

        // Log the activity
        if (class_exists('App\Models\ActivityLog')) {
            \App\Models\ActivityLog::create([
                'action' => 'Area Assigned',
                'performed_by' => auth()->id(),
                'target' => 'User #' . $request->warden_id,
                'description' => "Assigned area '{$request->name}' to warden"
            ]);
        }

        return redirect()->route('admin.areas.index')
            ->with('success', 'Area assigned successfully');
    }

    // Add other CRUD methods (edit, update, destroy)
}