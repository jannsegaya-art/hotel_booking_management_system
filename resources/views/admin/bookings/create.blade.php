@extends('layouts.admin')
@section('title', 'Create Booking')

@section('content')
<div class="container-fluid py-4">
    <div class="mb-4">
        <a href="{{ route('admin.bookings.index') }}" class="btn btn-sm btn-outline-secondary mb-2">
            <i class="bi bi-arrow-left me-1"></i> Back
        </a>
        <h2 class="fw-bold mb-0" style="color:var(--primary)"><i class="bi bi-plus-circle me-2"></i>Create New Booking</h2>
    </div>

    @if($errors->any())
    <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
    @endif

    <div class="card border-0 shadow-sm" style="border-radius:12px;">
        <div class="card-body p-4 p-md-5">
            <form method="POST" action="{{ route('admin.bookings.store') }}">
                @csrf
                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Guest (Customer) <span class="text-danger">*</span></label>
                        <select name="user_id" class="form-select @error('user_id') is-invalid @enderror" required>
                            <option value="">-- Select Customer --</option>
                            @foreach($customers as $c)
                            <option value="{{ $c->id }}" {{ old('user_id') == $c->id ? 'selected' : '' }}>
                                {{ $c->name }} ({{ $c->email }})
                            </option>
                            @endforeach
                        </select>
                        @error('user_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Room <span class="text-danger">*</span></label>
                        <select name="room_id" class="form-select @error('room_id') is-invalid @enderror" required id="roomSelect">
                            <option value="">-- Select Room --</option>
                            @foreach($rooms as $room)
                            <option value="{{ $room->id }}" data-price="{{ $room->price_per_night }}" {{ old('room_id') == $room->id ? 'selected' : '' }}>
                               Room {{ $room->room_number }} - {{ $room->room_type }} (${{ number_format($room->price_per_night,2) }}/night)
                            </option>
                            @endforeach
                        </select>
                        @error('room_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Check-In Date <span class="text-danger">*</span></label>
                        <input type="date" name="check_in_date" class="form-control @error('check_in_date') is-invalid @enderror"
                               value="{{ old('check_in_date') }}" min="{{ date('Y-m-d') }}" required id="checkIn">
                        @error('check_in_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Check-Out Date <span class="text-danger">*</span></label>
                        <input type="date" name="check_out_date" class="form-control @error('check_out_date') is-invalid @enderror"
                               value="{{ old('check_out_date') }}" required id="checkOut">
                        @error('check_out_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Number of Guests <span class="text-danger">*</span></label>
                        <input type="number" name="guests" class="form-control" value="{{ old('guests',1) }}" min="1" max="10" required>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Payment Status</label>
                        <select name="payment_status" class="form-select">
                            <option value="unpaid" {{ old('payment_status') === 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                            <option value="paid" {{ old('payment_status') === 'paid' ? 'selected' : '' }}>Paid</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Assign Staff</label>
                        <select name="staff_id" class="form-select">
                            <option value="">-- No Assignment --</option>
                            @foreach($staff as $s)
                            <option value="{{ $s->id }}">{{ $s->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-semibold">Special Requests</label>
                        <textarea name="special_requests" class="form-control" rows="3"
                                  placeholder="Any special requests from the guest...">{{ old('special_requests') }}</textarea>
                    </div>

                    {{-- Price Summary --}}
                    <div class="col-12">
                        <div class="card" style="background:#f8f9fc; border-radius:10px; border:2px solid var(--secondary);">
                            <div class="card-body p-3">
                                <h6 class="fw-bold mb-2" style="color:var(--primary);">Price Summary</h6>
                                <div class="d-flex justify-content-between">
                                    <span class="text-muted">Nights: <strong id="nightsDisplay">0</strong></span>
                                    <span class="text-muted">Rate: <strong id="rateDisplay">$0.00</strong>/night</span>
                                    <span class="fw-bold fs-5" style="color:var(--secondary);">Total: <strong id="totalDisplay">$0.00</strong></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 d-flex gap-2 justify-content-end">
                        <a href="{{ route('admin.bookings.index') }}" class="btn btn-outline-secondary px-4">Cancel</a>
                        <button type="submit" class="btn px-5 fw-bold text-white" style="background:var(--primary);">
                            <i class="bi bi-calendar-check me-2"></i> Create Booking
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function calcTotal(){
    const roomSel = document.getElementById('roomSelect');
    const checkIn = document.getElementById('checkIn').value;
    const checkOut = document.getElementById('checkOut').value;
    const price = parseFloat(roomSel.options[roomSel.selectedIndex]?.dataset.price || 0);
    if(checkIn && checkOut && price){
        const nights = Math.max(0, (new Date(checkOut) - new Date(checkIn)) / 86400000);
        document.getElementById('nightsDisplay').textContent = nights;
        document.getElementById('rateDisplay').textContent = '$' + price.toFixed(2);
        document.getElementById('totalDisplay').textContent = '$' + (nights * price).toFixed(2);
    }
}
document.getElementById('roomSelect').addEventListener('change', calcTotal);
document.getElementById('checkIn').addEventListener('change', function(){
    document.getElementById('checkOut').min = this.value;
    calcTotal();
});
document.getElementById('checkOut').addEventListener('change', calcTotal);
</script>
@endpush
