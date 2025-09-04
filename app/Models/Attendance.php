<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'status', 
        'timestamp',
        'date',
        'check_in_time',
        'check_out_time'
    ];

    protected $casts = [
        'timestamp' => 'datetime',
        'check_in_time' => 'datetime',
        'check_out_time' => 'datetime',
        'date' => 'date'
    ];

    // 👇 Add this relationship
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
