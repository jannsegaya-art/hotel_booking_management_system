<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'address',
        'profile_photo',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    // ── Relationships ─────────────────────────────────────────────

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function assignedBookings()
    {
        return $this->hasMany(Booking::class, 'staff_id');
    }

    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }

    // ── Role helpers ─────────────────────────────────────────────

    public function isAdmin(): bool    { return $this->role === 'admin'; }
    public function isStaff(): bool    { return $this->role === 'staff'; }
    public function isCustomer(): bool { return $this->role === 'customer'; }

    // ── Profile photo URL ────────────────────────────────────────

    /**
     * Returns a working URL for the profile photo.
     *
     * Photos are stored in:  public/uploads/profiles/filename.jpg
     * So the web URL is:     http://127.0.0.1:8000/uploads/profiles/filename.jpg
     *
     * No storage:link command needed. Works on XAMPP out of the box.
     */
    public function getProfilePhotoUrlAttribute(): string
    {
        if ($this->profile_photo) {
            // Check if the file actually exists on disk
            $fullPath = public_path($this->profile_photo);
            if (file_exists($fullPath)) {
                return asset($this->profile_photo);
            }
        }

        // Fallback: auto-generated avatar with initials
        $name = urlencode($this->name);
        return "https://ui-avatars.com/api/?name={$name}&background=1a3c8f&color=fff&size=128&bold=true";
    }
}
