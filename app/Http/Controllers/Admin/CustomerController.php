<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $query = User::where('role', 'customer');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q) => $q->where('name', 'like', "%$s%")->orWhere('email', 'like', "%$s%"));
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $customers = $query->withCount('bookings')->latest()->paginate(15);
        return view('admin.customers.index', compact('customers'));
    }

    public function show(User $user)
    {
        abort_if($user->role !== 'customer', 404);
        $bookings = $user->bookings()->with('room')->latest()->get();
        $ratings  = $user->ratings()->with('room')->latest()->get();
        return view('admin.customers.show', compact('user', 'bookings', 'ratings'));
    }

    public function toggleStatus(User $user)
    {
        abort_if($user->role !== 'customer', 404);
        $user->status = $user->status === 'active' ? 'inactive' : 'active';
        $user->save();

        ActivityLog::create([
            'user_id'     => auth()->id(),
            'action'      => 'customer_status_toggle',
            'description' => auth()->user()->name . " changed customer {$user->name} status to {$user->status}",
            'ip_address'  => request()->ip(),
        ]);

        $msg = $user->status === 'active' ? 'activated' : 'deactivated';
        return back()->with('success', "Customer account has been {$msg}.");
    }

    public function destroy(User $user)
    {
        abort_if($user->role !== 'customer', 404);
        $name = $user->name;
        $user->delete();

        ActivityLog::create([
            'user_id'     => auth()->id(),
            'action'      => 'customer_delete',
            'description' => auth()->user()->name . " deleted customer: {$name}",
            'ip_address'  => request()->ip(),
        ]);

        return redirect()->route('admin.customers.index')->with('success', 'Customer deleted successfully.');
    }
}
