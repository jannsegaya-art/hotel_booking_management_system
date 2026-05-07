<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Booking;
use App\Models\Room;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    public function index(Request $request)
    {
        $query = Room::query();
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q) => $q->where('room_number','like',"%$s%")->orWhere('room_type','like',"%$s%"));
        }
        if ($request->filled('status'))  $query->where('status',    $request->status);
        if ($request->filled('type'))    $query->where('room_type',  $request->type);

        $rooms     = $query->latest()->paginate(15);
        $roomTypes = Room::distinct()->pluck('room_type');
        return view('admin.rooms.index', compact('rooms', 'roomTypes'));
    }

    public function create()
    {
        return view('admin.rooms.form');
    }

    public function store(Request $request)
    {
        $request->validate([
            'room_number'     => 'required|unique:rooms,room_number',
            'room_type'       => 'required|string|max:100',
            'description'     => 'nullable|string',
            'price_per_night' => 'required|numeric|min:1',
            'capacity'        => 'required|integer|min:1|max:20',
            'floor'           => 'required|integer|min:1',
            'status'          => 'required|in:available,occupied,maintenance',
            'image'           => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'amenities'       => 'nullable|array',
        ]);

        $data             = $request->except(['image', 'amenities', '_token']);
        $data['amenities'] = $request->amenities ?? [];

        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $data['image'] = $this->uploadRoomImage($request->file('image'));
        }

        $room = Room::create($data);

        ActivityLog::create([
            'user_id'     => auth()->id(),
            'action'      => 'create_room',
            'description' => auth()->user()->name . ' created room ' . $room->room_number,
            'ip_address'  => $request->ip(),
        ]);

        return redirect()->route('admin.rooms.index')->with('success', 'Room created successfully!');
    }

    public function show(Room $room)
    {
        return view('admin.rooms.form', compact('room'));
    }

    public function edit(Room $room)
    {
        return view('admin.rooms.form', compact('room'));
    }

    public function update(Request $request, Room $room)
    {
        $request->validate([
            'room_number'     => 'required|unique:rooms,room_number,' . $room->id,
            'room_type'       => 'required|string|max:100',
            'description'     => 'nullable|string',
            'price_per_night' => 'required|numeric|min:1',
            'capacity'        => 'required|integer|min:1|max:20',
            'floor'           => 'required|integer|min:1',
            'status'          => 'required|in:available,occupied,maintenance',
            'image'           => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'amenities'       => 'nullable|array',
        ]);

        $data             = $request->except(['image', 'amenities', '_token', '_method']);
        $data['amenities'] = $request->amenities ?? [];

        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            // Delete old image
            if ($room->image && file_exists(public_path($room->image))) {
                @unlink(public_path($room->image));
            }
            $data['image'] = $this->uploadRoomImage($request->file('image'));
        }

        // Handle remove image checkbox
        if ($request->boolean('remove_image') && $room->image) {
            if (file_exists(public_path($room->image))) {
                @unlink(public_path($room->image));
            }
            $data['image'] = null;
        }

        $room->update($data);

        ActivityLog::create([
            'user_id'     => auth()->id(),
            'action'      => 'update_room',
            'description' => auth()->user()->name . ' updated room ' . $room->room_number,
            'ip_address'  => $request->ip(),
        ]);

        return redirect()->route('admin.rooms.index')->with('success', 'Room updated successfully!');
    }

    public function destroy(Room $room)
    {
        if ($room->image && file_exists(public_path($room->image))) {
            @unlink(public_path($room->image));
        }

        ActivityLog::create([
            'user_id'     => auth()->id(),
            'action'      => 'delete_room',
            'description' => auth()->user()->name . ' deleted room ' . $room->room_number,
            'ip_address'  => request()->ip(),
        ]);

        $room->delete();
        return redirect()->route('admin.rooms.index')->with('success', 'Room deleted successfully!');
    }

    public function availability(Request $request)
    {
        $rooms = Room::query();
        if ($request->filled('check_in') && $request->filled('check_out')) {
            $bookedRoomIds = Booking::where('status', '!=', 'cancelled')
                ->where(fn($q) => $q
                    ->whereBetween('check_in_date',  [$request->check_in, $request->check_out])
                    ->orWhereBetween('check_out_date', [$request->check_in, $request->check_out])
                    ->orWhere(fn($q2) => $q2->where('check_in_date','<=',$request->check_in)->where('check_out_date','>=',$request->check_out))
                )->pluck('room_id');
            $rooms->whereNotIn('id', $bookedRoomIds);
        }
        $rooms = $rooms->get();
        return view('admin.rooms.availability', compact('rooms', 'request'));
    }

    // ── Helper ─────────────────────────────────────────────────────────

    private function uploadRoomImage($file): string
    {
        $dir = public_path('uploads/rooms');
        if (!file_exists($dir)) mkdir($dir, 0775, true);

        $filename = 'room_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $file->move($dir, $filename);

        return 'uploads/rooms/' . $filename;
    }
}
