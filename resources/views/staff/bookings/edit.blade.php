@extends('layouts.staff')
@section('title', 'Edit Booking #' . $booking->booking_reference)

@section('content')
<div class="container-fluid py-4">

    {{-- Header --}}
    <div class="mb-4">
        <a href="{{ route('staff.bookings.index') }}" class="btn btn-sm btn-outline-secondary mb-2">
            <i class="bi bi-arrow-left me-1"></i> Back to My Bookings
        </a>
        <h4 class="fw-bold mb-0" style="color:var(--primary);">
            <i class="bi bi-pencil-square me-2"></i>Edit Booking
            <span style="color:var(--secondary);">#{{ $booking->booking_reference }}</span>
        </h4>
        <p class="text-muted small mb-0">Update booking status and payment status</p>
    </div>

    @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show shadow-sm">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>
        <ul class="mb-0 ps-3">
            @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="row g-4">

        {{-- Edit Form --}}
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm" style="border-radius:14px;">
                <div class="card-header py-3" style="background:var(--primary); border-radius:14px 14px 0 0;">
                    <h5 class="text-white mb-0 fw-semibold">
                        <i class="bi bi-pencil me-2"></i>Update Booking Information
                    </h5>
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('staff.bookings.update', $booking) }}" id="editForm">
                        @csrf
                        @method('PUT')

                        {{-- Booking Status --}}
                        <div class="mb-4">
                            <label class="form-label fw-semibold">
                                <i class="bi bi-arrow-repeat me-1" style="color:var(--primary);"></i>
                                Booking Status <span class="text-danger">*</span>
                            </label>
                            <div class="row g-2">
                                @php
                                $statusOptions = [
                                    'confirmed'   => ['label' => 'Confirmed',   'icon' => 'bi-check-circle',   'color' => '#0d6efd', 'bg' => 'rgba(13,110,253,.1)'],
                                    'checked_in'  => ['label' => 'Checked In',  'icon' => 'bi-door-open',      'color' => '#198754', 'bg' => 'rgba(25,135,84,.1)'],
                                    'checked_out' => ['label' => 'Checked Out', 'icon' => 'bi-door-closed',    'color' => '#6c757d', 'bg' => 'rgba(108,117,125,.1)'],
                                    'cancelled'   => ['label' => 'Cancelled',   'icon' => 'bi-x-circle',       'color' => '#dc3545', 'bg' => 'rgba(220,53,69,.1)'],
                                ];
                                @endphp
                                @foreach($statusOptions as $value => $opt)
                                <div class="col-6">
                                    <input type="radio" name="status" id="status_{{ $value }}"
                                           value="{{ $value }}" class="d-none status-radio"
                                           {{ old('status', $booking->status) === $value ? 'checked' : '' }}>
                                    <label for="status_{{ $value }}"
                                           class="status-label w-100 d-flex align-items-center gap-2 p-3 rounded-3 border fw-semibold small"
                                           style="cursor:pointer; transition:.2s;
                                                  background:{{ old('status', $booking->status) === $value ? $opt['bg'] : '#fff' }};
                                                  border-color:{{ old('status', $booking->status) === $value ? $opt['color'] : '#dee2e6' }} !important;
                                                  color:{{ old('status', $booking->status) === $value ? $opt['color'] : '#6c757d' }};">
                                        <i class="bi {{ $opt['icon'] }} fs-5"></i>
                                        {{ $opt['label'] }}
                                    </label>
                                </div>
                                @endforeach
                            </div>
                        </div>

                        {{-- Payment Status --}}
                        <div class="mb-4">
                            <label class="form-label fw-semibold">
                                <i class="bi bi-cash-stack me-1" style="color:var(--secondary);"></i>
                                Payment Status <span class="text-danger">*</span>
                            </label>
                            <div class="row g-2">
                                @php
                                $paymentOptions = [
                                    'unpaid'   => ['label' => 'Unpaid',   'icon' => 'bi-clock',          'color' => '#856404', 'bg' => 'rgba(255,193,7,.1)'],
                                    'paid'     => ['label' => 'Paid',     'icon' => 'bi-check-circle',   'color' => '#198754', 'bg' => 'rgba(25,135,84,.1)'],
                                    'refunded' => ['label' => 'Refunded', 'icon' => 'bi-arrow-counterclockwise','color' => '#055160','bg' => 'rgba(13,202,240,.1)'],
                                ];
                                @endphp
                                @foreach($paymentOptions as $value => $opt)
                                <div class="col-4">
                                    <input type="radio" name="payment_status" id="pay_{{ $value }}"
                                           value="{{ $value }}" class="d-none payment-radio"
                                           {{ old('payment_status', $booking->payment_status) === $value ? 'checked' : '' }}>
                                    <label for="pay_{{ $value }}"
                                           class="pay-label w-100 d-flex flex-column align-items-center gap-1 p-3 rounded-3 border fw-semibold"
                                           style="cursor:pointer; transition:.2s; font-size:.8rem;
                                                  background:{{ old('payment_status', $booking->payment_status) === $value ? $opt['bg'] : '#fff' }};
                                                  border-color:{{ old('payment_status', $booking->payment_status) === $value ? $opt['color'] : '#dee2e6' }} !important;
                                                  color:{{ old('payment_status', $booking->payment_status) === $value ? $opt['color'] : '#6c757d' }};">
                                        <i class="bi {{ $opt['icon'] }} fs-4"></i>
                                        {{ $opt['label'] }}
                                    </label>
                                </div>
                                @endforeach
                            </div>
                        </div>

                        {{-- Staff Notes --}}
                        <div class="mb-4">
                            <label class="form-label fw-semibold">
                                <i class="bi bi-journal-text me-1" style="color:var(--primary);"></i>
                                Staff Notes <span class="text-muted fw-normal">(optional)</span>
                            </label>
                            <textarea name="notes" class="form-control" rows="3"
                                      placeholder="Add any notes about this booking...">{{ old('notes', $booking->notes) }}</textarea>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="d-flex gap-2">
                            <a href="{{ route('staff.bookings.index') }}"
                               class="btn btn-outline-secondary px-4">
                                <i class="bi bi-x me-1"></i>Cancel
                            </a>
                            <button type="submit" class="btn flex-fill fw-semibold text-white"
                                    style="background:var(--primary); border-radius:8px;">
                                <i class="bi bi-save me-2"></i>Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Booking Info (read-only) --}}
        <div class="col-lg-5">

            {{-- Summary Card --}}
            <div class="card border-0 shadow-sm mb-4" style="border-radius:14px;">
                <div class="card-header py-3" style="background:var(--secondary); border-radius:14px 14px 0 0;">
                    <h5 class="text-white mb-0 fw-semibold">
                        <i class="bi bi-info-circle me-2"></i>Booking Summary
                    </h5>
                </div>
                <div class="card-body p-4">
                    <table class="table table-sm table-borderless mb-0">
                        <tr>
                            <td class="text-muted small fw-semibold" style="width:130px;">Reference</td>
                            <td><code class="fw-semibold" style="color:var(--primary);">{{ $booking->booking_reference }}</code></td>
                        </tr>
                        <tr>
                            <td class="text-muted small fw-semibold">Guest</td>
                            <td class="small fw-semibold">{{ $booking->user->name }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted small fw-semibold">Email</td>
                            <td class="small text-muted">{{ $booking->user->email }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted small fw-semibold">Phone</td>
                            <td class="small">{{ $booking->user->phone ?? '—' }}</td>
                        </tr>
                        <tr><td colspan="2"><hr class="my-1"></td></tr>
                        <tr>
                            <td class="text-muted small fw-semibold">Room</td>
                            <td class="small fw-semibold">Room {{ $booking->room->room_number }} ({{ $booking->room->room_type }})</td>
                        </tr>
                        <tr>
                            <td class="text-muted small fw-semibold">Check-In</td>
                            <td class="small">{{ $booking->check_in_date->format('M d, Y') }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted small fw-semibold">Check-Out</td>
                            <td class="small">{{ $booking->check_out_date->format('M d, Y') }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted small fw-semibold">Nights</td>
                            <td class="small">{{ $booking->nights }} night(s)</td>
                        </tr>
                        <tr>
                            <td class="text-muted small fw-semibold">Guests</td>
                            <td class="small">{{ $booking->guests }} person(s)</td>
                        </tr>
                        <tr><td colspan="2"><hr class="my-1"></td></tr>
                        <tr>
                            <td class="text-muted small fw-semibold">Total Amount</td>
                            <td class="fw-bold" style="color:var(--secondary); font-size:1.1rem;">
                                ₱{{ number_format($booking->total_amount, 2) }}
                            </td>
                        </tr>
                        @if($booking->special_requests)
                        <tr>
                            <td class="text-muted small fw-semibold">Special Request</td>
                            <td class="small text-muted">{{ $booking->special_requests }}</td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>

            {{-- Delete Button (only for pending/cancelled) --}}
            @if(in_array($booking->status, ['pending', 'cancelled']))
            <div class="card border-0 shadow-sm border border-danger" style="border-radius:14px;">
                <div class="card-body p-4">
                    <h6 class="fw-bold text-danger mb-1">
                        <i class="bi bi-exclamation-triangle me-1"></i>Danger Zone
                    </h6>
                    <p class="text-muted small mb-3">
                        Delete this booking permanently. This cannot be undone.
                        Only available for <strong>Pending</strong> or <strong>Cancelled</strong> bookings.
                    </p>
                    <form method="POST" action="{{ route('staff.bookings.destroy', $booking) }}" id="deleteBookingForm">
                        @csrf @method('DELETE')
                        <button type="button" class="btn btn-outline-danger w-100 fw-semibold" id="deleteBookingBtn">
                            <i class="bi bi-trash me-2"></i>Delete This Booking
                        </button>
                    </form>
                </div>
            </div>
            @endif

        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Status radio cards — visual selection
document.querySelectorAll('.status-radio').forEach(function(radio) {
    radio.addEventListener('change', function() {
        var colors = {
            'confirmed':   { color: '#0d6efd', bg: 'rgba(13,110,253,.1)' },
            'checked_in':  { color: '#198754', bg: 'rgba(25,135,84,.1)' },
            'checked_out': { color: '#6c757d', bg: 'rgba(108,117,125,.1)' },
            'cancelled':   { color: '#dc3545', bg: 'rgba(220,53,69,.1)' },
        };
        document.querySelectorAll('.status-label').forEach(function(label) {
            label.style.background   = '#fff';
            label.style.borderColor  = '#dee2e6';
            label.style.color        = '#6c757d';
        });
        if (this.checked) {
            var c = colors[this.value];
            var label = document.querySelector('label[for="status_' + this.value + '"]');
            label.style.background   = c.bg;
            label.style.borderColor  = c.color;
            label.style.color        = c.color;
        }
    });
});

// Payment radio cards — visual selection
document.querySelectorAll('.payment-radio').forEach(function(radio) {
    radio.addEventListener('change', function() {
        var colors = {
            'unpaid':   { color: '#856404', bg: 'rgba(255,193,7,.1)' },
            'paid':     { color: '#198754', bg: 'rgba(25,135,84,.1)' },
            'refunded': { color: '#055160', bg: 'rgba(13,202,240,.1)' },
        };
        document.querySelectorAll('.pay-label').forEach(function(label) {
            label.style.background   = '#fff';
            label.style.borderColor  = '#dee2e6';
            label.style.color        = '#6c757d';
        });
        if (this.checked) {
            var c = colors[this.value];
            var label = document.querySelector('label[for="pay_' + this.value + '"]');
            label.style.background   = c.bg;
            label.style.borderColor  = c.color;
            label.style.color        = c.color;
        }
    });
});

// Delete confirmation
var deleteBtn = document.getElementById('deleteBookingBtn');
if (deleteBtn) {
    deleteBtn.addEventListener('click', function() {
        Swal.fire({
            title: 'Delete Booking?',
            html: 'Are you sure you want to permanently delete booking <strong>{{ $booking->booking_reference }}</strong>?<br><small class="text-danger">This cannot be undone.</small>',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="bi bi-trash me-1"></i> Yes, Delete',
            cancelButtonText: 'Keep Booking'
        }).then(function(result) {
            if (result.isConfirmed) {
                document.getElementById('deleteBookingForm').submit();
            }
        });
    });
}
</script>
@endpush