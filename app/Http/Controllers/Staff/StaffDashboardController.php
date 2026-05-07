<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Booking;
use App\Models\Notification;
use App\Models\Room;

class StaffDashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $myBookings = Booking::where('staff_id', $user->id)
            ->with(['user', 'room'])
            ->latest()
            ->take(10)
            ->get();

        $stats = [
            'assigned'    => Booking::where('staff_id', $user->id)->count(),
            'pending'     => Booking::where('staff_id', $user->id)->where('status', 'pending')->count(),
            'confirmed'   => Booking::where('staff_id', $user->id)->where('status', 'confirmed')->count(),
            'checked_in'  => Booking::where('staff_id', $user->id)->where('status', 'checked_in')->count(),
            'checked_out' => Booking::where('staff_id', $user->id)->where('status', 'checked_out')->count(),
        ];

        $available_rooms = Room::where('status', 'available')->count();
        $occupied_rooms  = Room::where('status', 'occupied')->count();

        $notifications = Notification::where('user_id', $user->id)
            ->where('is_read', false)
            ->latest()
            ->take(5)
            ->get();

        $recentLogs = ActivityLog::where('user_id', $user->id)
            ->latest()
            ->take(10)
            ->get();

        return view('staff.dashboard', compact(
            'myBookings', 'stats', 'available_rooms',
            'occupied_rooms', 'notifications', 'recentLogs'
        ));
    }
}
