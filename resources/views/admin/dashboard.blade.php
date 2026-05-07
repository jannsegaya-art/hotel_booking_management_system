@extends('layouts.admin')
@section('title', 'Dashboard')

@section('content')

{{-- Welcome Header --}}
<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <div>
        <h4 class="mb-0 fw-700" style="font-family:'Playfair Display',serif; color:var(--primary);">
            Good {{ now()->format('H') < 12 ? 'Morning' : (now()->format('H') < 17 ? 'Afternoon' : 'Evening') }},
            {{ explode(' ', auth()->user()->name)[0] }}! 👋
        </h4>
        <small class="text-muted">{{ now()->format('l, F j, Y') }}</small>
    </div>
    <a href="{{ route('admin.bookings.create') }}" class="btn btn-gold">
        <i class="bi bi-plus-circle me-1"></i> New Booking
    </a>
</div>

{{-- Alert Notices --}}
@if($pendingStaff > 0)
<div class="alert alert-warning d-flex align-items-center gap-2 mb-3 py-2">
    <i class="bi bi-exclamation-triangle-fill fs-5"></i>
    <div class="small">
        <strong>{{ $pendingStaff }} staff account(s)</strong> pending approval.
        <a href="{{ route('admin.staff.index') }}" class="alert-link ms-1">Review now →</a>
    </div>
</div>
@endif
@if($pendingBookings > 0)
<div class="alert alert-info d-flex align-items-center gap-2 mb-3 py-2">
    <i class="bi bi-info-circle-fill fs-5"></i>
    <div class="small">
        <strong>{{ $pendingBookings }} booking(s)</strong> awaiting confirmation.
        <a href="{{ route('admin.bookings.index') }}" class="alert-link ms-1">View bookings →</a>
    </div>
</div>
@endif

{{-- Stats Cards --}}
<div class="row g-3 mb-4">
    @php
    $statsData = [
        ['Total Staff',     $totalStaff,                         'bi-people-fill',         '#1a3c8f', 'rgba(26,60,143,.12)',  route('admin.staff.index')],
        ['Customers',       $totalCustomers,                     'bi-person-badge-fill',   '#198754', 'rgba(25,135,84,.12)', route('admin.customers.index')],
        ['Total Bookings',  $totalBookings,                      'bi-calendar-check-fill', '#c8a84b', 'rgba(200,168,75,.12)',route('admin.bookings.index')],
        ['Available Rooms', $availableRooms,                     'bi-door-open-fill',      '#0d6efd', 'rgba(13,110,253,.12)',route('admin.rooms.index')],
        ['Occupied Rooms',  $occupiedRooms,                      'bi-house-fill',          '#dc3545', 'rgba(220,53,69,.12)', route('admin.rooms.index')],
        ['Total Revenue',   '₱'.number_format($totalRevenue, 2), 'bi-cash-stack',          '#6f42c1', 'rgba(111,66,193,.12)',route('admin.revenue')],
    ];
    @endphp
    @foreach($statsData as [$label, $value, $icon, $color, $bg, $link])
    <div class="col-6 col-md-4 col-xl-2">
        <a href="{{ $link }}" class="text-decoration-none">
            <div class="card stat-card h-100">
                <div class="card-body p-3">
                    <div class="stat-icon mb-2" style="background:{{ $bg }}; color:{{ $color }};">
                        <i class="bi {{ $icon }}"></i>
                    </div>
                    <div class="fw-700 fs-5" style="color:{{ $color }};">{{ $value }}</div>
                    <div class="text-muted small">{{ $label }}</div>
                </div>
            </div>
        </a>
    </div>
    @endforeach
</div>

{{-- Charts Row --}}
<div class="row g-3 mb-4">
    <div class="col-lg-8">
        <div class="card h-100">
            <div class="card-header card-header-primary d-flex justify-content-between align-items-center py-3">
                <span class="fw-600"><i class="bi bi-graph-up me-2"></i>Monthly Revenue ({{ now()->year }})</span>
                <a href="{{ route('admin.revenue') }}" class="btn btn-sm btn-outline-light">Details</a>
            </div>
            <div class="card-body p-3">
                <canvas id="revenueChart" height="100"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header card-header-gold py-3">
                <span class="fw-600"><i class="bi bi-pie-chart me-2"></i>Booking Status</span>
            </div>
            <div class="card-body d-flex align-items-center justify-content-center p-3">
                <canvas id="statusChart" height="220"></canvas>
            </div>
        </div>
    </div>
