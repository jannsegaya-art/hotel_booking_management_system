<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_number',
        'room_type',
        'description',
        'price_per_night',
        'capacity',
        'status',
        'image',
        'amenities',
        'floor',
    ];

    protected $casts = [
        'amenities' => 'array',
    ];

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }

    public function getAverageRatingAttribute(): float
    {
        return $this->ratings()->avg('rating') ?? 0;
    }
}
