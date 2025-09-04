<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'status',
        'last_active_at', // Added for on-duty tracking
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'last_active_at' => 'datetime', // Added for on-duty tracking
    ];

    // Helper method for on-duty status
    public function isOnDuty()
    {
        return $this->status === 'on-duty';
    }

    // Relationship with tasks assigned to the warden
    // NOTE: We changed this from 'warden_id' to 'assigned_to' in our migration
    public function tasks()
    {
        return $this->hasMany(Task::class, 'assigned_to');
    }

    // Get only pending tasks
    public function pendingTasks()
    {
        return $this->tasks()->whereIn('status', ['pending', 'in_progress']);
    }

    // Relationship with assigned areas (new direct relationship)
    public function assignedAreas()
    {
        return $this->hasMany(AssignedArea::class, 'warden_id');
    }

    // Keeping your existing areas relationship for backward compatibility
    public function areas()
    {
        return $this->belongsToMany(Area::class, 'area_user', 'user_id', 'area_id');
    }

    // Relationship with alerts (adjust based on your existing alert model)
    public function alerts()
    {
        return $this->hasMany(Alert::class, 'user_id');
    }

    // Get only unread alerts
    public function unreadAlerts()
    {
        return $this->belongsToMany(Alert::class, 'alert_recipients')
            ->wherePivot('read_at', null);
    }

    // Relationship with attendances
    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    // Relationship with location
    public function location()
    {
        return $this->hasOne(Location::class);
    }
}