@extends('layouts.admin')
@section('title', 'Reports')

@section('content')
<div class="container-fluid py-4">
    <div class="mb-4">
        <h2 class="fw-bold mb-0" style="color:var(--primary)"><i class="bi bi-file-earmark-bar-graph me-2"></i>Reports</h2>
        <p class="text-muted mb-0">Generate detailed reports for bookings, staff, users, revenue, and occupancy</p>
    </div>

    {{-- Type & Date Filters --}}
    <div class="card border-0 shadow-sm mb-4" style="border-radius:12px;">
        <div class="card-body p-3">
            <form method="GET" class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="form-label small fw-semibold">Report Type</label>
                    <select name="type" class="form-select form-select-sm">
                        @foreach(['bookings'=>'Bookings','staff'=>'Staff Performance','users'=>'Customers','revenue'=>'Revenue','occupancy'=>'Occupancy','ratings'=>'Ratings'] as $val => $label)
                        <option value="{{ $val }}" {{ $type === $val ? 'selected':'' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-semibold">From</label>
                    <input type="date" name="from" class="form-control form-control-sm" value="{{ $from ?? '' }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-semibold">To</label>
                    <input type="date" name="to" class="form-control form-control-sm" value="{{ $to ?? '' }}">
                </div>
                <div class="col-md-2">
                    <button class="btn btn-sm w-100 text-white" style="background:var(--primary);">
                        <i class="bi bi-file-earmark-text me-1"></i> Generate
                    </button>
                </div>
                <div class="col-md-2">
                    <button type="button" onclick="window.print()" class="btn btn-sm btn-outline-secondary w-100">
                        <i class="bi bi-printer me-1"></i> Print
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm" style="border-radius:12px;">
        <div class="card-header py-3 d-flex justify-content-between align-items-center" style="background:var(--primary);border-radius:12px 12px 0 0;">
            <h5 class="text-white mb-0 text-capitalize">
                <i class="bi bi-table me-2"></i>{{ ucfirst($type) }} Report
                @if($from || $to) — {{ $from ?? 'Start' }} to {{ $to ?? 'Now' }} @endif
            </h5>
            <span class="badge bg-light text-dark">{{ $data->count() }} records</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                @if($type === 'bookings')
                <table class="table table-hover align-middle mb-0">
                    <thead style="background:#f8f9fc;"><tr>
                        <th class="ps-4">Ref</th><th>Guest</th><th>Room</th><th>Check-In</th><th>Check-Out</th><th>Nights</th><th>Amount</th><th>Payment</th><th>Status</th>
                    </tr></thead>
                    <tbody>
                        @forelse($data as $b)
                        <tr>
                            <td class="ps-4 fw-semibold" style="color:var(--primary);">{{ $b->booking_reference }}</td>
                            <td>{{ $b->user->name }}</td>
                            <td>{{ $b->room->room_number }}</td>
                            <td>{{ $b->check_in_date->format('M d, Y') }}</td>
                            <td>{{ $b->check_out_date->format('M d, Y') }}</td>
                            <td>{{ $b->nights }}</td>
                            <td class="fw-semibold" style="color:var(--secondary);">₱{{ number_format($b->total_amount,2) }}</td>
                            <td><span class="badge bg-{{ $b->payment_status==='paid'?'success':($b->payment_status==='refunded'?'info':'warning text-dark') }}">{{ ucfirst($b->payment_status) }}</span></td>
                            <td><span class="badge bg-{{ ['pending'=>'warning text-dark','confirmed'=>'primary','checked_in'=>'success','checked_out'=>'secondary','cancelled'=>'danger'][$b->status] ?? 'secondary' }}">{{ ucfirst(str_replace('_',' ',$b->status)) }}</span></td>
                        </tr>
                        @empty
                        <tr><td colspan="9" class="text-center py-4 text-muted">No bookings found.</td></tr>
                        @endforelse
                    </tbody>
                </table>

                @elseif($type === 'staff')
                <table class="table table-hover align-middle mb-0">
                    <thead style="background:#f8f9fc;"><tr>
                        <th class="ps-4">Name</th><th>Email</th><th>Status</th><th>Assigned Bookings</th><th>Joined</th>
                    </tr></thead>
                    <tbody>
                        @forelse($data as $s)
                        <tr>
                            <td class="ps-4 fw-semibold">{{ $s->name }}</td>
                            <td>{{ $s->email }}</td>
                            <td><span class="badge bg-{{ $s->status==='active'?'success':($s->status==='pending'?'warning text-dark':'danger') }}">{{ ucfirst($s->status) }}</span></td>
                            <td>{{ $s->total_bookings }}</td>
                            <td>{{ $s->created_at->format('M d, Y') }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-center py-4 text-muted">No staff found.</td></tr>
                        @endforelse
                    </tbody>
                </table>

                @elseif($type === 'users')
                <table class="table table-hover align-middle mb-0">
                    <thead style="background:#f8f9fc;"><tr>
                        <th class="ps-4">Name</th><th>Email</th><th>Phone</th><th>Total Bookings</th><th>Status</th><th>Joined</th>
                    </tr></thead>
                    <tbody>
                        @forelse($data as $u)
                        <tr>
                            <td class="ps-4 fw-semibold">{{ $u->name }}</td>
                            <td>{{ $u->email }}</td>
                            <td>{{ $u->phone ?? '—' }}</td>
                            <td>{{ $u->bookings_count }}</td>
                            <td><span class="badge bg-{{ $u->status==='active'?'success':'danger' }}">{{ ucfirst($u->status) }}</span></td>
                            <td>{{ $u->created_at->format('M d, Y') }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="text-center py-4 text-muted">No customers found.</td></tr>
                        @endforelse
                    </tbody>
                </table>

                @elseif($type === 'revenue')
                <table class="table table-hover align-middle mb-0">
                    <thead style="background:#f8f9fc;"><tr>
                        <th class="ps-4">Ref</th><th>Room</th><th>Check-In</th><th>Nights</th><th>Amount</th><th>Date Paid</th>
                    </tr></thead>
                    <tbody>
                        @php $grandTotal = 0; @endphp
                        @forelse($data as $b)
                        @php $grandTotal += $b->total_amount; @endphp
                        <tr>
                            <td class="ps-4 fw-semibold" style="color:var(--primary);">{{ $b->booking_reference }}</td>
                            <td>Room {{ $b->room->room_number }}</td>
                            <td>{{ $b->check_in_date->format('M d, Y') }}</td>
                            <td>{{ $b->nights }}</td>
                            <td class="fw-bold" style="color:var(--secondary);">₱{{ number_format($b->total_amount,2) }}</td>
                            <td>{{ $b->updated_at->format('M d, Y') }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="text-center py-4 text-muted">No revenue data.</td></tr>
                        @endforelse
                        @if($data->count() > 0)
                        <tr style="background:#f8f9fc;font-weight:bold;">
                            <td colspan="4" class="ps-4 text-end">Grand Total:</td>
                            <td colspan="2" style="color:var(--secondary);">₱{{ number_format($grandTotal,2) }}</td>
                        </tr>
                        @endif
                    </tbody>
                </table>

                @elseif($type === 'occupancy')
                <table class="table table-hover align-middle mb-0">
                    <thead style="background:#f8f9fc;"><tr>
                        <th class="ps-4">Room</th><th>Type</th><th>Floor</th><th>Capacity</th><th>Price/Night</th><th>Total Bookings</th><th>Status</th>
                    </tr></thead>
                    <tbody>
                        @forelse($data as $room)
                        <tr>
                            <td class="ps-4 fw-bold" style="color:var(--primary);">{{ $room->room_number }}</td>
                            <td>{{ $room->room_type }}</td>
                            <td>{{ $room->floor }}</td>
                            <td>{{ $room->capacity }}</td>
                            <td>₱{{ number_format($room->price_per_night,2) }}</td>
                            <td>{{ $room->total_bookings }}</td>
                            <td><span class="badge bg-{{ $room->status==='available'?'success':($room->status==='occupied'?'danger':'warning text-dark') }}">{{ ucfirst($room->status) }}</span></td>
                        </tr>
                        @empty
                        <tr><td colspan="7" class="text-center py-4 text-muted">No rooms found.</td></tr>
                        @endforelse
                    </tbody>
                </table>

                @elseif($type === 'ratings')
                <table class="table table-hover align-middle mb-0">
                    <thead style="background:#f8f9fc;"><tr>
                        <th class="ps-4">Customer</th><th>Room</th><th>Rating</th><th>Comment</th><th>Date</th>
                    </tr></thead>
                    <tbody>
                        @forelse($data as $r)
                        <tr>
                            <td class="ps-4 fw-semibold">{{ $r->user->name }}</td>
                            <td>Room {{ $r->room->room_number }}</td>
                            <td>
                                @for($i=1;$i<=5;$i++)
                                <i class="bi bi-star{{ $i<=$r->rating?'-fill':'' }}" style="color:var(--secondary);"></i>
                                @endfor
                            </td>
                            <td>{{ $r->comment ?? '—' }}</td>
                            <td>{{ $r->created_at->format('M d, Y') }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-center py-4 text-muted">No ratings found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
