<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Congestion extends Model
{
    protected $fillable = [
        'zone',
        'location',
        'status',       // e.g., reported, resolved
        'reported_by',  // user_id of warden or officer
        'description',
        'severity',     // e.g., low, medium, high
    ];

    // Relationships
    public function reporter()
    {
        return $this->belongsTo(User::class, 'reported_by');
    }
}
