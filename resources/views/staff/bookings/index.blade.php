@extends('layouts.staff')
@section('title', 'My Assigned Bookings')

@section('content')
<div class="container-fluid py-4">

    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h4 class="fw-bold mb-0" style="color:var(--primary);">
                <i class="bi bi-calendar-check me-2"></i>My Assigned Bookings
            </h4>
            <p class="text-muted small mb-0">Manage bookings assigned to you</p>
        </div>
        <a href="{{ route('staff.bookings.all') }}" class="btn btn-outline-primary btn-sm">
            <i class="bi bi-grid me-1"></i> View All Hotel Bookings
        </a>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show shadow-sm">
        <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show shadow-sm">
        <i class="bi bi-exclamation-circle-fill me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    {{-- Filter --}}
    <div class="card border-0 shadow-sm mb-3" style="border-radius:12px;">
        <div class="card-body p-3">
            <form method="GET" class="row g-2 align-items-end">
                <div class="col-md-4">
                    <label class="form-label small fw-semibold mb-1">Filter by Status</label>
                    <select name="status" class="form-select form-select-sm">
                        <option value="">All Status</option>
                        @foreach(['pending','confirmed','checked_in','checked_out','cancelled'] as $s)
                        <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>
                            {{ ucfirst(str_replace('_',' ',$s)) }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-sm text-white w-100" style="background:var(--primary);">
                        <i class="bi bi-funnel me-1"></i>Filter
                    </button>
                </div>
                <div class="col-md-2">
                    <a href="{{ route('staff.bookings.index') }}" class="btn btn-sm btn-outline-secondary w-100">
                        <i class="bi bi-x me-1"></i>Clear
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- Bookings Table --}}
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
                            <th class="pe-4 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($bookings as $b)
                        <tr>
                            <td class="ps-4">
                                <span class="fw-semibold" style="color:var(--primary);">{{ $b->booking_reference }}</span>
                            </td>
                            <td>
                                <div class="fw-semibold small">{{ $b->user->name }}</div>
                                <div class="text-muted" style="font-size:.75rem;">{{ $b->user->email }}</div>
                            </td>
                            <td>
                                <span class="fw-semibold small">Room {{ $b->room->room_number }}</span>
                                <div class="text-muted" style="font-size:.75rem;">{{ $b->room->room_type }}</div>
                            </td>
                            <td class="small">{{ $b->check_in_date->format('M d, Y') }}</td>
                            <td class="small">{{ $b->check_out_date->format('M d, Y') }}</td>
                            <td class="fw-semibold small" style="color:var(--secondary);">
                                ₱{{ number_format($b->total_amount, 2) }}
                            </td>
                            <td>
                                @php $pc=['unpaid'=>'warning text-dark','paid'=>'success','refunded'=>'info']; @endphp
                                <span class="badge bg-{{ $pc[$b->payment_status] ?? 'secondary' }} small">
                                    {{ ucfirst($b->payment_status) }}
                                </span>
                            </td>
                            <td>
                                @php $sc=['pending'=>'warning text-dark','confirmed'=>'primary','checked_in'=>'success','checked_out'=>'secondary','cancelled'=>'danger']; @endphp
                                <span class="badge bg-{{ $sc[$b->status] ?? 'secondary' }} small">
                                    {{ ucfirst(str_replace('_',' ',$b->status)) }}
                                </span>
                            </td>
                            <td class="pe-4">
                                <div class="d-flex gap-1 justify-content-center">
                                    {{-- View --}}
                                    <a href="{{ route('staff.bookings.show', $b) }}"
                                       class="btn btn-sm btn-outline-primary"
                                       title="View Details">
                                        <i class="bi bi-eye"></i>
                                    </a>

                                    {{-- Edit (only if not checked_out) --}}
                                    @if(!in_array($b->status, ['checked_out']))
                                    <a href="{{ route('staff.bookings.edit', $b) }}"
                                       class="btn btn-sm btn-outline-warning"
                                       title="Edit Booking">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    @endif

                                    {{-- Delete (only if pending or cancelled) --}}
                                    @if(in_array($b->status, ['pending', 'cancelled']))
                                    <button class="btn btn-sm btn-outline-danger delete-btn"
                                            data-id="{{ $b->id }}"
                                            data-ref="{{ $b->booking_reference }}"
                                            title="Delete Booking">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                    <form id="deleteForm-{{ $b->id }}"
                                          method="POST"
                                          action="{{ route('staff.bookings.destroy', $b) }}"
                                          class="d-none">
                                        @csrf @method('DELETE')
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center py-5 text-muted">
                                <i class="bi bi-calendar-x d-block mb-2" style="font-size:3rem;"></i>
                                No assigned bookings found.
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

    {{-- Legend --}}
    <div class="mt-3 small text-muted">
        <i class="bi bi-info-circle me-1"></i>
        <strong>Edit</strong> is available for bookings that are not yet checked out. &nbsp;|&nbsp;
        <strong>Delete</strong> is only available for <span class="badge bg-warning text-dark">Pending</span>
        or <span class="badge bg-danger">Cancelled</span> bookings.
    </div>

</div>
@endsection

@push('scripts')
<script>
document.querySelectorAll('.delete-btn').forEach(function(btn) {
    btn.addEventListener('click', function() {
        var id  = this.dataset.id;
        var ref = this.dataset.ref;
        Swal.fire({
            title: 'Delete Booking?',
            html: 'Are you sure you want to delete booking <strong>' + ref + '</strong>?<br><small class="text-muted">This action cannot be undone.</small>',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="bi bi-trash me-1"></i> Yes, Delete',
            cancelButtonText: 'Cancel'
        }).then(function(result) {
            if (result.isConfirmed) {
                document.getElementById('deleteForm-' + id).submit();
            }
        });
    });
});
</script>
@endpush