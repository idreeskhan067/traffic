<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    // Tell Laravel to use the 'activity_logs' table instead of 'activities'
    protected $table = 'activity_logs';

    protected $fillable = [
        'user_id',
        'action',       // e.g., "Reported congestion", "Dispatched unit"
        'details',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
