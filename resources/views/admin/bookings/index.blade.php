@extends('layouts.admin')
@section('title', 'Booking Management')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h2 class="fw-bold mb-0" style="color:var(--primary)"><i class="bi bi-calendar-check me-2"></i>Booking Management</h2>
            <p class="text-muted mb-0">Manage all hotel reservations</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.bookings.availability') }}" class="btn btn-outline-primary">
                <i class="bi bi-search me-1"></i> Check Availability
            </a>
            <a href="{{ route('admin.bookings.create') }}" class="btn text-white fw-semibold" style="background:var(--primary);">
                <i class="bi bi-plus-circle me-1"></i> New Booking
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    {{-- Filters --}}
    <div class="card border-0 shadow-sm mb-4" style="border-radius:12px;">
        <div class="card-body p-3">
            <form method="GET" class="row g-2 align-items-end">
                <div class="col-md-4">
                    <label class="form-label small fw-semibold">Search</label>
                    <input type="text" name="search" class="form-control form-control-sm" placeholder="Booking ref or guest name..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-semibold">Status</label>
                    <select name="status" class="form-select form-select-sm">
                        <option value="">All Status</option>
                        @foreach(['pending','confirmed','checked_in','checked_out','cancelled'] as $s)
                        <option value="{{ $s }}" {{ request('status') == $s ? 'selected' : '' }}>{{ ucfirst(str_replace('_',' ',$s)) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-sm text-white w-100" style="background:var(--primary);">
                        <i class="bi bi-filter me-1"></i> Filter
                    </button>
                </div>
                <div class="col-md-2">
                    <a href="{{ route('admin.bookings.index') }}" class="btn btn-sm btn-outline-secondary w-100">
                        <i class="bi bi-x me-1"></i> Clear
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm" style="border-radius:12px;">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead style="background:#f8f9fc;">
                        <tr>
                            <th class="ps-4 py-3">Ref #</th>
                            <th>Guest</th>
                            <th>Room</th>
                            <th>Check-In</th>
                            <th>Check-Out</th>
                            <th>Amount</th>
                            <th>Payment</th>
                            <th>Status</th>
                            <th>Staff</th>
                            <th class="pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($bookings as $booking)
                        <tr>
                            <td class="ps-4">
                                <span class="fw-semibold" style="color:var(--primary);">{{ $booking->booking_reference }}</span>
                            </td>
                            <td>
                                <div class="fw-semibold">{{ $booking->user->name }}</div>
                                <div class="small text-muted">{{ $booking->user->email }}</div>
                            </td>
                            <td>
                                <span class="fw-semibold">Room {{ $booking->room->room_number }}</span>
                                <div class="small text-muted">{{ $booking->room->room_type }}</div>
                            </td>
                            <td>{{ $booking->check_in_date->format('M d, Y') }}</td>
                            <td>{{ $booking->check_out_date->format('M d, Y') }}</td>
                            <td class="fw-semibold" style="color:var(--secondary);">₱{{ number_format($booking->total_amount, 2) }}</td>
                            <td>
                                <span class="badge rounded-pill bg-{{ $booking->payment_status === 'paid' ? 'success' : ($booking->payment_status === 'refunded' ? 'info' : 'warning text-dark') }}">
                                    {{ ucfirst($booking->payment_status) }}
                                </span>
                            </td>
                            <td>
                                @php
                                $statusColors = ['pending'=>'warning text-dark','confirmed'=>'primary','checked_in'=>'success','checked_out'=>'secondary','cancelled'=>'danger'];
                                @endphp
                                <span class="badge rounded-pill bg-{{ $statusColors[$booking->status] ?? 'secondary' }}">
                                    {{ ucfirst(str_replace('_',' ',$booking->status)) }}
                                </span>
                            </td>
                            <td>{{ $booking->staff?->name ?? '<span class="text-muted small">Unassigned</span>' }}</td>
                            <td class="pe-4">
                                <div class="d-flex gap-1">
                                    <a href="{{ route('admin.bookings.show', $booking) }}" class="btn btn-sm btn-outline-primary" title="View">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.bookings.edit', $booking) }}" class="btn btn-sm btn-outline-warning" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <button class="btn btn-sm btn-outline-danger delete-btn" title="Delete"
                                            data-id="{{ $booking->id }}" data-ref="{{ $booking->booking_reference }}">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="text-center py-5 text-muted">
                                <i class="bi bi-calendar-x" style="font-size:3rem; display:block; margin-bottom:8px;"></i>
                                No bookings found.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($bookings->hasPages())
            <div class="d-flex justify-content-center py-3">
                {{ $bookings->appends(request()->query())->links() }}
            </div>
            @endif
        </div>
    </div>
</div>

{{-- Delete Forms --}}
@foreach($bookings as $booking)
<form id="deleteForm-{{ $booking->id }}" method="POST" action="{{ route('admin.bookings.destroy', $booking) }}" class="d-none">
    @csrf @method('DELETE')
</form>
@endforeach
@endsection

@push('scripts')
<script>
document.querySelectorAll('.delete-btn').forEach(btn => {
    btn.addEventListener('click', function(){
        const id  = this.dataset.id;
        const ref = this.dataset.ref;
        Swal.fire({
            title: 'Delete Booking?',
            html: `Are you sure you want to delete booking <strong>${ref}</strong>? This cannot be undone.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, Delete',
            cancelButtonText: 'Cancel',
        }).then(result => {
            if(result.isConfirmed) document.getElementById('deleteForm-' + id).submit();
        });
    });
});
</script>
@endpush