</div>

{{-- Recent Bookings + Activity Logs --}}
<div class="row g-3">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header card-header-primary d-flex justify-content-between align-items-center py-3">
                <span class="fw-600"><i class="bi bi-calendar-check me-2"></i>Recent Bookings</span>
                <a href="{{ route('admin.bookings.index') }}" class="btn btn-sm btn-outline-light">View All</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th class="ps-3">Reference</th>
                                <th>Guest</th>
                                <th>Room</th>
                                <th>Check-In</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Staff</th>
                                <th class="pe-3"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentBookings as $booking)
                            <tr>
                                <td class="ps-3">
                                    <code class="small" style="color:var(--primary);">{{ $booking->booking_reference }}</code>
                                </td>
                                <td class="small">{{ $booking->user->name }}</td>
                                <td class="small">Room {{ $booking->room->room_number }}</td>
                                <td class="small">{{ $booking->check_in_date->format('M d, Y') }}</td>
                                <td class="small fw-600" style="color:var(--secondary);">
                                    ₱{{ number_format($booking->total_amount, 2) }}
                                </td>
                                <td>
                                    <span class="badge badge-{{ $booking->status }} px-2 py-1">
                                        {{ ucfirst(str_replace('_', ' ', $booking->status)) }}
                                    </span>
                                </td>
                                <td class="small text-muted">{{ $booking->staff?->name ?? '—' }}</td>
                                <td class="pe-3">
                                    <a href="{{ route('admin.bookings.show', $booking) }}"
                                       class="btn btn-sm btn-outline-primary py-0 px-2">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                    <i class="bi bi-calendar-x d-block mb-1" style="font-size:2rem;"></i>
                                    No bookings yet.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-header card-header-primary d-flex justify-content-between align-items-center py-3">
                <span class="fw-600"><i class="bi bi-journal-text me-2"></i>Recent Activity</span>
                <a href="{{ route('admin.logs') }}" class="btn btn-sm btn-outline-light">View All</a>
            </div>
            <div class="card-body p-0" style="max-height:340px; overflow-y:auto;">
                @forelse($recentLogs as $log)
                <div class="d-flex gap-2 p-3 border-bottom">
                    <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 text-white fw-700"
                         style="width:34px; height:34px; background:var(--primary); font-size:.8rem; min-width:34px;">
                        {{ $log->user ? strtoupper(substr($log->user->name, 0, 1)) : '?' }}
                    </div>
                    <div class="overflow-hidden">
                        <div class="small fw-600 text-truncate">{{ $log->user->name ?? 'System' }}</div>
                        <div class="small text-muted text-truncate">{{ Str::limit($log->description, 55) }}</div>
                        <div class="text-muted" style="font-size:.7rem;">{{ $log->created_at->diffForHumans() }}</div>
                    </div>
                </div>
                @empty
                <div class="text-center text-muted py-4 small">No activity yet.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
// Revenue Bar Chart
(function() {
    var rCtx = document.getElementById('revenueChart');
    if (!rCtx) return;
    new Chart(rCtx.getContext('2d'), {
        type: 'bar',
        data: {
            labels: @json($revenueLabels),
            datasets: [{
                label: 'Revenue (₱)',
                data: @json($revenueData),
                backgroundColor: 'rgba(26,60,143,0.15)',
                borderColor: 'rgba(26,60,143,1)',
                borderWidth: 2,
                borderRadius: 8,
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(0,0,0,0.04)' },
                    ticks: { callback: function(v) { return '₱' + v.toLocaleString(); } }
                },
                x: { grid: { display: false } }
            }
        }
    });
})();

// Booking Status Doughnut Chart
(function() {
    var sCtx = document.getElementById('statusChart');
    if (!sCtx) return;
    new Chart(sCtx.getContext('2d'), {
        type: 'doughnut',
        data: {
            labels: ['Pending', 'Confirmed', 'Checked In', 'Checked Out', 'Cancelled'],
            datasets: [{
                data: @json($bookingStatusData),
                backgroundColor: ['#ffc107','#0d6efd','#198754','#6c757d','#dc3545'],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            cutout: '65%',
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { font: { size: 11 }, padding: 12 }
                }
            }
        }
    });
})();
</script>
@endpush
