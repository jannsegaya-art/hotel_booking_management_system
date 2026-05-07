@extends('layouts.customer')
@section('title', 'Book a Room')

@section('content')
<div class="container-fluid py-4">
    <div class="mb-4">
        <a href="{{ route('customer.bookings.index') }}" class="btn btn-sm btn-outline-secondary mb-2">
            <i class="bi bi-arrow-left me-1"></i> Back
        </a>
        <h2 class="fw-bold mb-0" style="color:var(--primary)"><i class="bi bi-calendar-plus me-2"></i>Book a Room</h2>
        <p class="text-muted mb-0">Fill in your reservation details below</p>
    </div>

    @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show">
        <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm" style="border-radius:12px;">
                <div class="card-header py-3" style="background:var(--primary);border-radius:12px 12px 0 0;">
                    <h5 class="text-white mb-0"><i class="bi bi-pencil me-2"></i>Reservation Details</h5>
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('customer.bookings.store') }}">
                        @csrf
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label fw-semibold">Select Room <span class="text-danger">*</span></label>
                                <select name="room_id" id="roomSelect" class="form-select @error('room_id') is-invalid @enderror" required>
                                    <option value="">-- Choose a Room --</option>
                                    @foreach($rooms as $r)
                                    <option value="{{ $r->id }}" data-price="{{ $r->price_per_night }}"
                                            data-type="{{ $r->room_type }}" data-cap="{{ $r->capacity }}"
                                            {{ (old('room_id', $room?->id) == $r->id) ? 'selected' : '' }}>
                                        Room {{ $r->room_number }} — {{ $r->room_type }} · ₱{{ number_format($r->price_per_night,2) }}/night · {{ $r->capacity }} guests
                                    </option>
                                    @endforeach
                                </select>
                                @error('room_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            {{-- Room Info Card --}}
                            <div class="col-12" id="roomInfoBox" style="display:none;">
                                <div class="p-3 rounded" style="background:linear-gradient(135deg,var(--primary)10,var(--secondary)10);border:2px solid var(--secondary);">
                                    <div class="row g-2 small">
                                        <div class="col-4"><strong>Type:</strong> <span id="infoType">—</span></div>
                                        <div class="col-4"><strong>Capacity:</strong> <span id="infoCap">—</span> guests</div>
                                        <div class="col-4"><strong>Rate:</strong> ₱<span id="infoPrice">—</span>/night</div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Check-In Date <span class="text-danger">*</span></label>
                                <input type="date" name="check_in_date" id="checkIn" class="form-control @error('check_in_date') is-invalid @enderror"
                                       value="{{ old('check_in_date') }}" min="{{ date('Y-m-d') }}" required>
                                @error('check_in_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Check-Out Date <span class="text-danger">*</span></label>
                                <input type="date" name="check_out_date" id="checkOut" class="form-control @error('check_out_date') is-invalid @enderror"
                                       value="{{ old('check_out_date') }}" required>
                                @error('check_out_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Number of Guests <span class="text-danger">*</span></label>
                                <input type="number" name="guests" id="guests" class="form-control" value="{{ old('guests',1) }}" min="1" max="10" required>
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold">Special Requests <span class="text-muted fw-normal">(optional)</span></label>
                                <textarea name="special_requests" class="form-control" rows="3"
                                          placeholder="Dietary requirements, room preferences, accessibility needs...">{{ old('special_requests') }}</textarea>
                            </div>

                            {{-- Price Summary --}}
                            <div class="col-12" id="priceSummary" style="display:none;">
                                <div class="card" style="background:#f8f9fc;border:2px solid var(--secondary);border-radius:10px;">
                                    <div class="card-body p-3">
                                        <h6 class="fw-bold mb-3" style="color:var(--primary);">
                                            <i class="bi bi-receipt me-2"></i>Price Summary
                                        </h6>
                                        <div class="d-flex justify-content-between mb-2">
                                            <span class="text-muted">Room Rate</span>
                                            <span>₱<span id="sumRate">0</span>/night</span>
                                        </div>
                                        <div class="d-flex justify-content-between mb-2">
                                            <span class="text-muted">Number of Nights</span>
                                            <span id="sumNights">0</span>
                                        </div>
                                        <hr class="my-2">
                                        <div class="d-flex justify-content-between fw-bold">
                                            <span>Total Amount</span>
                                            <span style="color:var(--secondary);font-size:1.1rem;">$<span id="sumTotal">0.00</span></span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12">
                                <button type="submit" class="btn w-100 py-3 fw-bold text-white" style="background:var(--primary);border-radius:10px;">
                                    <i class="bi bi-calendar-check me-2"></i> Confirm Booking
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Info Sidebar --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-3" style="border-radius:12px;">
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-3" style="color:var(--primary);"><i class="bi bi-info-circle me-2"></i>Booking Info</h6>
                    <ul class="list-unstyled small text-muted">
                        <li class="mb-2"><i class="bi bi-check-circle-fill me-2 text-success"></i>Free cancellation for pending bookings</li>
                        <li class="mb-2"><i class="bi bi-check-circle-fill me-2 text-success"></i>Booking confirmation within 24 hours</li>
                        <li class="mb-2"><i class="bi bi-check-circle-fill me-2 text-success"></i>Check-in: 2:00 PM · Check-out: 12:00 PM</li>
                        <li class="mb-2"><i class="bi bi-check-circle-fill me-2 text-success"></i>Payment at check-in</li>
                    </ul>
                </div>
            </div>
            <div class="card border-0" style="background:linear-gradient(135deg,var(--primary),var(--secondary));border-radius:12px;">
                <div class="card-body p-4 text-white text-center">
                    <i class="bi bi-headset fs-1 mb-2 d-block"></i>
                    <h6 class="fw-bold">Need Help?</h6>
                    <p class="small opacity-75 mb-2">Our team is available 24/7.</p>
                    <div class="small">📞 +1 (555) 123-4567</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function calcPrice(){
    const sel = document.getElementById('roomSelect');
    const opt = sel.options[sel.selectedIndex];
    const price = parseFloat(opt?.dataset.price || 0);
    const ci = document.getElementById('checkIn').value;
    const co = document.getElementById('checkOut').value;

    if(opt?.value){
        document.getElementById('roomInfoBox').style.display='';
        document.getElementById('infoType').textContent = opt.dataset.type || '—';
        document.getElementById('infoCap').textContent = opt.dataset.cap || '—';
        document.getElementById('infoPrice').textContent = price.toFixed(2);
    } else {
        document.getElementById('roomInfoBox').style.display='none';
    }

    if(price && ci && co){
        const nights = Math.max(0,(new Date(co)-new Date(ci))/86400000);
        const total = nights * price;
        document.getElementById('priceSummary').style.display='';
        document.getElementById('sumRate').textContent = price.toFixed(2);
        document.getElementById('sumNights').textContent = nights;
        document.getElementById('sumTotal').textContent = total.toFixed(2);
    } else {
        document.getElementById('priceSummary').style.display='none';
    }
}
document.getElementById('roomSelect').addEventListener('change', calcPrice);
document.getElementById('checkIn').addEventListener('change', function(){
    document.getElementById('checkOut').min = this.value;
    calcPrice();
});
document.getElementById('checkOut').addEventListener('change', calcPrice);
// Init
if(document.getElementById('roomSelect').value) calcPrice();
</script>
@endpush
