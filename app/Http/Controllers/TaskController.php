<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class TaskController extends Controller
{
    public function index()
    {
        $tasks = Task::with(['assignee', 'creator'])->latest()->paginate(10);
        return view('admin.tasks.index', compact('tasks'));
    }

    public function create()
    {
        $wardens = User::role('warden')->get();
        return view('admin.tasks.create', compact('wardens'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'required|in:low,medium,high,urgent',
            'status' => 'required|in:pending,in_progress,completed,cancelled',
            'assigned_to' => 'required|exists:users,id',
            'due_date' => 'nullable|date',
        ]);

        $validated['created_by'] = auth()->id();

        $task = Task::create($validated);

        // Log the activity
        if (class_exists('App\Models\ActivityLog')) {
            \App\Models\ActivityLog::create([
                'action' => 'Task Created',
                'performed_by' => auth()->id(),
                'target' => 'Task #' . $task->id,
                'description' => "Created task '{$task->title}' assigned to " . $task->assignee->name
            ]);
        }

        return redirect()->route('admin.tasks.index')
            ->with('success', 'Task created successfully');
    }

    // Add other CRUD methods (show, edit, update, destroy)
}