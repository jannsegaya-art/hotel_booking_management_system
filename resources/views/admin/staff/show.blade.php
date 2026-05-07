@extends('layouts.admin')
@section('title','Staff Profile - ' . $user->name)

@section('content')
<div class="container-fluid py-4">
    <div class="mb-4">
        <a href="{{ route('admin.staff.index') }}" class="btn btn-sm btn-outline-secondary mb-2">
            <i class="bi bi-arrow-left me-1"></i> Back to Staff
        </a>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm text-center p-4" style="border-radius:12px;">
                <img src="{{ $user->profile_photo_url }}"
                     class="rounded-circle mx-auto mb-3 d-block shadow"
                     style="width:90px; height:90px; object-fit:cover; border:3px solid var(--secondary);"
                     alt="{{ $user->name }}"
                     onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=1a3c8f&color=fff&size=128&bold=true'">
                     style="width:100px; height:100px; object-fit:cover;" alt="Profile">
                <h4 class="fw-bold mb-1" style="color:var(--primary);">{{ $user->name }}</h4>
                <span class="badge mb-2" style="background:var(--secondary); color:#fff;">Staff</span>
                <span class="badge bg-{{ $user->status === 'active' ? 'success' : ($user->status === 'pending' ? 'warning text-dark' : 'danger') }}">
                    {{ ucfirst($user->status) }}
                </span>
                <hr>
                <div class="text-muted small text-start">
                    <div class="mb-2"><i class="bi bi-envelope me-2"></i>{{ $user->email }}</div>
                    @if($user->phone)<div class="mb-2"><i class="bi bi-telephone me-2"></i>{{ $user->phone }}</div>@endif
                    @if($user->address)<div class="mb-2"><i class="bi bi-geo-alt me-2"></i>{{ $user->address }}</div>@endif
                    <div><i class="bi bi-calendar me-2"></i>Joined {{ $user->created_at->format('M d, Y') }}</div>
                </div>
                <div class="d-grid gap-2 mt-3">
                    <a href="{{ route('admin.staff.edit', $user) }}" class="btn text-white fw-semibold" style="background:var(--secondary);">
                        <i class="bi bi-pencil me-1"></i> Edit Profile
                    </a>
                    @if($user->status === 'pending')
                    <form method="POST" action="{{ route('admin.staff.approve', $user) }}">
                        @csrf
                        <button type="submit" class="btn btn-success w-100"><i class="bi bi-check-circle me-1"></i> Approve</button>
                    </form>
                    <form method="POST" action="{{ route('admin.staff.reject', $user) }}">
                        @csrf
                        <button type="submit" class="btn btn-danger w-100"><i class="bi bi-x-circle me-1"></i> Reject</button>
                    </form>
                    @else
                    <form method="POST" action="{{ route('admin.staff.toggle', $user) }}">
                        @csrf
                        <button type="submit" class="btn w-100 {{ $user->status === 'active' ? 'btn-warning' : 'btn-success' }}">
                            <i class="bi bi-{{ $user->status === 'active' ? 'pause' : 'play' }}-circle me-1"></i>
                            {{ $user->status === 'active' ? 'Deactivate' : 'Activate' }}
                        </button>
                    </form>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card border-0 shadow-sm" style="border-radius:12px;">
                <div class="card-header py-3" style="background:var(--primary); border-radius:12px 12px 0 0;">
                    <h5 class="text-white mb-0"><i class="bi bi-calendar-check me-2"></i>Assigned Bookings ({{ $bookings->count() }})</h5>
                </div>
                <div class="card-body p-0">
                    @if($bookings->isEmpty())
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-calendar-x" style="font-size:2.5rem; display:block;"></i>
                        No bookings assigned yet.
                    </div>
                    @else
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead style="background:#f8f9fc;">
                                <tr>
                                    <th class="ps-4">Ref #</th>
                                    <th>Guest</th>
                                    <th>Room</th>
                                    <th>Check-In</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($bookings as $b)
                                <tr>
                                    <td class="ps-4 fw-semibold" style="color:var(--primary);">{{ $b->booking_reference }}</td>
                                    <td>{{ $b->user->name }}</td>
                                    <td>Room {{ $b->room->room_number }}</td>
                                    <td>{{ $b->check_in_date->format('M d, Y') }}</td>
                                    <td>
                                        @php $sc=['pending'=>'warning text-dark','confirmed'=>'primary','checked_in'=>'success','checked_out'=>'secondary','cancelled'=>'danger']; @endphp
                                        <span class="badge bg-{{ $sc[$b->status] ?? 'secondary' }}">{{ ucfirst(str_replace('_',' ',$b->status)) }}</span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
