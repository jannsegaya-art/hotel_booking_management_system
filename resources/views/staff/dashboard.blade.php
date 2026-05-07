@extends('layouts.staff')
@section('title', 'Staff Dashboard')

@section('content')
<div class="container-fluid py-4">
    <div class="mb-4">
        <h2 class="fw-bold mb-0" style="color:var(--primary)">
            <i class="bi bi-speedometer2 me-2"></i>Welcome back, {{ auth()->user()->name }}!
        </h2>
        <p class="text-muted mb-0">Here's your task overview for today.</p>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    {{-- Stats --}}
    <div class="row g-3 mb-4">
        @foreach([
            ['Assigned','bi-calendar-check','var(--primary)',$stats['assigned']],
            ['Pending','bi-hourglass-split','#ffc107',$stats['pending']],
            ['Confirmed','bi-check-circle','#17a2b8',$stats['confirmed']],
            ['Checked In','bi-door-open','#28a745',$stats['checked_in']],
            ['Checked Out','bi-door-closed','#6c757d',$stats['checked_out']],
        ] as $s)
        <div class="col-6 col-md">
            <div class="card border-0 shadow-sm text-center p-3" style="border-radius:12px;">
                <i class="bi {{ $s[1] }} mb-2" style="font-size:1.8rem;color:{{ $s[2] }};"></i>
                <div class="fw-bold fs-4" style="color:{{ $s[2] }};">{{ $s[3] }}</div>
                <div class="text-muted small">{{ $s[0] }}</div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="row g-4">
        {{-- Room Status --}}
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100" style="border-radius:12px;">
                <div class="card-header py-3" style="background:var(--primary);border-radius:12px 12px 0 0;">
                    <h6 class="text-white mb-0"><i class="bi bi-building me-2"></i>Room Status</h6>
                </div>
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between mb-3">
                        <div class="text-center">
                            <div class="fw-bold fs-3 text-success">{{ $available_rooms }}</div>
                            <div class="text-muted small">Available</div>
                        </div>
                        <div class="text-center">
                            <div class="fw-bold fs-3 text-danger">{{ $occupied_rooms }}</div>
                            <div class="text-muted small">Occupied</div>
                        </div>
                    </div>
                    @php $total = $available_rooms + $occupied_rooms; $pct = $total > 0 ? round($occupied_rooms/$total*100) : 0; @endphp
                    <div class="mb-1 d-flex justify-content-between small">
                        <span>Occupancy Rate</span>
                        <span class="fw-bold">{{ $pct }}%</span>
                    </div>
                    <div class="progress" style="height:8px;">
                        <div class="progress-bar" role="progressbar"
                             style="width:{{ $pct }}%;background:var(--primary);"
                             aria-valuenow="{{ $pct }}" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Notifications --}}
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100" style="border-radius:12px;">
                <div class="card-header py-3" style="background:var(--secondary);border-radius:12px 12px 0 0;">
                    <h6 class="text-white mb-0"><i class="bi bi-bell me-2"></i>Notifications</h6>
                </div>
                <div class="card-body p-3">
                    @forelse($notifications as $n)
                    <div class="d-flex gap-2 mb-2 p-2 rounded" style="background:#f8f9fc;">
                        <i class="bi bi-{{ ['success'=>'check-circle','info'=>'info-circle','warning'=>'exclamation-triangle','danger'=>'x-circle'][$n->type] ?? 'bell' }}"
                           style="color:{{ ['success'=>'#28a745','info'=>'#17a2b8','warning'=>'#ffc107','danger'=>'#dc3545'][$n->type] ?? 'var(--primary)' }};flex-shrink:0;margin-top:2px;"></i>
                        <div>
                            <div class="small fw-semibold">{{ $n->title }}</div>
                            <div class="small text-muted">{{ Str::limit($n->message,60) }}</div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center text-muted py-3 small">No new notifications.</div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Recent Activity --}}
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100" style="border-radius:12px;">
                <div class="card-header py-3" style="background:var(--secondary);border-radius:12px 12px 0 0;">
                    <h6 class="text-white mb-0"><i class="bi bi-clock-history me-2"></i>My Recent Activity</h6>
                </div>
                <div class="card-body p-3">
                    @forelse($recentLogs as $log)
                    <div class="d-flex gap-2 mb-2">
                        <div class="rounded-circle bg-light d-flex align-items-center justify-content-center flex-shrink-0"
                             style="width:28px;height:28px;">
                            <i class="bi bi-activity small" style="color:var(--primary);"></i>
                        </div>
                        <div>
                            <div class="small">{{ $log->description }}</div>
                            <div class="small text-muted">{{ $log->created_at->diffForHumans() }}</div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center text-muted py-3 small">No recent activity.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- My Bookings --}}
    <div class="card border-0 shadow-sm mt-4" style="border-radius:12px;">
        <div class="card-header py-3 d-flex justify-content-between align-items-center" style="background:var(--primary);border-radius:12px 12px 0 0;">
            <h5 class="text-white mb-0"><i class="bi bi-calendar-check me-2"></i>My Assigned Bookings</h5>
            <a href="{{ route('staff.bookings.index') }}" class="btn btn-sm text-white" style="border:1px solid rgba(255,255,255,0.5);">View All</a>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead style="background:#f8f9fc;">
                        <tr>
                            <th class="ps-4">Ref</th><th>Guest</th><th>Room</th>
                            <th>Check-In</th><th>Check-Out</th><th>Status</th><th class="pe-4">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($myBookings as $booking)
                        <tr>
                            <td class="ps-4 fw-semibold" style="color:var(--primary);">{{ $booking->booking_reference }}</td>
                            <td>{{ $booking->user->name }}</td>
                            <td>Room {{ $booking->room->room_number }}</td>
                            <td>{{ $booking->check_in_date->format('M d, Y') }}</td>
                            <td>{{ $booking->check_out_date->format('M d, Y') }}</td>
                            <td>
                                @php $sc=['pending'=>'warning text-dark','confirmed'=>'primary','checked_in'=>'success','checked_out'=>'secondary','cancelled'=>'danger']; @endphp
                                <span class="badge bg-{{ $sc[$booking->status] ?? 'secondary' }}">{{ ucfirst(str_replace('_',' ',$booking->status)) }}</span>
                            </td>
                            <td class="pe-4">
                                <a href="{{ route('staff.bookings.show', $booking) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="7" class="text-center py-4 text-muted">No assigned bookings yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
