@extends('layouts.admin')
@section('title', 'Customer Details')

@section('content')
<div class="container-fluid py-4">
    <div class="mb-4">
        <a href="{{ route('admin.customers.index') }}" class="btn btn-sm btn-outline-secondary mb-2">
            <i class="bi bi-arrow-left me-1"></i> Back
        </a>
        <h2 class="fw-bold mb-0" style="color:var(--primary)">Customer Profile: {{ $user->name }}</h2>
    </div>

    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm" style="border-radius:12px;">
                <div class="card-body p-4 text-center">
                    <img src="{{ $user->profile_photo_url }}"
                         alt="{{ $user->name }}"
                         class="rounded-circle mx-auto mb-3 d-block shadow"
                         style="width:90px; height:90px; object-fit:cover; border:3px solid var(--secondary);"
                         onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=1a3c8f&color=fff&size=128&bold=true'">
                    <h5 class="fw-bold mb-1">{{ $user->name }}</h5>
                    <p class="text-muted mb-3">{{ $user->email }}</p>
                    <span class="badge bg-{{ $user->status === 'active' ? 'success' : 'danger' }} mb-3">
                        {{ ucfirst($user->status) }}
                    </span>
                    <table class="table table-sm table-borderless text-start">
                        <tr><td class="text-muted">Phone</td><td>{{ $user->phone ?? '—' }}</td></tr>
                        <tr><td class="text-muted">Address</td><td>{{ $user->address ?? '—' }}</td></tr>
                        <tr><td class="text-muted">Joined</td><td>{{ $user->created_at->format('M d, Y') }}</td></tr>
                        <tr><td class="text-muted">Total Bookings</td><td>{{ $bookings->count() }}</td></tr>
                    </table>
                    <form method="POST" action="{{ route('admin.customers.toggle', $user) }}">
                        @csrf
                        <button type="submit" class="btn w-100 btn-{{ $user->status === 'active' ? 'warning' : 'success' }}">
                            <i class="bi bi-{{ $user->status === 'active' ? 'pause-circle' : 'play-circle' }} me-1"></i>
                            {{ $user->status === 'active' ? 'Deactivate' : 'Activate' }} Account
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-4" style="border-radius:12px;">
                <div class="card-header py-3" style="background:var(--primary);border-radius:12px 12px 0 0;">
                    <h5 class="text-white mb-0"><i class="bi bi-calendar-check me-2"></i>Booking History</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead style="background:#f8f9fc;">
                                <tr>
                                    <th class="ps-4">Ref</th><th>Room</th>
                                    <th>Check-In</th><th>Check-Out</th>
                                    <th>Amount</th><th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($bookings as $booking)
                                <tr>
                                    <td class="ps-4 fw-semibold" style="color:var(--primary);">{{ $booking->booking_reference }}</td>
                                    <td>Room {{ $booking->room->room_number }}</td>
                                    <td>{{ $booking->check_in_date->format('M d, Y') }}</td>
                                    <td>{{ $booking->check_out_date->format('M d, Y') }}</td>
                                    <td class="fw-semibold" style="color:var(--secondary);">${{ number_format($booking->total_amount,2) }}</td>
                                    <td>
                                        @php $sc=['pending'=>'warning text-dark','confirmed'=>'primary','checked_in'=>'success','checked_out'=>'secondary','cancelled'=>'danger']; @endphp
                                        <span class="badge bg-{{ $sc[$booking->status] ?? 'secondary' }}">{{ ucfirst(str_replace('_',' ',$booking->status)) }}</span>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="6" class="text-center py-4 text-muted">No bookings yet.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            @if($ratings->count() > 0)
            <div class="card border-0 shadow-sm" style="border-radius:12px;">
                <div class="card-header py-3" style="background:var(--secondary);border-radius:12px 12px 0 0;">
                    <h5 class="text-white mb-0"><i class="bi bi-star me-2"></i>Ratings & Feedback</h5>
                </div>
                <div class="card-body p-3">
                    @foreach($ratings as $rating)
                    <div class="p-3 mb-2 rounded" style="background:#f8f9fc;">
                        <div class="d-flex justify-content-between">
                            <span class="fw-semibold">Room {{ $rating->room->room_number }}</span>
                            <div>
                                @for($i=1;$i<=5;$i++)
                                <i class="bi bi-star{{ $i<=$rating->rating ? '-fill' : '' }}" style="color:var(--secondary);"></i>
                                @endfor
                            </div>
                        </div>
                        @if($rating->comment)<p class="text-muted small mb-0 mt-1">"{{ $rating->comment }}"</p>@endif
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
