<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Squad;

class DispatchController extends Controller
{
    public function dispatchSquad(Request $request)
    {
        $request->validate([
            'squad_id' => 'required|exists:squads,id',
            'zone' => 'required|string|max:255',
        ]);

        $squad = Squad::find($request->squad_id);
        $squad->dispatched_at = now();
        $squad->status = 'dispatched';
        $squad->assigned_zone = $request->zone;
        $squad->save();

        return redirect()->back()->with('success', 'Squad dispatched successfully.');
    }
}
