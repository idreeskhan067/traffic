<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dispatch extends Model
{
    protected $fillable = [
        'congestion_id',
        'officer_id',
        'team_id',
        'status',
        'dispatched_at',
        'notes',
    ];

    public function congestion()
    {
        return $this->belongsTo(Congestion::class);
    }

    public function officer()
    {
        return $this->belongsTo(User::class, 'officer_id');
    }

    public function team()
    {
        return $this->belongsTo(Squad::class, 'team_id');
    }
}
