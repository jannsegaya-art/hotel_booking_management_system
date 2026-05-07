@extends('layouts.staff')
@section('title', 'Booking Details')

@section('content')
<div class="container-fluid py-4">
    <div class="mb-4">
        <a href="{{ route('staff.bookings.index') }}" class="btn btn-sm btn-outline-secondary mb-2">
            <i class="bi bi-arrow-left me-1"></i> Back
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
                    <h5 class="text-white mb-0"><i class="bi bi-info-circle me-2"></i>Booking Details</h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6"><div class="text-muted small">Reference</div><div class="fw-bold" style="color:var(--primary);">{{ $booking->booking_reference }}</div></div>
                        <div class="col-md-6"><div class="text-muted small">Status</div>
                            @php $sc=['pending'=>'warning text-dark','confirmed'=>'primary','checked_in'=>'success','checked_out'=>'secondary','cancelled'=>'danger']; @endphp
                            <span class="badge bg-{{ $sc[$booking->status] ?? 'secondary' }} fs-6">{{ ucfirst(str_replace('_',' ',$booking->status)) }}</span>
                        </div>
                        <div class="col-md-6"><div class="text-muted small">Guest</div><div class="fw-semibold">{{ $booking->user->name }}</div></div>
                        <div class="col-md-6"><div class="text-muted small">Room</div><div class="fw-semibold">Room {{ $booking->room->room_number }} ({{ $booking->room->room_type }})</div></div>
                        <div class="col-md-4"><div class="text-muted small">Check-In</div><div class="fw-semibold">{{ $booking->check_in_date->format('M d, Y') }}</div></div>
                        <div class="col-md-4"><div class="text-muted small">Check-Out</div><div class="fw-semibold">{{ $booking->check_out_date->format('M d, Y') }}</div></div>
                        <div class="col-md-4"><div class="text-muted small">Nights / Guests</div><div class="fw-semibold">{{ $booking->nights }} nights, {{ $booking->guests }} guest(s)</div></div>
                        <div class="col-md-6"><div class="text-muted small">Total Amount</div><div class="fw-bold fs-5" style="color:var(--secondary);">₱{{ number_format($booking->total_amount,2) }}</div></div>
                        <div class="col-md-6"><div class="text-muted small">Payment</div>
                            <span class="badge bg-{{ $booking->payment_status==='paid'?'success':($booking->payment_status==='refunded'?'info':'warning text-dark') }}">{{ ucfirst($booking->payment_status) }}</span>
                        </div>
                        @if($booking->special_requests)
                        <div class="col-12"><div class="text-muted small">Special Requests</div><div class="fw-semibold">{{ $booking->special_requests }}</div></div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Update Status --}}
            @if(!in_array($booking->status, ['checked_out','cancelled']))
            <div class="card border-0 shadow-sm" style="border-radius:12px;">
                <div class="card-header py-3" style="background:var(--secondary);border-radius:12px 12px 0 0;">
                    <h5 class="text-white mb-0"><i class="bi bi-arrow-repeat me-2"></i>Update Booking Status</h5>
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('staff.bookings.status', $booking) }}">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">New Status</label>
                                <select name="status" class="form-select" required>
                                    @foreach(['confirmed','checked_in','checked_out','cancelled'] as $s)
                                    @if($s !== $booking->status)
                                    <option value="{{ $s }}">{{ ucfirst(str_replace('_',' ',$s)) }}</option>
                                    @endif
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 d-flex align-items-end">
                                <button type="submit" class="btn w-100 fw-bold text-white" style="background:var(--primary);">
                                    <i class="bi bi-check-circle me-2"></i> Update Status
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            @endif
        </div>

        <div class="col-lg-4">
            {{-- Guest Info --}}
            <div class="card border-0 shadow-sm mb-4" style="border-radius:12px;">
                <div class="card-header py-3" style="background:var(--secondary);border-radius:12px 12px 0 0;">
                    <h5 class="text-white mb-0"><i class="bi bi-person me-2"></i>Guest Info</h5>
                </div>
                <div class="card-body p-4">
                    <div class="fw-bold mb-1">{{ $booking->user->name }}</div>
                    <div class="small text-muted mb-1"><i class="bi bi-envelope me-1"></i>{{ $booking->user->email }}</div>
                    @if($booking->user->phone)
                    <div class="small text-muted"><i class="bi bi-telephone me-1"></i>{{ $booking->user->phone }}</div>
                    @endif
                </div>
            </div>

            {{-- Rating if completed --}}
            @if($booking->rating)
            <div class="card border-0 shadow-sm" style="border-radius:12px;">
                <div class="card-header py-3" style="background:var(--secondary);border-radius:12px 12px 0 0;">
                    <h5 class="text-white mb-0"><i class="bi bi-star me-2"></i>Guest Rating</h5>
                </div>
                <div class="card-body p-4">
                    <div class="d-flex gap-1 mb-2">
                        @for($i=1;$i<=5;$i++)
                        <i class="bi bi-star{{ $i<=$booking->rating->rating?'-fill':'' }}" style="color:var(--secondary);font-size:1.2rem;"></i>
                        @endfor
                        <span class="fw-bold ms-1">{{ $booking->rating->rating }}/5</span>
                    </div>
                    @if($booking->rating->comment)
                    <p class="text-muted small mb-0">"{{ $booking->rating->comment }}"</p>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
