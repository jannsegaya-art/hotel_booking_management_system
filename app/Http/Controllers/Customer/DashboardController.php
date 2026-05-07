<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Notification;
use App\Models\Room;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $stats = [
            'total'      => Booking::where('user_id', $user->id)->count(),
            'pending'    => Booking::where('user_id', $user->id)->where('status', 'pending')->count(),
            'confirmed'  => Booking::where('user_id', $user->id)->where('status', 'confirmed')->count(),
            'checked_in' => Booking::where('user_id', $user->id)->where('status', 'checked_in')->count(),
            'completed'  => Booking::where('user_id', $user->id)->where('status', 'checked_out')->count(),
            'cancelled'  => Booking::where('user_id', $user->id)->where('status', 'cancelled')->count(),
        ];

        $recent_bookings = Booking::where('user_id', $user->id)
            ->with('room')
            ->latest()
            ->take(5)
            ->get();

        $notifications = Notification::where('user_id', $user->id)
            ->where('is_read', false)
            ->latest()
            ->take(5)
            ->get();

        $featured_rooms = Room::where('status', 'available')->take(4)->get();

        return view('customer.dashboard', compact(
            'stats', 'recent_bookings', 'notifications', 'featured_rooms'
        ));
    }
}
