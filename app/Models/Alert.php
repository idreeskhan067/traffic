<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Alert extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'message',
        'latitude',
        'longitude',
    ];

    // Relation → each alert belongs to a user (warden)
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
