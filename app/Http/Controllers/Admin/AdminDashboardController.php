<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Booking;
use App\Models\Notification;
use App\Models\Room;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $totalStaff      = User::where('role', 'staff')->count();
        $totalCustomers  = User::where('role', 'customer')->count();
        $totalBookings   = Booking::count();
        $availableRooms  = Room::where('status', 'available')->count();
        $occupiedRooms   = Room::where('status', 'occupied')->count();
        $totalRooms      = Room::count();
        $totalRevenue    = Booking::where('payment_status', 'paid')->sum('total_amount');
        $pendingBookings = Booking::where('status', 'pending')->count();
        $pendingStaff    = User::where('role', 'staff')->where('status', 'pending')->count();

        $recentBookings = Booking::with(['user', 'room'])->latest()->take(8)->get();

        $monthlyRevenue = Booking::where('payment_status', 'paid')
            ->whereYear('created_at', now()->year)
            ->select(DB::raw('MONTH(created_at) as month'), DB::raw('SUM(total_amount) as revenue'))
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->keyBy('month');

        $revenueLabels = [];
        $revenueData   = [];
        for ($m = 1; $m <= 12; $m++) {
            $revenueLabels[] = date('M', mktime(0, 0, 0, $m, 1));
            $revenueData[]   = $monthlyRevenue->get($m)?->revenue ?? 0;
        }

        $bookingStatusData = [
            Booking::where('status', 'pending')->count(),
            Booking::where('status', 'confirmed')->count(),
            Booking::where('status', 'checked_in')->count(),
            Booking::where('status', 'checked_out')->count(),
            Booking::where('status', 'cancelled')->count(),
        ];

        $notifications = Notification::where('user_id', auth()->id())
            ->where('is_read', false)
            ->latest()
            ->take(5)
            ->get();

        $recentLogs = ActivityLog::with('user')->latest()->take(10)->get();

        return view('admin.dashboard', compact(
            'totalStaff', 'totalCustomers', 'totalBookings',
            'availableRooms', 'occupiedRooms', 'totalRooms',
            'totalRevenue', 'pendingBookings', 'pendingStaff',
            'recentBookings', 'revenueData', 'revenueLabels',
            'bookingStatusData', 'notifications', 'recentLogs'
        ));
    }
}
