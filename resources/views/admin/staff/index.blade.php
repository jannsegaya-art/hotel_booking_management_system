@extends('layouts.admin')
@section('title', 'Staff Management')

@section('content')
<div class="container-fluid py-4">

    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h4 class="fw-bold mb-0" style="color:var(--primary);">
                <i class="bi bi-person-badge me-2"></i>Staff Management
            </h4>
            <p class="text-muted small mb-0">Manage hotel staff accounts</p>
        </div>
        <a href="{{ route('admin.staff.create') }}" class="btn text-white fw-semibold"
           style="background:var(--primary); border-radius:8px;">
            <i class="bi bi-person-plus me-1"></i> Add Staff
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
    <div class="card border-0 shadow-sm mb-4" style="border-radius:12px;">
        <div class="card-body p-3">
            <form method="GET" class="row g-2 align-items-end">
                <div class="col-md-5">
                    <input type="text" name="search" class="form-control form-control-sm"
                           placeholder="Search by name or email..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-select form-select-sm">
                        <option value="">All Status</option>
                        <option value="active"   {{ request('status')==='active'   ? 'selected':'' }}>Active</option>
                        <option value="inactive" {{ request('status')==='inactive' ? 'selected':'' }}>Inactive</option>
                        <option value="pending"  {{ request('status')==='pending'  ? 'selected':'' }}>Pending</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-sm w-100 text-white" style="background:var(--primary);">
                        <i class="bi bi-funnel me-1"></i> Filter
                    </button>
                </div>
                <div class="col-md-2">
                    <a href="{{ route('admin.staff.index') }}" class="btn btn-sm btn-outline-secondary w-100">Clear</a>
                </div>
            </form>
        </div>
    </div>

    {{-- Staff Table --}}
    <div class="card border-0 shadow-sm" style="border-radius:12px;">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead style="background:#f8f9fc;">
                        <tr>
                            <th class="ps-4 py-3">#</th>
                            <th>Staff Member</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Bookings</th>
                            <th>Status</th>
                            <th>Joined</th>
                            <th class="pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($staff as $s)
                        <tr>
                            <td class="ps-4 text-muted small">{{ $loop->iteration }}</td>

                            {{-- Staff Name + Photo --}}
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    {{-- Use profile_photo_url accessor — shows real photo or generated avatar --}}
                                    <img src="{{ $s->profile_photo_url }}"
                                         alt="{{ $s->name }}"
                                         class="rounded-circle flex-shrink-0"
                                         width="38" height="38"
                                         style="object-fit:cover; border:2px solid var(--secondary);"
                                         onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($s->name) }}&background=1a3c8f&color=fff&size=64&bold=true'">
                                    <div>
                                        <div class="fw-semibold small">{{ $s->name }}</div>
                                    </div>
                                </div>
                            </td>

                            <td class="small">{{ $s->email }}</td>
                            <td class="small">{{ $s->phone ?? '—' }}</td>
                            <td>
                                <span class="badge rounded-pill" style="background:var(--primary); color:#fff;">
                                    {{ $s->assignedBookings()->count() }}
                                </span>
                            </td>
                            <td>
                                @php
                                    $statusBg = [
                                        'active'   => 'bg-success',
                                        'inactive' => 'bg-danger',
                                        'pending'  => 'bg-warning text-dark',
                                    ];
                                @endphp
                                <span class="badge {{ $statusBg[$s->status] ?? 'bg-secondary' }} px-2 py-1">
                                    {{ ucfirst($s->status) }}
                                </span>
                            </td>
                            <td class="small text-muted">{{ $s->created_at->format('M d, Y') }}</td>
                            <td class="pe-4">
                                <div class="d-flex gap-1 flex-wrap">
                                    {{-- View --}}
                                    <a href="{{ route('admin.staff.show', $s) }}"
                                       class="btn btn-sm btn-outline-primary" title="View">
                                        <i class="bi bi-eye"></i>
                                    </a>

                                    {{-- Approve / Activate / Deactivate --}}
                                    @if($s->status === 'pending')
                                        <form method="POST" action="{{ route('admin.staff.approve', $s) }}">
                                            @csrf
                                            <button class="btn btn-sm btn-success btn-confirm-action"
                                                    data-title="Approve Staff"
                                                    data-message="Approve this staff account? They will be able to log in.">
                                                <i class="bi bi-check-circle me-1"></i>Approve
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ route('admin.staff.reject', $s) }}">
                                            @csrf
                                            <button class="btn btn-sm btn-outline-danger btn-confirm-action"
                                                    data-title="Reject Staff"
                                                    data-message="Reject this staff registration?">
                                                <i class="bi bi-x-circle"></i>
                                            </button>
                                        </form>
                                    @else
                                        <form method="POST" action="{{ route('admin.staff.toggle', $s) }}">
                                            @csrf
                                            <button class="btn btn-sm btn-outline-{{ $s->status === 'active' ? 'warning' : 'success' }} btn-confirm-action"
                                                    data-title="{{ $s->status === 'active' ? 'Deactivate' : 'Activate' }} Staff"
                                                    data-message="Are you sure you want to {{ $s->status === 'active' ? 'deactivate' : 'activate' }} {{ $s->name }}?"
                                                    title="{{ $s->status === 'active' ? 'Deactivate' : 'Activate' }}">
                                                <i class="bi bi-{{ $s->status === 'active' ? 'pause-circle' : 'play-circle' }}"></i>
                                            </button>
                                        </form>
                                    @endif

                                    {{-- Edit --}}
                                    <a href="{{ route('admin.staff.edit', $s) }}"
                                       class="btn btn-sm btn-outline-secondary" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>

                                    {{-- Delete --}}
                                    <button class="btn btn-sm btn-outline-danger del-staff-btn"
                                            data-id="{{ $s->id }}"
                                            data-name="{{ $s->name }}"
                                            title="Delete">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                    <form id="delStaff-{{ $s->id }}" method="POST"
                                          action="{{ route('admin.staff.destroy', $s) }}" class="d-none">
                                        @csrf @method('DELETE')
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="bi bi-people" style="font-size:3rem; display:block; margin-bottom:8px;"></i>
                                No staff members found.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($staff->hasPages())
            <div class="d-flex justify-content-center py-3">
                {{ $staff->appends(request()->query())->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Delete staff confirmation
document.querySelectorAll('.del-staff-btn').forEach(function(btn) {
    btn.addEventListener('click', function() {
        var id   = this.dataset.id;
        var name = this.dataset.name;
        Swal.fire({
            title: 'Delete Staff?',
            html: 'Delete <strong>' + name + '</strong>?<br><small class="text-muted">Assigned bookings will be unassigned.</small>',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, Delete',
            cancelButtonText: 'Cancel'
        }).then(function(result) {
            if (result.isConfirmed) {
                document.getElementById('delStaff-' + id).submit();
            }
        });
    });
});

// Generic confirm for approve/deactivate/activate buttons
document.querySelectorAll('.btn-confirm-action').forEach(function(btn) {
    btn.addEventListener('click', function(e) {
        e.preventDefault();
        var form    = this.closest('form');
        var title   = this.dataset.title   || 'Confirm';
        var message = this.dataset.message || 'Are you sure?';
        Swal.fire({
            title: title,
            text: message,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#1a3c8f',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, proceed!'
        }).then(function(result) {
            if (result.isConfirmed) form.submit();
        });
    });
});
</script>
@endpush
