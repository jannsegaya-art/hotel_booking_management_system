<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title','My Account') — Grand Royal Hotel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <style>
        :root{--primary:#1a3c8f;--primary-dark:#122d6e;--secondary:#c8a84b;--sidebar-w:245px;--topbar-h:62px;}
        *{box-sizing:border-box;}
        body{font-family:'Inter',sans-serif;background:#f0f2f8;margin:0;padding:0;}
        .sidebar{position:fixed;top:0;left:0;width:var(--sidebar-w);height:100vh;background:linear-gradient(180deg,var(--primary-dark),var(--primary));z-index:1000;overflow-y:auto;transition:transform .3s;}
        .sidebar-brand{padding:1.2rem 1.5rem;border-bottom:1px solid rgba(255,255,255,.1);color:var(--secondary);font-size:1.1rem;font-weight:700;display:flex;align-items:center;gap:.6rem;}
        .sidebar-label{font-size:.68rem;font-weight:700;text-transform:uppercase;letter-spacing:1.5px;color:rgba(255,255,255,.35);padding:.9rem 1.5rem .3rem;}
        .sidebar-link{display:flex;align-items:center;gap:.75rem;padding:.65rem 1.5rem;color:rgba(255,255,255,.75);text-decoration:none;font-size:.87rem;font-weight:500;transition:all .2s;border-left:3px solid transparent;}
        .sidebar-link:hover,.sidebar-link.active{color:#fff;background:rgba(255,255,255,.08);border-left-color:var(--secondary);}
        .sidebar-link i{font-size:1rem;width:18px;text-align:center;}
        .topbar{position:fixed;top:0;left:var(--sidebar-w);width:calc(100% - var(--sidebar-w));height:var(--topbar-h);background:#fff;z-index:999;box-shadow:0 2px 10px rgba(0,0,0,.06);display:flex;align-items:center;padding:0 1.5rem;justify-content:space-between;}
        .main-content{margin-left:var(--sidebar-w);margin-top:var(--topbar-h);padding:1.5rem;}
        .card{border:none!important;border-radius:12px!important;box-shadow:0 2px 12px rgba(0,0,0,.06);}
        .card-header{border-radius:12px 12px 0 0!important;}
        .form-control:focus,.form-select:focus{border-color:var(--primary);box-shadow:0 0 0 .2rem rgba(26,60,143,.15);}
        .page-link{color:var(--primary);}
        .page-item.active .page-link{background:var(--primary);border-color:var(--primary);}
        @media(max-width:991px){.sidebar{transform:translateX(-100%);}.sidebar.show{transform:translateX(0);}.topbar{left:0;width:100%;}.main-content{margin-left:0;}}
    </style>
    @stack('styles')
</head>
<body>
@php $unread = \App\Models\Notification::where('user_id',auth()->id())->where('is_read',false)->count(); @endphp
<aside class="sidebar" id="sidebar">
    <div class="sidebar-brand"><i class="bi bi-building-fill"></i> Grand Royal</div>
    <nav class="pb-4">
        <div class="sidebar-label">My Account</div>
        <a href="{{ route('customer.dashboard') }}" class="sidebar-link {{ request()->routeIs('customer.dashboard') ? 'active' : '' }}">
            <i class="bi bi-speedometer2"></i> Dashboard
        </a>
        <a href="{{ route('customer.bookings.index') }}" class="sidebar-link {{ request()->routeIs('customer.bookings.index') || request()->routeIs('customer.bookings.show') ? 'active' : '' }}">
            <i class="bi bi-calendar-check"></i> My Bookings
        </a>
        <a href="{{ route('customer.bookings.create') }}" class="sidebar-link {{ request()->routeIs('customer.bookings.create') ? 'active' : '' }}">
            <i class="bi bi-calendar-plus"></i> Book a Room
        </a>
        <a href="{{ route('customer.notifications.index') }}" class="sidebar-link {{ request()->routeIs('customer.notifications.*') ? 'active' : '' }}">
            <i class="bi bi-bell"></i> Notifications
            @if($unread > 0)
            <span class="badge bg-danger rounded-pill ms-auto" style="font-size:.65rem;">{{ $unread }}</span>
            @endif
        </a>
        <div class="sidebar-label">Browse</div>
        <a href="{{ route('rooms') }}" class="sidebar-link {{ request()->routeIs('rooms') ? 'active' : '' }}">
            <i class="bi bi-building"></i> View All Rooms
        </a>
        <div class="sidebar-label">Account</div>
        <a href="{{ route('profile') }}" class="sidebar-link {{ request()->routeIs('profile') ? 'active' : '' }}">
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
<div class="topbar">
    <div class="d-flex align-items-center gap-3">
        <button class="btn btn-sm btn-outline-secondary d-lg-none" onclick="document.getElementById('sidebar').classList.toggle('show')">
            <i class="bi bi-list fs-5"></i>
        </button>
        <span class="fw-semibold d-none d-md-block" style="color:var(--primary);">Customer Portal</span>
    </div>
    <div class="d-flex align-items-center gap-3">
        <a href="{{ route('customer.notifications.index') }}" class="position-relative text-decoration-none text-muted">
            <i class="bi bi-bell fs-5"></i>
            @if($unread > 0)
            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size:.6rem;">{{ $unread }}</span>
            @endif
        </a>
        <div class="d-flex align-items-center gap-2">
            <img src="{{ auth()->user()->profile_photo_url }}" class="rounded-circle border"
                 style="width:36px;height:36px;object-fit:cover;" alt="Avatar">
            <div class="d-none d-md-block lh-1">
                <div class="fw-semibold small">{{ auth()->user()->name }}</div>
                <div style="font-size:.7rem;color:var(--secondary);">Customer</div>
            </div>
        </div>
    </div>
</div>
<main class="main-content">@yield('content')</main>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
@if(session('success'))
    Swal.fire({icon:'success',title:'Success!',text:@json(session('success')),timer:3000,showConfirmButton:false,toast:true,position:'top-end'});
@endif
@if(session('error'))
    Swal.fire({icon:'error',title:'Error!',text:@json(session('error')),timer:4000,showConfirmButton:false,toast:true,position:'top-end'});
@endif
</script>
@stack('scripts')
</body>
</html>
