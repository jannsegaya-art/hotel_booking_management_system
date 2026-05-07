<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RevenueController extends Controller
{
    public function index(Request $request)
    {
        $period = $request->get('period', 'monthly');
        $year   = $request->get('year', date('Y'));
        $month  = $request->get('month', date('m'));

        $paidBookings = Booking::where('payment_status', 'paid');

        $dailyRevenue   = (clone $paidBookings)
            ->whereYear('created_at', $year)->whereMonth('created_at', $month)
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(total_amount) as total'), DB::raw('COUNT(*) as count'))
            ->groupBy('date')->orderBy('date')->get();

        $weeklyRevenue  = (clone $paidBookings)
            ->whereYear('created_at', $year)
            ->select(DB::raw('WEEK(created_at) as week'), DB::raw('SUM(total_amount) as total'), DB::raw('COUNT(*) as count'))
            ->groupBy('week')->orderBy('week')->get();

        $monthlyRevenue = (clone $paidBookings)
            ->whereYear('created_at', $year)
            ->select(DB::raw('MONTH(created_at) as month'), DB::raw('SUM(total_amount) as total'), DB::raw('COUNT(*) as count'))
            ->groupBy('month')->orderBy('month')->get();

        $revenueByRoom = Booking::where('payment_status', 'paid')
            ->with('room')
            ->select('room_id', DB::raw('SUM(total_amount) as total'), DB::raw('COUNT(*) as count'))
            ->groupBy('room_id')->orderByDesc('total')->get();

        $totalRevenue      = $paidBookings->sum('total_amount');
        $totalThisMonth    = (clone $paidBookings)->whereYear('created_at', $year)->whereMonth('created_at', $month)->sum('total_amount');
        $totalThisWeek     = (clone $paidBookings)->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->sum('total_amount');
        $totalToday        = (clone $paidBookings)->whereDate('created_at', today())->sum('total_amount');

        // Monthly chart data
        $monthLabels = [];
        $monthData   = [];
        $monthlyKeyed = $monthlyRevenue->keyBy('month');
        for ($m = 1; $m <= 12; $m++) {
            $monthLabels[] = date('M', mktime(0, 0, 0, $m, 1));
            $monthData[]   = $monthlyKeyed->get($m)?->total ?? 0;
        }

        return view('admin.revenue', compact(
            'period', 'year', 'month',
            'dailyRevenue', 'weeklyRevenue', 'monthlyRevenue',
            'revenueByRoom', 'totalRevenue', 'totalThisMonth',
            'totalThisWeek', 'totalToday',
            'monthLabels', 'monthData'
        ));
    }
}
