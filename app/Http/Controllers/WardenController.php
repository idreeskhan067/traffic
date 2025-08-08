<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Models\ActivityLog;

class WardenController extends Controller
{
    public function index()
    {
        $wardens = User::where('role', 'warden')->paginate(10);
        return view('admin.wardens.index', compact('wardens'));
    }

    public function create()
    {
        return view('admin.wardens.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'latitude'  => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
        ]);

        $warden = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => 'warden',
            'status'   => 'off-duty',
            'latitude' => $request->latitude,
            'longitude'=> $request->longitude,
        ]);

        ActivityLog::create([
            'performed_by' => auth()->user()->name ?? 'System',
            'target'       => 'Warden: ' . $warden->name,
            'description'  => 'A new warden (' . $warden->name . ') was added by ' . (auth()->user()->name ?? 'System'),
        ]);

        return redirect()->route('wardens.index')->with('success', 'Warden added successfully.');
    }
}
