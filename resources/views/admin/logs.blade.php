@extends('layouts.admin')
@section('title', 'Activity Logs')

@section('content')
<div class="container-fluid py-4">
    <div class="mb-4">
        <h2 class="fw-bold mb-0" style="color:var(--primary)"><i class="bi bi-clock-history me-2"></i>Activity Logs</h2>
        <p class="text-muted mb-0">Track all system activities and user actions</p>
    </div>

    <div class="card border-0 shadow-sm mb-4" style="border-radius:12px;">
        <div class="card-body p-3">
            <form method="GET" class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="form-label small fw-semibold">Action Type</label>
                    <select name="action" class="form-select form-select-sm">
                        <option value="">All Actions</option>
                        @foreach(['login','logout','register','booking_create','booking_update','booking_cancel','booking_delete','profile_update','password_change'] as $a)
                        <option value="{{ $a }}" {{ request('action')===$a?'selected':'' }}>{{ ucfirst(str_replace('_',' ',$a)) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-semibold">User</label>
                    <select name="user_id" class="form-select form-select-sm">
                        <option value="">All Users</option>
                        @foreach($users as $u)
                        <option value="{{ $u->id }}" {{ request('user_id')==$u->id?'selected':'' }}>{{ $u->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-semibold">From</label>
                    <input type="date" name="from" class="form-control form-control-sm" value="{{ request('from') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-semibold">To</label>
                    <input type="date" name="to" class="form-control form-control-sm" value="{{ request('to') }}">
                </div>
                <div class="col-md-2">
                    <button class="btn btn-sm w-100 text-white" style="background:var(--primary);">
                        <i class="bi bi-filter me-1"></i> Filter
                    </button>
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
                            <th class="ps-4 py-3">#</th>
                            <th>User</th>
                            <th>Action</th>
                            <th>Description</th>
                            <th>IP Address</th>
                            <th class="pe-4">Date & Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                        <tr>
                            <td class="ps-4 text-muted small">{{ $log->id }}</td>
                            <td>
                                @if($log->user)
                                <div class="fw-semibold">{{ $log->user->name }}</div>
                                <div class="small text-muted text-capitalize">{{ $log->user->role }}</div>
                                @else
                                <span class="text-muted">System</span>
                                @endif
                            </td>
                            <td>
                                @php
                                $actionColors = [
                                    'login'=>'success','logout'=>'secondary','register'=>'info',
                                    'booking_create'=>'primary','booking_update'=>'warning text-dark',
                                    'booking_cancel'=>'danger','booking_delete'=>'danger',
                                    'profile_update'=>'info','password_change'=>'warning text-dark',
                                ];
                                @endphp
                                <span class="badge bg-{{ $actionColors[$log->action] ?? 'secondary' }}">
                                    {{ ucfirst(str_replace('_',' ',$log->action)) }}
                                </span>
                            </td>
                            <td class="small">{{ $log->description }}</td>
                            <td class="small text-muted">{{ $log->ip_address ?? '—' }}</td>
                            <td class="pe-4 small text-muted">{{ $log->created_at->format('M d, Y h:i A') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="bi bi-clock-history" style="font-size:3rem;display:block;"></i>No activity logs found.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($logs->hasPages())
            <div class="d-flex justify-content-center py-3">{{ $logs->appends(request()->query())->links() }}</div>
            @endif
        </div>
    </div>
</div>
@endsection
