<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Grand Royal Hotel')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: #1a3c8f;
            --primary-dark: #122d6e;
            --secondary: #c8a84b;
            --secondary-dark: #a8882f;
        }
        * { box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; color: #1a1a2e; background: #fff; margin: 0; padding: 0; }
        .navbar-custom { background: var(--primary); padding: 1rem 0; box-shadow: 0 2px 10px rgba(0,0,0,.15); }
        .navbar-brand { font-family: 'Playfair Display', serif; font-size: 1.4rem; color: var(--secondary) !important; font-weight: 700; }
        .nav-link { color: rgba(255,255,255,.85) !important; font-weight: 500; transition: color .2s; }
        .nav-link:hover, .nav-link.active { color: var(--secondary) !important; }
        .btn-gold { background: var(--secondary); color: #fff; border: none; font-weight: 600; }
        .btn-gold:hover { background: var(--secondary-dark); color: #fff; }
        .btn-outline-gold { border: 2px solid var(--secondary); color: var(--secondary); background: transparent; font-weight: 600; }
        .btn-outline-gold:hover { background: var(--secondary); color: #fff; }
        .section-title { font-family: 'Playfair Display', serif; color: var(--primary); font-weight: 700; }
        footer { background: var(--primary-dark); color: rgba(255,255,255,.8); }
        footer a { color: rgba(255,255,255,.65); text-decoration: none; }
        footer a:hover { color: var(--secondary); }
    </style>
    @stack('styles')
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-custom sticky-top">
    <div class="container">
        <a class="navbar-brand" href="{{ route('home') }}">
            <i class="bi bi-building-fill me-2"></i>Grand Royal Hotel
        </a>
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navMain">
            <i class="bi bi-list text-white fs-4"></i>
        </button>
        <div class="collapse navbar-collapse" id="navMain">
            <ul class="navbar-nav mx-auto gap-1">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('about') ? 'active' : '' }}" href="{{ route('about') }}">About</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('rooms') ? 'active' : '' }}" href="{{ route('rooms') }}">Rooms</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('contact') ? 'active' : '' }}" href="{{ route('contact') }}">Contact</a>
                </li>
            </ul>
            <div class="d-flex gap-2 mt-2 mt-lg-0">
                @auth
                    @php
                       $dash = auth()->user()->role === 'admin' ? 'admin.dashboard'
                              : (auth()->user()->role === 'staff' ? 'staff.dashboard' : 'customer.dashboard');
                    @endphp
                    <a href="{{ route($dash) }}" class="btn btn-gold btn-sm px-3 rounded-pill">
                        <i class="bi bi-speedometer2 me-1"></i> Dashboard
                    </a>
                    <form method="POST" action="{{ route('logout') }}" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-outline-gold btn-sm px-3 rounded-pill">
                            <i class="bi bi-box-arrow-right me-1"></i> Logout
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="btn btn-outline-gold btn-sm px-3 rounded-pill">
                        <i class="bi bi-person me-1"></i> Login
                    </a>
                    <a href="{{ route('register') }}" class="btn btn-gold btn-sm px-3 rounded-pill">
                        <i class="bi bi-person-plus me-1"></i> Sign Up
                    </a>
                @endauth
            </div>
        </div>
    </div>
</nav>

@yield('content')

<footer class="pt-5 pb-3 mt-5">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-4">
                <h5 class="text-white fw-bold mb-3" style="font-family:'Playfair Display',serif;">
                    <i class="bi bi-building-fill me-2" style="color:var(--secondary);"></i>Grand Royal Hotel
                </h5>
                <p style="font-size:.9rem;">Experience luxury and comfort at its finest. Your home away from home.</p>
                <div class="d-flex gap-2 mt-3">
                    @foreach(['facebook','twitter','instagram','linkedin'] as $s)
                    <a href="#" class="btn btn-sm rounded-circle d-flex align-items-center justify-content-center"
                       style="width:36px;height:36px;background:rgba(255,255,255,.1);color:#fff;">
                        <i class="bi bi-{{ $s }}"></i>
                    </a>
                    @endforeach
                </div>
            </div>
            <div class="col-lg-2 col-6">
                <h6 class="text-white fw-semibold mb-3">Quick Links</h6>
                <ul class="list-unstyled" style="font-size:.9rem;">
                    <li class="mb-1"><a href="{{ route('home') }}">Home</a></li>
                    <li class="mb-1"><a href="{{ route('about') }}">About</a></li>
                    <li class="mb-1"><a href="{{ route('rooms') }}">Rooms</a></li>
                    <li class="mb-1"><a href="{{ route('contact') }}">Contact</a></li>
                </ul>
            </div>
            <div class="col-lg-3 col-6">
                <h6 class="text-white fw-semibold mb-3">Contact Us</h6>
                <ul class="list-unstyled" style="font-size:.9rem;">
                    <li class="mb-2"><i class="bi bi-geo-alt me-2" style="color:var(--secondary);"></i>Dingle Iloilo City</li>
                    <li class="mb-2"><i class="bi bi-telephone me-2" style="color:var(--secondary);"></i>+09111111111</li>
                    <li class="mb-2"><i class="bi bi-envelope me-2" style="color:var(--secondary);"></i>jannlenron@grandroyal.com</li>
                    <li><i class="bi bi-clock me-2" style="color:var(--secondary);"></i>Open 24/7</li>
                </ul>
            </div>
            <div class="col-lg-3">
                <h6 class="text-white fw-semibold mb-3">Newsletter</h6>
                <p style="font-size:.85rem;">Subscribe for exclusive offers and updates.</p>
                <div class="input-group input-group-sm">
                    <input type="email" class="form-control" placeholder="Your email">
                    <button class="btn btn-gold">Subscribe</button>
                </div>
            </div>
        </div>
        <hr class="border-secondary mt-4">
        <div class="text-center py-2">
            <small>&copy; {{ date('Y') }} Grand Royal Hotel. All rights reserved.</small>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
@if(session('success'))
    Swal.fire({ icon:'success', title:'Success!', text:@json(session('success')), timer:3000, showConfirmButton:false, toast:true, position:'top-end' });
@endif
@if(session('error'))
    Swal.fire({ icon:'error', title:'Error!', text:@json(session('error')), timer:4000, showConfirmButton:false, toast:true, position:'top-end' });
@endif
</script>
@stack('scripts')
</body>
</html>
