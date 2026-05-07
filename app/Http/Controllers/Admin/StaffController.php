<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Booking;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class StaffController extends Controller
{
    public function index(Request $request)
    {
        $query = User::where('role', 'staff');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q) => $q->where('name', 'like', "%$s%")->orWhere('email', 'like', "%$s%"));
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $staff = $query->latest()->paginate(15);
        return view('admin.staff.index', compact('staff'));
    }

    public function create()
    {
        return view('admin.staff.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
            'phone'    => 'nullable|string|max:20',
            'address'  => 'nullable|string|max:500',
            'status'   => 'required|in:active,inactive',
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => 'staff',
            'phone'    => $request->phone,
            'address'  => $request->address,
            'status'   => $request->status,
        ]);

        Notification::create([
            'user_id' => $user->id,
            'title'   => 'Account Created',
            'message' => 'Your staff account has been created by the administrator. Welcome to the team!',
            'type'    => 'success',
        ]);

        ActivityLog::create([
            'user_id'     => auth()->id(),
            'action'      => 'create_staff',
            'description' => auth()->user()->name . ' created staff account for ' . $user->name,
            'ip_address'  => $request->ip(),
        ]);

        return redirect()->route('admin.staff.index')->with('success', 'Staff member added successfully!');
    }

    public function show(User $user)
    {
        abort_if($user->role !== 'staff', 404);
        $bookings = Booking::where('staff_id', $user->id)->with(['user', 'room'])->latest()->get();
        return view('admin.staff.show', compact('user', 'bookings'));
    }

    public function edit(User $user)
    {
        abort_if($user->role !== 'staff', 404);
        return view('admin.staff.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        abort_if($user->role !== 'staff', 404);

        $request->validate([
            'name'    => 'required|string|max:255',
            'phone'   => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'status'  => 'required|in:active,inactive,pending',
        ]);

        $user->update([
            'name'    => $request->name,
            'phone'   => $request->phone,
            'address' => $request->address,
            'status'  => $request->status,
        ]);

        if ($request->filled('password')) {
            $request->validate(['password' => 'min:8|confirmed']);
            $user->update(['password' => Hash::make($request->password)]);
        }

        ActivityLog::create([
            'user_id'     => auth()->id(),
            'action'      => 'update_staff',
            'description' => auth()->user()->name . ' updated staff: ' . $user->name,
            'ip_address'  => $request->ip(),
        ]);

        return redirect()->route('admin.staff.index')->with('success', 'Staff member updated successfully!');
    }

    public function destroy(User $user)
    {
        abort_if($user->role !== 'staff', 404);
        $name = $user->name;
        $user->delete();

        ActivityLog::create([
            'user_id'     => auth()->id(),
            'action'      => 'delete_staff',
            'description' => auth()->user()->name . ' deleted staff: ' . $name,
            'ip_address'  => request()->ip(),
        ]);

        return redirect()->route('admin.staff.index')->with('success', 'Staff member deleted.');
    }

    public function approve(User $user)
    {
        abort_if($user->role !== 'staff', 404);
        $user->update(['status' => 'active']);

        Notification::create([
            'user_id' => $user->id,
            'title'   => 'Account Approved!',
            'message' => 'Your staff account has been approved. You can now log in.',
            'type'    => 'success',
        ]);

        ActivityLog::create([
            'user_id'     => auth()->id(),
            'action'      => 'approve_staff',
            'description' => auth()->user()->name . ' approved staff: ' . $user->name,
            'ip_address'  => request()->ip(),
        ]);

        return back()->with('success', $user->name . "'s account has been approved!");
    }

    public function reject(User $user)
    {
        abort_if($user->role !== 'staff', 404);
        $user->update(['status' => 'inactive']);

        Notification::create([
            'user_id' => $user->id,
            'title'   => 'Account Rejected',
            'message' => 'Your staff registration has been rejected. Please contact the administrator.',
            'type'    => 'danger',
        ]);

        ActivityLog::create([
            'user_id'     => auth()->id(),
            'action'      => 'reject_staff',
            'description' => auth()->user()->name . ' rejected staff: ' . $user->name,
            'ip_address'  => request()->ip(),
        ]);

        return back()->with('success', $user->name . "'s account has been rejected.");
    }

    public function toggleStatus(User $user)
    {
        abort_if($user->role !== 'staff', 404);
        $newStatus = $user->status === 'active' ? 'inactive' : 'active';
        $user->update(['status' => $newStatus]);

        Notification::create([
            'user_id' => $user->id,
            'title'   => 'Account ' . ($newStatus === 'active' ? 'Activated' : 'Deactivated'),
            'message' => 'Your account has been ' . ($newStatus === 'active' ? 'activated' : 'deactivated') . ' by admin.',
            'type'    => $newStatus === 'active' ? 'success' : 'warning',
        ]);

        ActivityLog::create([
            'user_id'     => auth()->id(),
            'action'      => 'toggle_staff_status',
            'description' => auth()->user()->name . ' changed staff status to ' . $newStatus . ': ' . $user->name,
            'ip_address'  => request()->ip(),
        ]);

        $msg = $newStatus === 'active' ? 'activated' : 'deactivated';
        return back()->with('success', $user->name . "'s account has been {$msg}!");
    }
}
