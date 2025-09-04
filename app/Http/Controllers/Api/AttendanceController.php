<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Attendance;

class AttendanceController extends Controller
{
    /**
     * Mark attendance check-in
     */
    public function checkIn(Request $request)
    {
        try {
            $user = $request->user();

            // check if already checked in today
            $existing = Attendance::where('user_id', $user->id)
                ->whereDate('date', now()->toDateString())
                ->first();

            if ($existing && $existing->check_in_time) {
                return response()->json([
                    'success'   => false,
                    'message'   => 'Already checked in today',
                    'attendance'=> $existing
                ], 400);
            }

            // create or update record
            $attendance = Attendance::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'date'    => now()->toDateString(),
                ],
                [
                    'status'        => 'in',
                    'timestamp'     => now(),
                    'check_in_time' => now(),
                ]
            );

            return response()->json([
                'success'    => true,
                'message'    => 'Checked in successfully',
                'attendance' => $attendance->load('user'),
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to check in',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mark attendance check-out
     */
    public function checkOut(Request $request)
    {
        try {
            $user = $request->user();

            // find today's check-in
            $attendance = Attendance::where('user_id', $user->id)
                ->whereDate('date', now()->toDateString())
                ->first();

            if (!$attendance || !$attendance->check_in_time) {
                return response()->json([
                    'success' => false,
                    'message' => 'No check-in record found for today',
                ], 404);
            }

            // prevent double checkout
            if ($attendance->check_out_time) {
                return response()->json([
                    'success' => false,
                    'message' => 'Already checked out today',
                ], 400);
            }

            $attendance->update([
                'check_out_time' => now(),
                'status'         => 'out',
            ]);

            return response()->json([
                'success'    => true,
                'message'    => 'Checked out successfully',
                'attendance' => $attendance->load('user'),
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to check out',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get today's attendance status
     */
    public function todayStatus(Request $request)
    {
        $user = $request->user();

        $attendance = Attendance::where('user_id', $user->id)
            ->whereDate('date', now()->toDateString())
            ->first();

        if ($attendance) {
            return response()->json([
                'success'    => true,
                'status'     => $attendance->status, // "in" or "out"
                'attendance' => $attendance->load('user'),
            ], 200);
        }

        return response()->json([
            'success' => true,
            'status'  => 'none', // not checked in today
        ], 200);
    }
}
