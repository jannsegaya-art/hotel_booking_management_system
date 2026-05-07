<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Booking;
use App\Models\Notification;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    /**
     * My assigned bookings list
     */
    public function index(Request $request)
    {
        $query = Booking::where('staff_id', auth()->id())->with(['user', 'room']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $bookings = $query->latest()->paginate(15);
        return view('staff.bookings.index', compact('bookings'));
    }

    /**
     * View booking details
     */
    public function show(Booking $booking)
    {
        if ($booking->staff_id !== auth()->id()) {
            abort(403, 'You are not assigned to this booking.');
        }
        $booking->load(['user', 'room', 'rating']);
        return view('staff.bookings.show', compact('booking'));
    }

    /**
     * Show edit form — booking status + payment status
     */
    public function edit(Booking $booking)
    {
        if ($booking->staff_id !== auth()->id()) {
            abort(403, 'You are not assigned to this booking.');
        }
        $booking->load(['user', 'room']);
        return view('staff.bookings.edit', compact('booking'));
    }

    /**
     * Update booking status AND payment status
     */
    public function update(Request $request, Booking $booking)
    {
        if ($booking->staff_id !== auth()->id()) {
            abort(403, 'You are not assigned to this booking.');
        }

        $request->validate([
            'status'         => 'required|in:confirmed,checked_in,checked_out,cancelled',
            'payment_status' => 'required|in:unpaid,paid,refunded',
            'notes'          => 'nullable|string|max:500',
        ]);

        $oldStatus  = $booking->status;
        $oldPayment = $booking->payment_status;

        $booking->update([
            'status'         => $request->status,
            'payment_status' => $request->payment_status,
            'notes'          => $request->notes,
        ]);

        // Update room status based on booking status
        if ($request->status === 'checked_in') {
            $booking->room->update(['status' => 'occupied']);
        } elseif (in_array($request->status, ['checked_out', 'cancelled'])) {
            $booking->room->update(['status' => 'available']);
        }

        // Notify the guest
        Notification::create([
            'user_id' => $booking->user_id,
            'title'   => 'Booking Updated',
            'message' => "Your booking #{$booking->booking_reference} has been updated. " .
                         "Status: " . ucfirst(str_replace('_', ' ', $request->status)) . " | " .
                         "Payment: " . ucfirst($request->payment_status),
            'type'    => 'info',
        ]);

        // Log the action
        ActivityLog::create([
            'user_id'     => auth()->id(),
            'action'      => 'booking_update',
            'description' => "Staff updated booking {$booking->booking_reference}: " .
                             "status {$oldStatus}→{$request->status}, " .
                             "payment {$oldPayment}→{$request->payment_status}",
            'ip_address'  => $request->ip(),
        ]);

        return redirect()
            ->route('staff.bookings.index')
            ->with('success', "Booking #{$booking->booking_reference} updated successfully!");
    }

    /**
     * Quick status-only update (from show page)
     */
    public function updateStatus(Request $request, Booking $booking)
    {
        if ($booking->staff_id !== auth()->id()) {
            abort(403, 'You are not assigned to this booking.');
        }

        $request->validate([
            'status' => 'required|in:confirmed,checked_in,checked_out,cancelled',
        ]);

        $oldStatus = $booking->status;
        $booking->update(['status' => $request->status]);

        if ($request->status === 'checked_in') {
            $booking->room->update(['status' => 'occupied']);
        } elseif (in_array($request->status, ['checked_out', 'cancelled'])) {
            $booking->room->update(['status' => 'available']);
        }

        Notification::create([
            'user_id' => $booking->user_id,
            'title'   => 'Booking Status Updated',
            'message' => "Your booking #{$booking->booking_reference} is now: " .
                         ucfirst(str_replace('_', ' ', $request->status)),
            'type'    => 'info',
        ]);

        ActivityLog::create([
            'user_id'     => auth()->id(),
            'action'      => 'booking_status_update',
            'description' => "Staff updated booking {$booking->booking_reference} from {$oldStatus} to {$request->status}",
            'ip_address'  => $request->ip(),
        ]);

        return back()->with('success', 'Booking status updated successfully!');
    }

    /**
     * Delete a booking (only if pending or cancelled)
     */
    public function destroy(Booking $booking)
    {
        if ($booking->staff_id !== auth()->id()) {
            abort(403, 'You are not assigned to this booking.');
        }

        // Only allow deletion of pending or cancelled bookings
        if (!in_array($booking->status, ['pending', 'cancelled'])) {
            return back()->with('error', 'Only pending or cancelled bookings can be deleted.');
        }

        $ref = $booking->booking_reference;

        // Free up the room
        $booking->room->update(['status' => 'available']);

        // Notify the guest
        Notification::create([
            'user_id' => $booking->user_id,
            'title'   => 'Booking Deleted',
            'message' => "Your booking #{$ref} has been removed by the hotel staff.",
            'type'    => 'warning',
        ]);

        ActivityLog::create([
            'user_id'     => auth()->id(),
            'action'      => 'booking_delete',
            'description' => "Staff deleted booking {$ref}",
            'ip_address'  => request()->ip(),
        ]);

        $booking->delete();

        return redirect()
            ->route('staff.bookings.index')
            ->with('success', "Booking #{$ref} has been deleted.");
    }

    /**
     * All hotel bookings (read-only view)
     */
    public function allBookings(Request $request)
    {
        $query = Booking::with(['user', 'room', 'staff']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $bookings = $query->latest()->paginate(15);
        return view('staff.bookings.all', compact('bookings'));
    }
}