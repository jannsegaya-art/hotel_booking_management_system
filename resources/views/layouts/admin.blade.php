<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title','Admin') — Grand Royal Hotel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: #1a3c8f;
            --primary-dark: #122d6e;
            --secondary: #c8a84b;
            --secondary-dark: #a8882f;
            --sidebar-w: 250px;
            --topbar-h: 62px;
            --light-bg: #f0f2f8;
        }
        * { box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: var(--light-bg); margin: 0; padding: 0; }

        /* ── Sidebar ── */
        .sidebar {
            position: fixed; top: 0; left: 0; width: var(--sidebar-w);
            height: 100vh; background: linear-gradient(180deg, var(--primary-dark) 0%, var(--primary) 100%);
            z-index: 1000; overflow-y: auto; transition: transform .3s;
        }
        .sidebar-brand {
            padding: 1.2rem 1.5rem; border-bottom: 1px solid rgba(255,255,255,.1);
            color: var(--secondary); font-size: 1.15rem; font-weight: 700;
            display: flex; align-items: center; gap: .6rem;
            font-family: 'Playfair Display', serif;
        }
        .sidebar-label {
            font-size: .68rem; font-weight: 700; text-transform: uppercase;
            letter-spacing: 1.5px; color: rgba(255,255,255,.35);
            padding: .9rem 1.5rem .3rem;
        }
        .sidebar-link {
            display: flex; align-items: center; gap: .75rem;
            padding: .65rem 1.5rem; color: rgba(255,255,255,.75);
            text-decoration: none; font-size: .875rem; font-weight: 500;
            transition: all .2s; border-left: 3px solid transparent;
        }
        .sidebar-link:hover, .sidebar-link.active {
            color: #fff; background: rgba(255,255,255,.1); border-left-color: var(--secondary);
        }
        .sidebar-link i { font-size: 1rem; width: 18px; text-align: center; }

        /* ── Topbar ── */
        .topbar {
            position: fixed; top: 0; left: var(--sidebar-w);
            width: calc(100% - var(--sidebar-w)); height: var(--topbar-h);
            background: #fff; z-index: 999; box-shadow: 0 2px 10px rgba(0,0,0,.06);
            display: flex; align-items: center; padding: 0 1.5rem; justify-content: space-between;
        }
        .main-content {
            margin-left: var(--sidebar-w); margin-top: var(--topbar-h);
            padding: 1.5rem; min-height: calc(100vh - var(--topbar-h));
        }

        /* ── Cards ── */
        .card { border: none !important; border-radius: 12px !important; box-shadow: 0 2px 12px rgba(0,0,0,.06); }
        .card-header { border-radius: 12px 12px 0 0 !important; border-bottom: none !important; }
        .card-header-primary { background: var(--primary) !important; color: #fff; }
        .card-header-gold    { background: var(--secondary) !important; color: #fff; }

        /* ── Stat cards ── */
        .stat-card { transition: transform .2s, box-shadow .2s; cursor: pointer; border-radius: 12px !important; }
        .stat-card:hover { transform: translateY(-4px); box-shadow: 0 8px 24px rgba(0,0,0,.12) !important; }
        .stat-icon {
            width: 46px; height: 46px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.2rem;
        }

        /* ── Typography helpers ── */
        .fw-600 { font-weight: 600 !important; }
        .fw-700 { font-weight: 700 !important; }

        /* ── Tables ── */
        .table thead th {
            font-size: .82rem; font-weight: 600; padding: .75rem 1rem;
            border: none; white-space: nowrap; background: #f8f9fc;
        }
        .table tbody td { font-size: .875rem; vertical-align: middle; padding: .65rem 1rem; }

        /* ── Status badges ── */
        .badge-pending     { background: #fff3cd !important; color: #856404 !important; }
        .badge-confirmed   { background: #cff4fc !important; color: #055160 !important; }
        .badge-checked_in  { background: #d1e7dd !important; color: #0a3622 !important; }
        .badge-checked_out { background: #e2e3e5 !important; color: #383d41 !important; }
        .badge-cancelled   { background: #f8d7da !important; color: #842029 !important; }

        /* ── Forms ── */
        .form-control:focus, .form-select:focus {
            border-color: var(--primary); box-shadow: 0 0 0 .2rem rgba(26,60,143,.15);
        }

        /* ── Pagination ── */
        .page-link { color: var(--primary); }
        .page-item.active .page-link { background: var(--primary); border-color: var(--primary); }

        /* ── Button helpers ── */
        .btn-primary-hotel { background: var(--primary); border-color: var(--primary); color: #fff; }
        .btn-primary-hotel:hover { background: var(--primary-dark); color: #fff; }
        .btn-gold { background: var(--secondary); border-color: var(--secondary); color: #fff; font-weight: 600; }
        .btn-gold:hover { background: var(--secondary-dark); color: #fff; }

        /* ── Responsive ── */
        @media (max-width: 991px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.show { transform: translateX(0); }
            .topbar { left: 0; width: 100%; }
            .main-content { margin-left: 0; }
        }
    </style>
    @stack('styles')
</head>
<body>

{{-- SIDEBAR --}}
<aside class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <i class="bi bi-building-fill"></i>Grand Royal
    </div>
    <nav class="pb-4">
        <div class="sidebar-label">Main</div>
        <a href="{{ route('admin.dashboard') }}"
           class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <i class="bi bi-speedometer2"></i> Dashboard
        </a>

        <div class="sidebar-label">Management</div>
        <a href="{{ route('admin.staff.index') }}"
           class="sidebar-link {{ request()->routeIs('admin.staff.*') ? 'active' : '' }}">
            <i class="bi bi-person-badge"></i> Staff
            @php $pendingStaff = \App\Models\User::where('role','staff')->where('status','pending')->count(); @endphp
            @if($pendingStaff > 0)
            <span class="badge bg-danger rounded-pill ms-auto" style="font-size:.65rem;">{{ $pendingStaff }}</span>
            @endif
        </a>
        <a href="{{ route('admin.customers.index') }}"
           class="sidebar-link {{ request()->routeIs('admin.customers.*') ? 'active' : '' }}">
            <i class="bi bi-people"></i> Customers
        </a>
        <a href="{{ route('admin.rooms.index') }}"
           class="sidebar-link {{ request()->routeIs('admin.rooms.*') ? 'active' : '' }}">
            <i class="bi bi-building"></i> Rooms
        </a>
        <a href="{{ route('admin.bookings.index') }}"
           class="sidebar-link {{ request()->routeIs('admin.bookings.*') ? 'active' : '' }}">
            <i class="bi bi-calendar-check"></i> Bookings
            @php $pendingBookings = \App\Models\Booking::where('status','pending')->count(); @endphp
            @if($pendingBookings > 0)
            <span class="badge bg-warning text-dark rounded-pill ms-auto" style="font-size:.65rem;">{{ $pendingBookings }}</span>
            @endif
        </a>

        <div class="sidebar-label">Finance & Reports</div>
        <a href="{{ route('admin.revenue') }}"
           class="sidebar-link {{ request()->routeIs('admin.revenue') ? 'active' : '' }}">
            <i class="bi bi-graph-up-arrow"></i> Revenue
        </a>
        <a href="{{ route('admin.reports') }}"
           class="sidebar-link {{ request()->routeIs('admin.reports') ? 'active' : '' }}">
            <i class="bi bi-file-earmark-bar-graph"></i> Reports
        </a>
        <a href="{{ route('admin.ratings') }}"
           class="sidebar-link {{ request()->routeIs('admin.ratings') ? 'active' : '' }}">
            <i class="bi bi-star"></i> Ratings
        </a>
        <a href="{{ route('admin.logs') }}"
           class="sidebar-link {{ request()->routeIs('admin.logs') ? 'active' : '' }}">
            <i class="bi bi-journal-text"></i> Activity Logs
        </a>

        <div class="sidebar-label">System</div>
        <a href="{{ route('admin.notifications.index') }}"
           class="sidebar-link {{ request()->routeIs('admin.notifications.*') ? 'active' : '' }}">
            <i class="bi bi-bell"></i> Notifications
            @php $unreadNotif = \App\Models\Notification::where('user_id',auth()->id())->where('is_read',false)->count(); @endphp
            @if($unreadNotif > 0)
            <span class="badge bg-danger rounded-pill ms-auto" style="font-size:.65rem;">{{ $unreadNotif }}</span>
            @endif
        </a>
        <a href="{{ route('admin.bookings.availability') }}"
           class="sidebar-link {{ request()->routeIs('admin.bookings.availability') ? 'active' : '' }}">
            <i class="bi bi-search"></i> Availability
        </a>

        <div class="sidebar-label">Account</div>
        <a href="{{ route('profile') }}"
           class="sidebar-link {{ request()->routeIs('profile') ? 'active' : '' }}">
            <i class="bi bi-person-circle"></i> My Profile
        </a>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="sidebar-link w-100 border-0 bg-transparent text-start" style="cursor:pointer;">
                <i class="bi bi-box-arrow-right"></i> Logout
            </button>
        </form>
    </nav>
</aside>

{{-- TOPBAR --}}
<div class="topbar">
    <div class="d-flex align-items-center gap-3">
        <button class="btn btn-sm btn-outline-secondary d-lg-none"
                onclick="document.getElementById('sidebar').classList.toggle('show')">
            <i class="bi bi-list fs-5"></i>
        </button>
        <div class="d-none d-md-block">
            <span class="fw-600" style="color:var(--primary);">Admin Panel</span>
            <span class="text-muted small ms-2">Grand Royal Hotel</span>
        </div>
    </div>
    <div class="d-flex align-items-center gap-3">
        {{-- Notification Bell --}}
        <a href="{{ route('admin.notifications.index') }}" class="position-relative text-decoration-none text-muted">
            <i class="bi bi-bell fs-5"></i>
            @if(isset($unreadNotif) && $unreadNotif > 0)
            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size:.6rem;">
                {{ $unreadNotif }}
            </span>
            @endif
        </a>
        {{-- User Avatar & Name --}}
        <div class="d-flex align-items-center gap-2">
            <img src="{{ auth()->user()->profile_photo_url }}"
                 class="rounded-circle shadow-sm"
                 style="width:38px; height:38px; object-fit:cover; border:2px solid var(--secondary);"
                 alt="Avatar"
                 onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=1a3c8f&color=fff&size=64'">
            <div class="d-none d-md-block lh-1">
                <div class="fw-600 small">{{ auth()->user()->name }}</div>
                <div style="font-size:.7rem; color:var(--secondary);">Administrator</div>
            </div>
        </div>
    </div>
</div>

{{-- MAIN CONTENT --}}
<main class="main-content">
    @yield('content')
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
// Flash messages
@if(session('success'))
    Swal.fire({
        icon: 'success', title: 'Success!',
        text: @json(session('success')),
        timer: 3000, showConfirmButton: false, toast: true, position: 'top-end'
    });
@endif
@if(session('error'))
    Swal.fire({
        icon: 'error', title: 'Error!',
        text: @json(session('error')),
        timer: 4000, showConfirmButton: false, toast: true, position: 'top-end'
    });
@endif

// SweetAlert confirm for delete buttons
document.querySelectorAll('.btn-confirm-action').forEach(function(btn) {
    btn.addEventListener('click', function(e) {
        e.preventDefault();
        var form    = this.closest('form');
        var title   = this.dataset.title   || 'Confirm Action';
        var message = this.dataset.message || 'Are you sure?';
        Swal.fire({
            title: title, text: message, icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, proceed!'
        }).then(function(result) {
            if (result.isConfirmed) form.submit();
        });
    });
});
</script>

@stack('scripts')
</body>
</html>
