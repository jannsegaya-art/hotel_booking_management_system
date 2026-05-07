@extends('layouts.admin')
@section('title', 'Revenue Monitoring')

@section('content')
<div class="container-fluid py-4">
    <div class="mb-4">
        <h2 class="fw-bold mb-0" style="color:var(--primary)"><i class="bi bi-graph-up me-2"></i>Revenue Monitoring</h2>
        <p class="text-muted mb-0">Track earnings across daily, weekly, and monthly periods</p>
    </div>

    {{-- Period Filter --}}
    <div class="card border-0 shadow-sm mb-4" style="border-radius:12px;">
        <div class="card-body p-3">
            <form method="GET" class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="form-label small fw-semibold">Year</label>
                    <select name="year" class="form-select form-select-sm">
                        @for($y = date('Y'); $y >= date('Y')-3; $y--)
                        <option value="{{ $y }}" {{ $year == $y ? 'selected':'' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-semibold">Month (for daily view)</label>
                    <select name="month" class="form-select form-select-sm">
                        @for($m=1;$m<=12;$m++)
                        <option value="{{ $m }}" {{ $month == $m ? 'selected':'' }}>{{ date('F',mktime(0,0,0,$m,1)) }}</option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-sm w-100 text-white" style="background:var(--primary);">
                        <i class="bi bi-filter me-1"></i> Apply
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Summary Cards --}}
    <div class="row g-3 mb-4">
        @foreach([
            ['Today',       '₱'.number_format($totalToday,2),    'calendar-day',     'var(--primary)'],
            ['This Week',   '₱'.number_format($totalThisWeek,2), 'calendar-week',    'var(--secondary)'],
            ['This Month',  '₱'.number_format($totalThisMonth,2),'calendar-month',   '#28a745'],
            ['All Time',    '₱'.number_format($totalRevenue,2),  'trophy-fill',      '#6f42c1'],
        ] as $card)
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm p-4" style="border-radius:12px;">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center"
                         style="width:50px;height:50px;background:{{ $card[3] }}20;">
                        <i class="bi bi-{{ $card[2] }}" style="font-size:1.4rem;color:{{ $card[3] }};"></i>
                    </div>
                    <div>
                        <div class="fw-bold fs-5" style="color:{{ $card[3] }};">{{ $card[1] }}</div>
                        <div class="text-muted small">{{ $card[0] }}</div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Monthly Chart --}}
    <div class="card border-0 shadow-sm mb-4" style="border-radius:12px;">
        <div class="card-header py-3" style="background:var(--primary);border-radius:12px 12px 0 0;">
            <h5 class="text-white mb-0"><i class="bi bi-bar-chart me-2"></i>Monthly Revenue — {{ $year }}</h5>
        </div>
        <div class="card-body p-4">
            <canvas id="revenueChart" height="100"></canvas>
        </div>
    </div>

    <div class="row g-4">
        {{-- Monthly Table --}}
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm" style="border-radius:12px;">
                <div class="card-header py-3" style="background:var(--secondary);border-radius:12px 12px 0 0;">
                    <h5 class="text-white mb-0"><i class="bi bi-table me-2"></i>Monthly Breakdown</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead style="background:#f8f9fc;">
                                <tr><th class="ps-4">Month</th><th>Bookings</th><th class="pe-4">Revenue</th></tr>
                            </thead>
                            <tbody>
                                @forelse($monthlyRevenue as $row)
                                <tr>
                                    <td class="ps-4 fw-semibold">{{ date('F Y', mktime(0,0,0,$row->month,1,$year)) }}</td>
                                    <td>{{ $row->count }}</td>
                                    <td class="pe-4 fw-bold" style="color:var(--secondary);">₱{{ number_format($row->total,2) }}</td>
                                </tr>
                                @empty
                                <tr><td colspan="3" class="text-center py-4 text-muted">No revenue data.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Revenue by Room --}}
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm" style="border-radius:12px;">
                <div class="card-header py-3" style="background:var(--secondary);border-radius:12px 12px 0 0;">
                    <h5 class="text-white mb-0"><i class="bi bi-building me-2"></i>Revenue by Room</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead style="background:#f8f9fc;">
                                <tr><th class="ps-4">Room</th><th>Type</th><th>Bookings</th><th class="pe-4">Revenue</th></tr>
                            </thead>
                            <tbody>
                                @forelse($revenueByRoom as $row)
                                <tr>
                                    <td class="ps-4 fw-bold" style="color:var(--primary);">{{ $row->room?->room_number ?? '—' }}</td>
                                    <td>{{ $row->room?->room_type ?? '—' }}</td>
                                    <td>{{ $row->count }}</td>
                                    <td class="pe-4 fw-bold" style="color:var(--secondary);">₱{{ number_format($row->total,2) }}</td>
                                </tr>
                                @empty
                                <tr><td colspan="4" class="text-center py-4 text-muted">No data.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('revenueChart').getContext('2d');
new Chart(ctx, {
    type: 'bar',
    data: {
        labels: @json($monthLabels),
        datasets: [{
            label: 'Revenue ($)',
            data: @json($monthData),
            backgroundColor: 'rgba(26,60,143,0.8)',
            borderColor: 'rgba(26,60,143,1)',
            borderWidth: 1,
            borderRadius: 6,
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
            y: { beginAtZero: true, ticks: { callback: v => '20b1' + v.toLocaleString() } }
        }
    }
});
</script>
@endpush
