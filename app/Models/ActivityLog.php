<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    // Table name (optional if your table is named 'activity_logs')
    protected $table = 'activity_logs';

    // Allow mass assignment for these fields
    protected $fillable = [
        'action',
        'performed_by',
        'target',
        'description',
    ];

    /**
     * Get a formatted timestamp for activity.
     */
    public function getFormattedDateAttribute()
    {
        return $this->created_at->format('d M Y, h:i A');
    }
}
