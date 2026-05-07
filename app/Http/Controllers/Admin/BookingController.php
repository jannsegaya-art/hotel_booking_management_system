<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Booking;
use App\Models\Notification;
use App\Models\Room;
use App\Models\User;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $query = Booking::with(['user', 'room', 'staff']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('booking_reference', 'like', "%$s%")
                  ->orWhereHas('user', fn($u) => $u->where('name', 'like', "%$s%"));
            });
        }

        $bookings = $query->latest()->paginate(15);
        return view('admin.bookings.index', compact('bookings'));
    }

    public function create()
    {
        $rooms     = Room::where('status', 'available')->get();
        $customers = User::where('role', 'customer')->where('status', 'active')->get();
        $staff     = User::where('role', 'staff')->where('status', 'active')->get();
        return view('admin.bookings.create', compact('rooms', 'customers', 'staff'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id'          => 'required|exists:users,id',
            'room_id'          => 'required|exists:rooms,id',
            'check_in_date'    => 'required|date|after_or_equal:today',
            'check_out_date'   => 'required|date|after:check_in_date',
            'guests'           => 'required|integer|min:1',
            'special_requests' => 'nullable|string|max:1000',
        ]);

        $room   = Room::findOrFail($request->room_id);
        $nights = (int) ((strtotime($request->check_out_date) - strtotime($request->check_in_date)) / 86400);
        $total  = $room->price_per_night * $nights;

        $conflict = Booking::where('room_id', $request->room_id)
            ->whereIn('status', ['confirmed', 'checked_in'])
            ->where(function ($q) use ($request) {
                $q->whereBetween('check_in_date', [$request->check_in_date, $request->check_out_date])
                  ->orWhereBetween('check_out_date', [$request->check_in_date, $request->check_out_date]);
            })->exists();

        if ($conflict) {
            return back()->withErrors(['room_id' => 'Room is not available for the selected dates.'])->withInput();
        }

        $booking = Booking::create([
            'booking_reference' => Booking::generateReference(),
            'user_id'           => $request->user_id,
            'room_id'           => $request->room_id,
            'staff_id'          => $request->staff_id ?: null,
            'check_in_date'     => $request->check_in_date,
            'check_out_date'    => $request->check_out_date,
            'guests'            => $request->guests,
            'total_amount'      => $total,
            'status'            => 'confirmed',
            'payment_status'    => $request->payment_status ?? 'unpaid',
            'special_requests'  => $request->special_requests,
        ]);

        $room->update(['status' => 'occupied']);

        Notification::create([
            'user_id' => $request->user_id,
            'title'   => 'Booking Confirmed!',
            'message' => "Your booking #{$booking->booking_reference} for Room {$room->room_number} has been confirmed.",
            'type'    => 'success',
        ]);

        ActivityLog::create([
            'user_id'     => auth()->id(),
            'action'      => 'booking_create',
            'description' => 'Admin created booking ' . $booking->booking_reference,
            'ip_address'  => $request->ip(),
        ]);

        return redirect()->route('admin.bookings.index')->with('success', 'Booking created successfully!');
    }

    public function show(Booking $booking)
    {
        $booking->load(['user', 'room', 'staff', 'rating']);
        $staff = User::where('role', 'staff')->where('status', 'active')->get();
        return view('admin.bookings.show', compact('booking', 'staff'));
    }

    public function edit(Booking $booking)
    {
        $rooms     = Room::all();
        $customers = User::where('role', 'customer')->get();
        $staff     = User::where('role', 'staff')->where('status', 'active')->get();
        return view('admin.bookings.edit', compact('booking', 'rooms', 'customers', 'staff'));
    }

    public function update(Request $request, Booking $booking)
    {
        $request->validate([
            'status'         => 'required|in:pending,confirmed,checked_in,checked_out,cancelled',
            'payment_status' => 'required|in:unpaid,paid,refunded',
            'staff_id'       => 'nullable|exists:users,id',
            'notes'          => 'nullable|string|max:1000',
        ]);

        $oldStatus = $booking->status;
        $booking->update($request->only(['status', 'payment_status', 'staff_id', 'notes']));

        if ($request->status === 'checked_in') {
            $booking->room->update(['status' => 'occupied']);
        } elseif (in_array($request->status, ['checked_out', 'cancelled'])) {
            $booking->room->update(['status' => 'available']);
        }

        Notification::create([
            'user_id' => $booking->user_id,
            'title'   => 'Booking Updated',
            'message' => "Your booking #{$booking->booking_reference} status is now: " . ucfirst(str_replace('_', ' ', $request->status)),
            'type'    => 'info',
        ]);

        ActivityLog::create([
            'user_id'     => auth()->id(),
            'action'      => 'booking_update',
            'description' => "Booking {$booking->booking_reference} updated from {$oldStatus} to {$request->status}",
            'ip_address'  => $request->ip(),
        ]);

        return redirect()->route('admin.bookings.index')->with('success', 'Booking updated successfully!');
    }

    public function destroy(Booking $booking)
    {
        $ref = $booking->booking_reference;
        $booking->room->update(['status' => 'available']);
        $booking->delete();

        ActivityLog::create([
            'user_id'     => auth()->id(),
            'action'      => 'booking_delete',
            'description' => "Booking {$ref} deleted.",
            'ip_address'  => request()->ip(),
        ]);

        return redirect()->route('admin.bookings.index')->with('success', 'Booking deleted successfully!');
    }

    public function checkAvailability(Request $request)
    {
        $availableRooms = collect();

        if ($request->filled('check_in_date') && $request->filled('check_out_date')) {
            $request->validate([
                'check_in_date'  => 'required|date',
                'check_out_date' => 'required|date|after:check_in_date',
            ]);

            $unavailableRoomIds = Booking::whereIn('status', ['confirmed', 'checked_in'])
                ->where(function ($q) use ($request) {
                    $q->whereBetween('check_in_date', [$request->check_in_date, $request->check_out_date])
                      ->orWhereBetween('check_out_date', [$request->check_in_date, $request->check_out_date])
                      ->orWhere(function ($q2) use ($request) {
                          $q2->where('check_in_date', '<=', $request->check_in_date)
                             ->where('check_out_date', '>=', $request->check_out_date);
                      });
                })->pluck('room_id');

            $availableRooms = Room::whereNotIn('id', $unavailableRoomIds)
                ->where('status', '!=', 'maintenance')
                ->get();
        }

        return view('admin.bookings.availability', compact('availableRooms', 'request'));
    }
}
