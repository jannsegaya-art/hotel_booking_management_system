<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Booking;
use App\Models\Rating;
use App\Models\Room;
use App\Models\User;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->get('type', 'bookings');
        $from = $request->get('from');
        $to   = $request->get('to');

        switch ($type) {
            case 'staff':
                $data = User::where('role', 'staff')
                    ->withCount(['assignedBookings as total_bookings'])
                    ->with(['assignedBookings' => function ($q) use ($from, $to) {
                        if ($from) $q->whereDate('created_at', '>=', $from);
                        if ($to)   $q->whereDate('created_at', '<=', $to);
                    }])
                    ->get();
                break;

            case 'users':
                $data = User::where('role', 'customer')
                    ->withCount('bookings')
                    ->get();
                break;

            case 'revenue':
                $query = Booking::where('payment_status', 'paid')->with('room');
                if ($from) $query->whereDate('created_at', '>=', $from);
                if ($to)   $query->whereDate('created_at', '<=', $to);
                $data = $query->latest()->get();
                break;

            case 'occupancy':
                $data = Room::withCount(['bookings as total_bookings'])->get();
                break;

            case 'ratings':
                $data = Rating::with(['user', 'room', 'booking'])->latest()->get();
                break;

            default: // bookings
                $query = Booking::with(['user', 'room', 'staff']);
                if ($from) $query->whereDate('created_at', '>=', $from);
                if ($to)   $query->whereDate('created_at', '<=', $to);
                $data = $query->latest()->get();
        }

        return view('admin.reports', compact('data', 'type', 'from', 'to'));
    }

    public function logs(Request $request)
    {
        $query = ActivityLog::with('user');

        if ($request->filled('action'))  $query->where('action', $request->action);
        if ($request->filled('user_id')) $query->where('user_id', $request->user_id);
        if ($request->filled('from'))    $query->whereDate('created_at', '>=', $request->from);
        if ($request->filled('to'))      $query->whereDate('created_at', '<=', $request->to);

        $logs  = $query->latest()->paginate(20);
        $users = User::orderBy('name')->get();

        return view('admin.logs', compact('logs', 'users'));
    }

    public function ratings(Request $request)
    {
        $query = Rating::with(['user', 'room', 'booking']);

        if ($request->filled('room_id')) $query->where('room_id', $request->room_id);
        if ($request->filled('rating'))  $query->where('rating', $request->rating);

        $ratings = $query->latest()->paginate(15);
        $rooms   = Room::all();

        return view('admin.ratings', compact('ratings', 'rooms'));
    }
}
