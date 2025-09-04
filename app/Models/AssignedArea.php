<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssignedArea extends Model
{
    use HasFactory;

    protected $fillable = [
        'warden_id',
        'name',
        'description',
        'boundaries',
        'status',
        'assigned_by',
        'assigned_at'
    ];

    protected $casts = [
        'boundaries' => 'array',
        'assigned_at' => 'datetime',
    ];

    public function warden()
    {
        return $this->belongsTo(User::class, 'warden_id');
    }

    public function assigner()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }
}