<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Shift extends Model
{
    protected $fillable = [
        'name',
        'start_time',
        'end_time',
    ];

    /**
     * Format the start time for display (e.g. 06:00 AM)
     */
    public function getFormattedStartTimeAttribute()
    {
        return Carbon::createFromFormat('H:i:s', $this->start_time)->format('g:i A');
    }

    /**
     * Format the end time for display (e.g. 02:00 PM)
     */
    public function getFormattedEndTimeAttribute()
    {
        return Carbon::createFromFormat('H:i:s', $this->end_time)->format('g:i A');
    }

    /**
     * Scope to get only active shifts (optional, if you add status later)
     */
    // public function scopeActive($query)
    // {
    //     return $query->where('status', 'active');
    // }
}
