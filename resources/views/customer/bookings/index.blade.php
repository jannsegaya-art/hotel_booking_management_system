@extends('layouts.customer')
@section('title', 'My Bookings')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h2 class="fw-bold mb-0" style="color:var(--primary)"><i class="bi bi-calendar-check me-2"></i>My Bookings</h2>
            <p class="text-muted mb-0">View and manage all your reservations</p>
        </div>
        <a href="{{ route('customer.bookings.create') }}" class="btn text-white fw-semibold" style="background:var(--primary);">
            <i class="bi bi-plus-circle me-1"></i> New Booking
        </a>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="card border-0 shadow-sm" style="border-radius:12px;">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead style="background:#f8f9fc;">
                        <tr>
                            <th class="ps-4 py-3">Ref #</th><th>Room</th><th>Check-In</th><th>Check-Out</th>
                            <th>Nights</th><th>Total</th><th>Payment</th><th>Status</th><th class="pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($bookings as $b)
                        <tr>
                            <td class="ps-4 fw-semibold" style="color:var(--primary);">{{ $b->booking_reference }}</td>
                            <td>
                                <div class="fw-semibold">Room {{ $b->room->room_number }}</div>
                                <div class="small text-muted">{{ $b->room->room_type }}</div>
                            </td>
                            <td>{{ $b->check_in_date->format('M d, Y') }}</td>
                            <td>{{ $b->check_out_date->format('M d, Y') }}</td>
                            <td>{{ $b->nights }}</td>
                            <td class="fw-bold" style="color:var(--secondary);">₱{{ number_format($b->total_amount,2) }}</td>
                            <td><span class="badge bg-{{ $b->payment_status==='paid'?'success':($b->payment_status==='refunded'?'info':'warning text-dark') }}">{{ ucfirst($b->payment_status) }}</span></td>
                            <td>
                                @php $sc=['pending'=>'warning text-dark','confirmed'=>'primary','checked_in'=>'success','checked_out'=>'secondary','cancelled'=>'danger']; @endphp
                                <span class="badge bg-{{ $sc[$b->status] ?? 'secondary' }}">{{ ucfirst(str_replace('_',' ',$b->status)) }}</span>
                            </td>
                            <td class="pe-4">
                                <div class="d-flex gap-1">
                                    <a href="{{ route('customer.bookings.show', $b) }}" class="btn btn-sm btn-outline-primary" title="View">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    @if(in_array($b->status, ['pending','confirmed']))
                                    <button class="btn btn-sm btn-outline-danger cancel-btn"
                                            data-id="{{ $b->id }}" data-ref="{{ $b->booking_reference }}" title="Cancel">
                                        <i class="bi bi-x-circle"></i>
                                    </button>
                                    <form id="cancelForm-{{ $b->id }}" method="POST" action="{{ route('customer.bookings.cancel', $b) }}" class="d-none">@csrf</form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center py-5 text-muted">
                                <i class="bi bi-calendar-x" style="font-size:3rem;display:block;"></i>
                                No bookings yet. <a href="{{ route('customer.bookings.create') }}">Book your first room!</a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($bookings->hasPages())
            <div class="d-flex justify-content-center py-3">{{ $bookings->links() }}</div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.querySelectorAll('.cancel-btn').forEach(btn => {
    btn.addEventListener('click', function(){
        const id = this.dataset.id, ref = this.dataset.ref;
        Swal.fire({ title:'Cancel Booking?', html:`Cancel booking <strong>${ref}</strong>? This action cannot be undone.`,
            icon:'warning', showCancelButton:true, confirmButtonColor:'#dc3545',
            confirmButtonText:'Yes, Cancel', cancelButtonText:'Keep Booking',
        }).then(r => { if(r.isConfirmed) document.getElementById('cancelForm-'+id).submit(); });
    });
});
</script>
@endpush
