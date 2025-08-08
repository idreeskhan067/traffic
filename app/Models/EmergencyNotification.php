<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmergencyNotification extends Model
{
    protected $fillable = ['title', 'message', 'priority', 'notified_at'];
}
