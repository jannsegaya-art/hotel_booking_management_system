<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Booking;
use App\Models\Notification;
use App\Models\Rating;
use App\Models\Room;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function index()
    {
        $bookings = Booking::where('user_id', auth()->id())
            ->with('room')
            ->latest()
            ->paginate(10);

        return view('customer.bookings.index', compact('bookings'));
    }

    public function create(Request $request)
    {
        $room  = null;
        if ($request->filled('room_id')) {
            $room = Room::findOrFail($request->room_id);
        }
        $rooms = Room::where('status', 'available')->get();
        return view('customer.bookings.create', compact('rooms', 'room'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'room_id'          => 'required|exists:rooms,id',
            'check_in_date'    => 'required|date|after_or_equal:today',
            'check_out_date'   => 'required|date|after:check_in_date',
            'guests'           => 'required|integer|min:1|max:10',
            'special_requests' => 'nullable|string|max:1000',
        ]);

        $room = Room::findOrFail($request->room_id);

        if ($room->status !== 'available') {
            return back()->withErrors(['room_id' => 'This room is not currently available.'])->withInput();
        }

        $conflict = Booking::where('room_id', $request->room_id)
            ->whereIn('status', ['confirmed', 'checked_in'])
            ->where(function ($q) use ($request) {
                $q->whereBetween('check_in_date', [$request->check_in_date, $request->check_out_date])
                  ->orWhereBetween('check_out_date', [$request->check_in_date, $request->check_out_date])
                  ->orWhere(function ($q2) use ($request) {
                      $q2->where('check_in_date', '<=', $request->check_in_date)
                         ->where('check_out_date', '>=', $request->check_out_date);
                  });
            })->exists();

        if ($conflict) {
            return back()->withErrors(['room_id' => 'Room is not available for selected dates.'])->withInput();
        }

        $nights = (int) ((strtotime($request->check_out_date) - strtotime($request->check_in_date)) / 86400);
        $total  = $room->price_per_night * $nights;

        $booking = Booking::create([
            'booking_reference' => Booking::generateReference(),
            'user_id'           => auth()->id(),
            'room_id'           => $request->room_id,
            'check_in_date'     => $request->check_in_date,
            'check_out_date'    => $request->check_out_date,
            'guests'            => $request->guests,
            'total_amount'      => $total,
            'status'            => 'pending',
            'payment_status'    => 'unpaid',
            'special_requests'  => $request->special_requests,
        ]);

        Notification::create([
            'user_id' => auth()->id(),
            'title'   => 'Booking Received!',
            'message' => "Your booking #{$booking->booking_reference} for Room {$room->room_number} is pending confirmation.",
            'type'    => 'info',
        ]);

        ActivityLog::create([
            'user_id'     => auth()->id(),
            'action'      => 'booking_create',
            'description' => auth()->user()->name . " booked Room {$room->room_number}. Ref: {$booking->booking_reference}",
            'ip_address'  => $request->ip(),
        ]);

        return redirect()->route('customer.bookings.index')
            ->with('success', "Booking #{$booking->booking_reference} submitted! Awaiting confirmation.");
    }

    public function show(Booking $booking)
    {
        if ($booking->user_id !== auth()->id()) {
            abort(403, 'This booking does not belong to you.');
        }
        $booking->load(['room', 'staff', 'rating']);
        return view('customer.bookings.show', compact('booking'));
    }

    public function cancel(Booking $booking)
    {
        if ($booking->user_id !== auth()->id()) {
            abort(403);
        }

        if (! in_array($booking->status, ['pending', 'confirmed'])) {
            return back()->with('error', 'This booking cannot be cancelled at this stage.');
        }

        $booking->update(['status' => 'cancelled']);
        $booking->room->update(['status' => 'available']);

        Notification::create([
            'user_id' => auth()->id(),
            'title'   => 'Booking Cancelled',
            'message' => "Your booking #{$booking->booking_reference} has been cancelled.",
            'type'    => 'warning',
        ]);

        ActivityLog::create([
            'user_id'     => auth()->id(),
            'action'      => 'booking_cancel',
            'description' => auth()->user()->name . " cancelled booking {$booking->booking_reference}",
            'ip_address'  => request()->ip(),
        ]);

        return redirect()->route('customer.bookings.index')
            ->with('success', 'Booking cancelled successfully.');
    }

    public function rate(Request $request, Booking $booking)
    {
        if ($booking->user_id !== auth()->id() || $booking->status !== 'checked_out') {
            abort(403);
        }

        $request->validate([
            'rating'  => 'required|integer|between:1,5',
            'comment' => 'nullable|string|max:1000',
        ]);

        Rating::updateOrCreate(
            ['booking_id' => $booking->id],
            [
                'user_id' => auth()->id(),
                'room_id' => $booking->room_id,
                'rating'  => $request->rating,
                'comment' => $request->comment,
            ]
        );

        return back()->with('success', 'Thank you for your feedback!');
    }
}
