@extends('layouts.admin')
@section('title', 'Booking Details - ' . $booking->booking_reference)

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <a href="{{ route('admin.bookings.index') }}" class="btn btn-sm btn-outline-secondary mb-2">
                <i class="bi bi-arrow-left me-1"></i> Back to Bookings
            </a>
            <h2 class="fw-bold mb-0" style="color:var(--primary)">Booking #{{ $booking->booking_reference }}</h2>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.bookings.edit', $booking) }}" class="btn text-white fw-semibold" style="background:var(--secondary);">
                <i class="bi bi-pencil me-1"></i> Edit Booking
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="row g-4">
        <div class="col-lg-8">
            {{-- Booking Info --}}
            <div class="card border-0 shadow-sm mb-4" style="border-radius:12px;">
                <div class="card-header py-3" style="background:var(--primary); border-radius:12px 12px 0 0;">
                    <h5 class="text-white mb-0"><i class="bi bi-info-circle me-2"></i>Booking Information</h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="text-muted small">Reference Number</div>
                            <div class="fw-bold fs-5" style="color:var(--primary);">{{ $booking->booking_reference }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-muted small">Status</div>
                            @php $statusColors = ['pending'=>'warning text-dark','confirmed'=>'primary','checked_in'=>'success','checked_out'=>'secondary','cancelled'=>'danger']; @endphp
                            <span class="badge bg-{{ $statusColors[$booking->status] ?? 'secondary' }} fs-6">
                                {{ ucfirst(str_replace('_',' ',$booking->status)) }}
                            </span>
                        </div>
                        <div class="col-md-6">
                            <div class="text-muted small">Check-In Date</div>
                            <div class="fw-semibold">{{ $booking->check_in_date->format('F d, Y') }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-muted small">Check-Out Date</div>
                            <div class="fw-semibold">{{ $booking->check_out_date->format('F d, Y') }}</div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-muted small">Duration</div>
                            <div class="fw-semibold">{{ $booking->nights }} night(s)</div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-muted small">Guests</div>
                            <div class="fw-semibold">{{ $booking->guests }} person(s)</div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-muted small">Total Amount</div>
                            <div class="fw-bold fs-5" style="color:var(--secondary);">₱{{ number_format($booking->total_amount, 2) }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-muted small">Payment Status</div>
                            <span class="badge bg-{{ $booking->payment_status === 'paid' ? 'success' : ($booking->payment_status === 'refunded' ? 'info' : 'warning text-dark') }}">
                                {{ ucfirst($booking->payment_status) }}
                            </span>
                        </div>
                        <div class="col-md-6">
                            <div class="text-muted small">Booked On</div>
                            <div class="fw-semibold">{{ $booking->created_at->format('F d, Y h:i A') }}</div>
                        </div>
                        @if($booking->special_requests)
                        <div class="col-12">
                            <div class="text-muted small">Special Requests</div>
                            <div class="fw-semibold">{{ $booking->special_requests }}</div>
                        </div>
                        @endif
                        @if($booking->notes)
                        <div class="col-12">
                            <div class="text-muted small">Staff Notes</div>
                            <div class="fw-semibold">{{ $booking->notes }}</div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Rating --}}
            @if($booking->rating)
            <div class="card border-0 shadow-sm" style="border-radius:12px;">
                <div class="card-header py-3" style="background:var(--primary); border-radius:12px 12px 0 0;">
                    <h5 class="text-white mb-0"><i class="bi bi-star-fill me-2"></i>Guest Rating</h5>
                </div>
                <div class="card-body p-4">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        @for($i=1; $i<=5; $i++)
                        <i class="bi bi-star{{ $i <= $booking->rating->rating ? '-fill' : '' }}" style="color:var(--secondary); font-size:1.3rem;"></i>
                        @endfor
                        <span class="fw-bold ms-1">{{ $booking->rating->rating }}/5</span>
                    </div>
                    @if($booking->rating->comment)
                    <p class="text-muted mb-0">"{{ $booking->rating->comment }}"</p>
                    @endif
                </div>
            </div>
            @endif
        </div>

        <div class="col-lg-4">
            {{-- Guest Info --}}
            <div class="card border-0 shadow-sm mb-4" style="border-radius:12px;">
                <div class="card-header py-3" style="background:var(--secondary); border-radius:12px 12px 0 0;">
                    <h5 class="text-white mb-0"><i class="bi bi-person me-2"></i>Guest Info</h5>
                </div>
                <div class="card-body p-4">
                    <div class="fw-bold mb-1">{{ $booking->user->name }}</div>
                    <div class="text-muted small mb-1"><i class="bi bi-envelope me-1"></i>{{ $booking->user->email }}</div>
                    @if($booking->user->phone)
                    <div class="text-muted small"><i class="bi bi-telephone me-1"></i>{{ $booking->user->phone }}</div>
                    @endif
                </div>
            </div>

            {{-- Room Info --}}
            <div class="card border-0 shadow-sm mb-4" style="border-radius:12px;">
                <div class="card-header py-3" style="background:var(--secondary); border-radius:12px 12px 0 0;">
                    <h5 class="text-white mb-0"><i class="bi bi-building me-2"></i>Room Info</h5>
                </div>
                <div class="card-body p-4">
                    <div class="fw-bold mb-1">Room {{ $booking->room->room_number }}</div>
                    <div class="text-muted small mb-1">{{ $booking->room->room_type }}</div>
                    <div class="text-muted small mb-1">Floor {{ $booking->room->floor }}</div>
                    <div class="fw-semibold" style="color:var(--secondary);">₱{{ number_format($booking->room->price_per_night, 2) }}/night</div>
                </div>
            </div>

            {{-- Assigned Staff --}}
            <div class="card border-0 shadow-sm" style="border-radius:12px;">
                <div class="card-header py-3" style="background:var(--secondary); border-radius:12px 12px 0 0;">
                    <h5 class="text-white mb-0"><i class="bi bi-person-badge me-2"></i>Assigned Staff</h5>
                </div>
                <div class="card-body p-4">
                    @if($booking->staff)
                    <div class="fw-bold mb-1">{{ $booking->staff->name }}</div>
                    <div class="text-muted small">{{ $booking->staff->email }}</div>
                    @else
                    <p class="text-muted mb-3">No staff assigned yet.</p>
                    @endif

                    <form method="POST" action="{{ route('admin.bookings.update', $booking) }}" class="mt-3">
                        @csrf @method('PUT')
                        <input type="hidden" name="status" value="{{ $booking->status }}">
                        <input type="hidden" name="payment_status" value="{{ $booking->payment_status }}">
                        <select name="staff_id" class="form-select form-select-sm mb-2">
                            <option value="">-- Select Staff --</option>
                            @foreach($staff as $s)
                            <option value="{{ $s->id }}" {{ $booking->staff_id == $s->id ? 'selected' : '' }}>
                                {{ $s->name }}
                            </option>
                            @endforeach
                        </select>
                        <button type="submit" class="btn btn-sm w-100 text-white" style="background:var(--primary);">
                            <i class="bi bi-person-check me-1"></i> Assign Staff
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
