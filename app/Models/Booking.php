<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_reference',
        'user_id',
        'room_id',
        'staff_id',
        'check_in_date',
        'check_out_date',
        'guests',
        'total_amount',
        'status',
        'payment_status',
        'special_requests',
        'notes',
    ];

    protected $casts = [
        'check_in_date'  => 'date',
        'check_out_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function staff()
    {
        return $this->belongsTo(User::class, 'staff_id');
    }

    public function rating()
    {
        return $this->hasOne(Rating::class);
    }

    public function getNightsAttribute(): int
    {
        return $this->check_in_date->diffInDays($this->check_out_date);
    }

    public static function generateReference(): string
    {
        do {
            $ref = 'HB-' . strtoupper(substr(uniqid(), -6));
        } while (static::where('booking_reference', $ref)->exists());

        return $ref;
    }
}
