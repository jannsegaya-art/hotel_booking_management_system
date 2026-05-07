@extends('layouts.admin')
@section('title', 'Room Availability')

@section('content')
<div class="container-fluid py-4">
    <div class="mb-4">
        <a href="{{ route('admin.bookings.index') }}" class="btn btn-sm btn-outline-secondary mb-2">
            <i class="bi bi-arrow-left me-1"></i> Back
        </a>
        <h2 class="fw-bold mb-0" style="color:var(--primary)"><i class="bi bi-search me-2"></i>Room Availability Checker</h2>
    </div>

    <div class="card border-0 shadow-sm mb-4" style="border-radius:12px;">
        <div class="card-body p-4">
            <form method="GET" action="{{ route('admin.bookings.availability') }}" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Check-In Date</label>
                    <input type="date" name="check_in_date" class="form-control" value="{{ request('check_in_date') }}" min="{{ date('Y-m-d') }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Check-Out Date</label>
                    <input type="date" name="check_out_date" class="form-control" value="{{ request('check_out_date') }}" required>
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn w-100 py-2 fw-bold text-white" style="background:var(--primary);">
                        <i class="bi bi-search me-2"></i> Check Availability
                    </button>
                </div>
            </form>
        </div>
    </div>

    @if(isset($availableRooms))
    <div class="card border-0 shadow-sm" style="border-radius:12px;">
        <div class="card-header py-3" style="background:var(--primary); border-radius:12px 12px 0 0;">
            <h5 class="text-white mb-0">
                <i class="bi bi-check-circle me-2"></i>
                {{ $availableRooms->count() }} Available Room(s)
                for {{ \Carbon\Carbon::parse(request('check_in_date'))->format('M d') }}
                – {{ \Carbon\Carbon::parse(request('check_out_date'))->format('M d, Y') }}
            </h5>
        </div>
        <div class="card-body p-0">
            @if($availableRooms->isEmpty())
            <div class="text-center py-5 text-muted">
                <i class="bi bi-calendar-x" style="font-size:3rem; display:block; margin-bottom:8px;"></i>
                No rooms available for the selected dates.
            </div>
            @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead style="background:#f8f9fc;">
                        <tr>
                            <th class="ps-4">Room</th>
                            <th>Type</th>
                            <th>Floor</th>
                            <th>Capacity</th>
                            <th>Price/Night</th>
                            <th>Amenities</th>
                            <th class="pe-4">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($availableRooms as $room)
                        <tr>
                            <td class="ps-4 fw-bold" style="color:var(--primary);">Room {{ $room->room_number }}</td>
                            <td><span class="badge" style="background:var(--secondary); color:#fff;">{{ $room->room_type }}</span></td>
                            <td>{{ $room->floor }}</td>
                            <td>{{ $room->capacity }} person(s)</td>
                           <td class="fw-bold" style="color:var(--secondary);">${{ number_format($room->price_per_night, 2) }}</td>
                            <td>
                                @if($room->amenities)
                                <div class="d-flex flex-wrap gap-1">
                                    @foreach(array_slice((array)$room->amenities, 0, 3) as $a)
                                    <span class="badge rounded-pill" style="background:#f0f4ff; color:var(--primary); font-size:.7rem;">{{ $a }}</span>
                                    @endforeach
                                </div>
                                @endif
                            </td>
                            <td class="pe-4">
                                <a href="{{ route('admin.bookings.create', ['room_id' => $room->id, 'check_in_date' => request('check_in_date'), 'check_out_date' => request('check_out_date')]) }}"
                                   class="btn btn-sm text-white fw-semibold" style="background:var(--primary);">
                                    <i class="bi bi-calendar-plus me-1"></i> Book
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>
    </div>
    @endif
</div>
@endsection
