@extends('layouts.staff')
@section('title', 'All Hotel Bookings')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h2 class="fw-bold mb-0" style="color:var(--primary)"><i class="bi bi-grid me-2"></i>All Hotel Bookings</h2>
            <p class="text-muted mb-0">Overview of all reservations in the hotel</p>
        </div>
        <a href="{{ route('staff.bookings.index') }}" class="btn btn-outline-primary btn-sm">
            <i class="bi bi-person-check me-1"></i> My Assigned Only
        </a>
    </div>

    <div class="card border-0 shadow-sm mb-3" style="border-radius:12px;">
        <div class="card-body p-3">
            <form method="GET" class="row g-2 align-items-end">
                <div class="col-md-4">
                    <select name="status" class="form-select form-select-sm">
                        <option value="">All Status</option>
                        @foreach(['pending','confirmed','checked_in','checked_out','cancelled'] as $s)
                        <option value="{{ $s }}" {{ request('status')===$s?'selected':'' }}>{{ ucfirst(str_replace('_',' ',$s)) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-sm text-white w-100" style="background:var(--primary);">Filter</button>
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
                            <th class="ps-4 py-3">Ref</th><th>Guest</th><th>Room</th>
                            <th>Check-In</th><th>Check-Out</th><th>Assigned To</th><th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($bookings as $b)
                        <tr>
                            <td class="ps-4 fw-semibold" style="color:var(--primary);">{{ $b->booking_reference }}</td>
                            <td>{{ $b->user->name }}</td>
                            <td>Room {{ $b->room->room_number }}</td>
                            <td>{{ $b->check_in_date->format('M d, Y') }}</td>
                            <td>{{ $b->check_out_date->format('M d, Y') }}</td>
                            <td>
                                @if($b->staff)
                                <span class="badge" style="background:var(--primary);color:#fff;">{{ $b->staff->name }}</span>
                                @else
                                <span class="text-muted small">Unassigned</span>
                                @endif
                            </td>
                            <td>
                                @php $sc=['pending'=>'warning text-dark','confirmed'=>'primary','checked_in'=>'success','checked_out'=>'secondary','cancelled'=>'danger']; @endphp
                                <span class="badge bg-{{ $sc[$b->status] ?? 'secondary' }}">{{ ucfirst(str_replace('_',' ',$b->status)) }}</span>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="7" class="text-center py-5 text-muted">No bookings found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($bookings->hasPages())
            <div class="d-flex justify-content-center py-3">{{ $bookings->appends(request()->query())->links() }}</div>
            @endif
        </div>
    </div>
</div>
@endsection
