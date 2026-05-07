@extends('layouts.customer')
@section('title', 'Booking Details')

@section('content')
<div class="container-fluid py-4">
    <div class="mb-4">
        <a href="{{ route('customer.bookings.index') }}" class="btn btn-sm btn-outline-secondary mb-2">
            <i class="bi bi-arrow-left me-1"></i> Back to My Bookings
        </a>
        <h2 class="fw-bold mb-0" style="color:var(--primary)">Booking #{{ $booking->booking_reference }}</h2>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-4" style="border-radius:12px;">
                <div class="card-header py-3" style="background:var(--primary);border-radius:12px 12px 0 0;">
                    <h5 class="text-white mb-0"><i class="bi bi-receipt me-2"></i>Booking Summary</h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="text-muted small">Reference Number</div>
                            <div class="fw-bold fs-5" style="color:var(--primary);">{{ $booking->booking_reference }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-muted small">Status</div>
                            @php $sc=['pending'=>'warning text-dark','confirmed'=>'primary','checked_in'=>'success','checked_out'=>'secondary','cancelled'=>'danger']; @endphp
                            <span class="badge bg-{{ $sc[$booking->status] ?? 'secondary' }} fs-6">{{ ucfirst(str_replace('_',' ',$booking->status)) }}</span>
                        </div>
                        <div class="col-md-6">
                            <div class="text-muted small">Room</div>
                            <div class="fw-semibold">Room {{ $booking->room->room_number }} — {{ $booking->room->room_type }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-muted small">Floor</div>
                            <div class="fw-semibold">Floor {{ $booking->room->floor }}</div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-muted small">Check-In</div>
                            <div class="fw-semibold">{{ $booking->check_in_date->format('F d, Y') }}</div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-muted small">Check-Out</div>
                            <div class="fw-semibold">{{ $booking->check_out_date->format('F d, Y') }}</div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-muted small">Duration</div>
                            <div class="fw-semibold">{{ $booking->nights }} night(s)</div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-muted small">Guests</div>
                            <div class="fw-semibold">{{ $booking->guests }} person(s)</div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-muted small">Payment Status</div>
                            <span class="badge bg-{{ $booking->payment_status==='paid'?'success':($booking->payment_status==='refunded'?'info':'warning text-dark') }}">
                                {{ ucfirst($booking->payment_status) }}
                            </span>
                        </div>
                        <div class="col-12">
                            <div class="p-3 rounded" style="background:#f8f9fc;border:2px solid var(--secondary);">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="fw-semibold">Total Amount</span>
                                    <span class="fw-bold fs-4" style="color:var(--secondary);">₱{{ number_format($booking->total_amount,2) }}</span>
                                </div>
                            </div>
                        </div>
                        @if($booking->special_requests)
                        <div class="col-12">
                            <div class="text-muted small">Special Requests</div>
                            <div class="fw-semibold">{{ $booking->special_requests }}</div>
                        </div>
                        @endif
                        @if($booking->notes)
                        <div class="col-12">
                            <div class="text-muted small">Notes from Staff</div>
                            <div class="fw-semibold">{{ $booking->notes }}</div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Rate Your Stay --}}
            @if($booking->status === 'checked_out')
            <div class="card border-0 shadow-sm" style="border-radius:12px;">
                <div class="card-header py-3" style="background:var(--secondary);border-radius:12px 12px 0 0;">
                    <h5 class="text-white mb-0"><i class="bi bi-star me-2"></i>Rate Your Stay</h5>
                </div>
                <div class="card-body p-4">
                    @if($booking->rating)
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <div class="fw-semibold text-muted me-1">Your Rating:</div>
                        @for($i=1;$i<=5;$i++)
                        <i class="bi bi-star{{ $i<=$booking->rating->rating?'-fill':'' }}" style="color:var(--secondary);font-size:1.3rem;"></i>
                        @endfor
                        <span class="fw-bold">{{ $booking->rating->rating }}/5</span>
                    </div>
                    @if($booking->rating->comment)
                    <p class="text-muted">"{{ $booking->rating->comment }}"</p>
                    @endif
                    <p class="text-muted small mb-0">Thank you for your feedback!</p>
                    @else
                    <form method="POST" action="{{ route('customer.bookings.rate', $booking) }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Your Rating</label>
                            <div class="d-flex gap-2" id="starRating">
                                @for($i=1;$i<=5;$i++)
                                <i class="bi bi-star star-btn" data-val="{{ $i }}"
                                   style="font-size:2rem;cursor:pointer;color:#dee2e6;transition:color .1s;" data-val="{{ $i }}"></i>
                                @endfor
                            </div>
                            <input type="hidden" name="rating" id="ratingInput" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Comment <span class="text-muted fw-normal">(optional)</span></label>
                            <textarea name="comment" class="form-control" rows="3" placeholder="Tell us about your experience..."></textarea>
                        </div>
                        <button type="submit" class="btn fw-bold text-white px-4" style="background:var(--secondary);">
                            <i class="bi bi-send me-2"></i>Submit Review
                        </button>
                    </form>
                    @endif
                </div>
            </div>
            @endif
        </div>

        <div class="col-lg-4">
            {{-- Room Amenities --}}
            <div class="card border-0 shadow-sm mb-4" style="border-radius:12px;">
                <div class="card-header py-3" style="background:var(--secondary);border-radius:12px 12px 0 0;">
                    <h5 class="text-white mb-0"><i class="bi bi-gem me-2"></i>Room Amenities</h5>
                </div>
                <div class="card-body p-4">
                    @if($booking->room->amenities)
                    <div class="d-flex flex-wrap gap-2">
                        @foreach((array)$booking->room->amenities as $a)
                        <span class="badge rounded-pill px-3 py-2" style="background:#f0f4ff;color:var(--primary);">
                            <i class="bi bi-check-circle me-1"></i>{{ $a }}
                        </span>
                        @endforeach
                    </div>
                    @else
                    <p class="text-muted mb-0">No amenities listed.</p>
                    @endif
                </div>
            </div>

            {{-- Assigned Staff --}}
            @if($booking->staff)
            <div class="card border-0 shadow-sm mb-4" style="border-radius:12px;">
                <div class="card-header py-3" style="background:var(--secondary);border-radius:12px 12px 0 0;">
                    <h5 class="text-white mb-0"><i class="bi bi-person-badge me-2"></i>Your Host</h5>
                </div>
                <div class="card-body p-4">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold text-white"
                             style="width:48px;height:48px;background:var(--primary);flex-shrink:0;">
                            {{ strtoupper(substr($booking->staff->name,0,1)) }}
                        </div>
                        <div>
                            <div class="fw-bold">{{ $booking->staff->name }}</div>
                            <div class="small text-muted">Hotel Staff</div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            {{-- Actions --}}
            @if(in_array($booking->status, ['pending','confirmed']))
            <div class="card border-0 shadow-sm" style="border-radius:12px;">
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-3">Actions</h6>
                    <button class="btn w-100 btn-outline-danger fw-semibold" id="cancelBtn">
                        <i class="bi bi-x-circle me-2"></i>Cancel Booking
                    </button>
                    <form id="cancelBtnForm" method="POST" action="{{ route('customer.bookings.cancel', $booking) }}" class="d-none">@csrf</form>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Star rating
