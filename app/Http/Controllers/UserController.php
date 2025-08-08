<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    // Show the create user form
    public function create()
    {
        return view('admin.users.create');
    }

    // Handle the form submission and save user
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'required|string|in:admin,warden,employee',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'status' => 'off-duty', // default status
        ]);

        $user->assignRole($request->role);

        return redirect()->route('admin.users.create')->with('success', 'User created successfully!');
    }
}
