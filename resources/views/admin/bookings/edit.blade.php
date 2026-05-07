@extends('layouts.admin')
@section('title', 'Edit Booking')

@section('content')
<div class="container-fluid py-4">
    <div class="mb-4">
        <a href="{{ route('admin.bookings.show', $booking) }}" class="btn btn-sm btn-outline-secondary mb-2">
            <i class="bi bi-arrow-left me-1"></i> Back to Booking
        </a>
        <h2 class="fw-bold mb-0" style="color:var(--primary)">Edit Booking #{{ $booking->booking_reference }}</h2>
    </div>

    @if($errors->any())
    <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
    @endif

    <div class="card border-0 shadow-sm" style="border-radius:12px;">
        <div class="card-body p-4 p-md-5">
            <form method="POST" action="{{ route('admin.bookings.update', $booking) }}">
                @csrf @method('PUT')
                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Booking Status</label>
                        <select name="status" class="form-select" required>
                            @foreach(['pending','confirmed','checked_in','checked_out','cancelled'] as $s)
                            <option value="{{ $s }}" {{ $booking->status === $s ? 'selected' : '' }}>
                                {{ ucfirst(str_replace('_',' ',$s)) }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Payment Status</label>
                        <select name="payment_status" class="form-select" required>
                            @foreach(['unpaid','paid','refunded'] as $ps)
                            <option value="{{ $ps }}" {{ $booking->payment_status === $ps ? 'selected' : '' }}>
                                {{ ucfirst($ps) }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Assign Staff</label>
                        <select name="staff_id" class="form-select">
                            <option value="">-- Unassigned --</option>
                            @foreach($staff as $s)
                            <option value="{{ $s->id }}" {{ $booking->staff_id == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Staff Notes</label>
                        <textarea name="notes" class="form-control" rows="4" placeholder="Internal notes...">{{ old('notes', $booking->notes) }}</textarea>
                    </div>

                    {{-- Read-only Info --}}
                    <div class="col-12">
                        <div class="card bg-light border-0 p-3" style="border-radius:10px;">
                            <div class="row g-2 text-muted small">
                                <div class="col-md-3"><strong>Guest:</strong> {{ $booking->user->name }}</div>
                                <div class="col-md-3"><strong>Room:</strong> {{ $booking->room->room_number }}</div>
                                <div class="col-md-3"><strong>Check-In:</strong> {{ $booking->check_in_date->format('M d, Y') }}</div>
                                <div class="col-md-3"><strong>Check-Out:</strong> {{ $booking->check_out_date->format('M d, Y') }}</div>
                                <div class="col-md-3"><strong>Guests:</strong> {{ $booking->guests }}</div>
                                <div class="col-md-3"><strong>Nights:</strong> {{ $booking->nights }}</div>
                                <div class="col-md-3"><strong>Total:</strong> ₱{{ number_format($booking->total_amount, 2) }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 d-flex gap-2 justify-content-end">
                        <a href="{{ route('admin.bookings.index') }}" class="btn btn-outline-secondary px-4">Cancel</a>
                        <button type="submit" class="btn px-5 fw-bold text-white" style="background:var(--primary);">
                            <i class="bi bi-save me-2"></i> Update Booking
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