let selectedRating = 0;
document.querySelectorAll('.star-btn').forEach(star => {
    star.addEventListener('mouseenter', function(){
        const val = parseInt(this.dataset.val);
        document.querySelectorAll('.star-btn').forEach((s,i) => {
            s.className = 'bi ' + (i < val ? 'bi-star-fill' : 'bi-star') + ' star-btn';
            s.style.color = i < val ? 'var(--secondary)' : '#dee2e6';
        });
    });
    star.addEventListener('mouseleave', function(){
        document.querySelectorAll('.star-btn').forEach((s,i) => {
            s.className = 'bi ' + (i < selectedRating ? 'bi-star-fill' : 'bi-star') + ' star-btn';
            s.style.color = i < selectedRating ? 'var(--secondary)' : '#dee2e6';
        });
    });
    star.addEventListener('click', function(){
        selectedRating = parseInt(this.dataset.val);
        document.getElementById('ratingInput').value = selectedRating;
    });
});

// Cancel
const cancelBtn = document.getElementById('cancelBtn');
if(cancelBtn){
    cancelBtn.addEventListener('click', function(){
        Swal.fire({ title:'Cancel Booking?', text:'Are you sure you want to cancel this booking?',
            icon:'warning', showCancelButton:true, confirmButtonColor:'#dc3545',
            confirmButtonText:'Yes, Cancel', cancelButtonText:'Keep It',
        }).then(r => { if(r.isConfirmed) document.getElementById('cancelBtnForm').submit(); });
    });
}
</script>
@endpush
