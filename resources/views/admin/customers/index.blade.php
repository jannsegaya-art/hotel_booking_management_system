@extends('layouts.admin')
@section('title', 'Customer Management')

@section('content')
<div class="container-fluid py-4">

    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h4 class="fw-bold mb-0" style="color:var(--primary);">
                <i class="bi bi-people me-2"></i>Customer Management
            </h4>
            <p class="text-muted small mb-0">View and manage registered customers</p>
        </div>
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
                    </select>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-sm w-100 text-white" style="background:var(--primary);">
                        <i class="bi bi-funnel me-1"></i> Filter
                    </button>
                </div>
                <div class="col-md-2">
                    <a href="{{ route('admin.customers.index') }}" class="btn btn-sm btn-outline-secondary w-100">Clear</a>
                </div>
            </form>
        </div>
    </div>

    {{-- Customer Table --}}
    <div class="card border-0 shadow-sm" style="border-radius:12px;">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead style="background:#f8f9fc;">
                        <tr>
                            <th class="ps-4 py-3">Customer</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Total Bookings</th>
                            <th>Status</th>
                            <th>Joined</th>
                            <th class="pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($customers as $customer)
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center gap-2">
                                    {{-- Real profile photo using profile_photo_url accessor --}}
                                    <img src="{{ $customer->profile_photo_url }}"
                                         alt="{{ $customer->name }}"
                                         class="rounded-circle flex-shrink-0"
                                         width="38" height="38"
                                         style="object-fit:cover; border:2px solid var(--secondary);"
                                         onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($customer->name) }}&background=1a3c8f&color=fff&size=64&bold=true'">
                                    <div>
                                        <div class="fw-semibold small">{{ $customer->name }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="small">{{ $customer->email }}</td>
                            <td class="small">{{ $customer->phone ?? '—' }}</td>
                            <td>
                                <span class="badge rounded-pill" style="background:var(--primary); color:#fff;">
                                    {{ $customer->bookings_count }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-{{ $customer->status === 'active' ? 'success' : 'danger' }} px-2 py-1">
                                    {{ ucfirst($customer->status) }}
                                </span>
                            </td>
                            <td class="small text-muted">{{ $customer->created_at->format('M d, Y') }}</td>
                            <td class="pe-4">
                                <div class="d-flex gap-1">
                                    {{-- View --}}
                                    <a href="{{ route('admin.customers.show', $customer) }}"
                                       class="btn btn-sm btn-outline-primary" title="View">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    {{-- Activate / Deactivate --}}
                                    <form method="POST" action="{{ route('admin.customers.toggle', $customer) }}">
                                        @csrf
                                        <button type="submit"
                                                class="btn btn-sm btn-outline-{{ $customer->status === 'active' ? 'warning' : 'success' }}"
                                                title="{{ $customer->status === 'active' ? 'Deactivate' : 'Activate' }}">
                                            <i class="bi bi-{{ $customer->status === 'active' ? 'pause-circle' : 'play-circle' }}"></i>
                                        </button>
                                    </form>
                                    {{-- Delete --}}
                                    <button class="btn btn-sm btn-outline-danger del-btn"
                                            data-id="{{ $customer->id }}"
                                            data-name="{{ $customer->name }}"
                                            title="Delete">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                    <form id="delCust-{{ $customer->id }}" method="POST"
                                          action="{{ route('admin.customers.destroy', $customer) }}" class="d-none">
                                        @csrf @method('DELETE')
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="bi bi-people" style="font-size:3rem; display:block; margin-bottom:8px;"></i>
                                No customers found.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($customers->hasPages())
            <div class="d-flex justify-content-center py-3">
                {{ $customers->appends(request()->query())->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.querySelectorAll('.del-btn').forEach(function(btn) {
    btn.addEventListener('click', function() {
        var id   = this.dataset.id;
        var name = this.dataset.name;
        Swal.fire({
            title: 'Delete Customer?',
            html: 'Delete <strong>' + name + '</strong>?<br><small class="text-muted">All their bookings will also be removed.</small>',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, Delete',
            cancelButtonText: 'Cancel'
        }).then(function(result) {
            if (result.isConfirmed) {
                document.getElementById('delCust-' + id).submit();
            }
        });
    });
});
</script>
@endpush
