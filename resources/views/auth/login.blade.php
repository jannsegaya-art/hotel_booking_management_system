@extends('layouts.public')
@section('title', 'Login — Grand Royal Hotel')

@section('content')
<section style="min-height:85vh; display:flex; align-items:center;
    background: linear-gradient(135deg, rgba(18,45,110,0.96) 0%, rgba(26,60,143,0.88) 100%),
    url('https://images.unsplash.com/photo-1566073771259-6a8506099945?w=1600&q=80') center/cover no-repeat;">
    <div class="container py-5">
        <div class="row justify-content-center align-items-center g-5">

            {{-- Left side branding --}}
            <div class="col-lg-5 text-white text-center d-none d-lg-block">
                <i class="bi bi-building-fill" style="font-size:5rem; color:var(--secondary); opacity:.9;"></i>
                <h2 class="fw-bold mt-3 mb-2" style="font-family:'Playfair Display',serif; font-size:2.2rem;">Grand Royal Hotel</h2>
                <p class="opacity-75 mb-4">Sign in to access your dashboard and manage your bookings.</p>
                <div class="d-flex flex-column gap-2">
                    @foreach(['World-Class Hospitality','Luxury Rooms & Suites','24/7 Guest Support'] as $f)
                    <div class="d-flex align-items-center gap-2 justify-content-center">
                        <i class="bi bi-check-circle-fill" style="color:var(--secondary);"></i>
                        <span class="small opacity-85">{{ $f }}</span>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Login Form --}}
            <div class="col-sm-10 col-md-8 col-lg-5">
                <div class="card border-0 shadow-lg" style="border-radius:20px; overflow:hidden;">

                    {{-- Card Header --}}
                    <div class="text-center py-4 px-4" style="background:var(--primary);">
                        <div class="rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center"
                             style="width:64px; height:64px; background:var(--secondary);">
                            <i class="bi bi-person-fill text-white fs-3"></i>
                        </div>
                        <h4 class="text-white fw-bold mb-0" style="font-family:'Playfair Display',serif;">Welcome Back</h4>
                        <p class="text-white mb-0" style="opacity:.7; font-size:.9rem;">Sign in to your account</p>
                    </div>

                    {{-- Card Body --}}
                    <div class="card-body p-4 p-md-5 bg-white">

                        {{-- Success Message --}}
                        @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show py-2 small">
                            <i class="bi bi-check-circle me-1"></i> {{ session('success') }}
                            <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert"></button>
                        </div>
                        @endif

                        {{-- Errors --}}
                        @if($errors->any())
                        <div class="alert alert-danger py-2 small">
                            <i class="bi bi-exclamation-triangle me-1"></i>
                            @foreach($errors->all() as $error)<div>{{ $error }}</div>@endforeach
                        </div>
                        @endif

                        <form method="POST" action="{{ route('login.post') }}">
                            @csrf

                            {{-- Email --}}
                            <div class="mb-3">
                                <label class="form-label fw-semibold small" style="color:var(--primary);">Email Address</label>
                                <div class="input-group">
                                    <span class="input-group-text" style="background:#f8f9fc; border-right:none;">
                                        <i class="bi bi-envelope" style="color:var(--primary);"></i>
                                    </span>
                                    <input type="email" name="email"
                                           class="form-control @error('email') is-invalid @enderror"
                                           style="border-left:none; border-radius:0 8px 8px 0;"
                                           placeholder="you@email.com"
                                           value="{{ old('email') }}" required autofocus>
                                </div>
                            </div>

                            {{-- Password --}}
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <label class="form-label fw-semibold small mb-0" style="color:var(--primary);">Password</label>
                                    <a href="{{ route('password.request') }}" class="small" style="color:var(--secondary);">Forgot Password?</a>
                                </div>
                                <div class="input-group mt-1">
                                    <span class="input-group-text" style="background:#f8f9fc; border-right:none;">
                                        <i class="bi bi-lock" style="color:var(--primary);"></i>
                                    </span>
                                    <input type="password" name="password" id="passwordInput"
                                           class="form-control @error('password') is-invalid @enderror"
                                           style="border-left:none; border-right:none;"
                                           placeholder="Enter password" required>
                                    <button type="button" class="input-group-text" style="background:#f8f9fc; cursor:pointer;"
                                            onclick="togglePassword()">
                                        <i class="bi bi-eye" id="eyeIcon" style="color:var(--primary);"></i>
                                    </button>
                                </div>
                            </div>

                            {{-- Remember Me --}}
                            <div class="mb-4 d-flex align-items-center gap-2">
                                <input class="form-check-input mt-0" type="checkbox" name="remember" id="remember">
                                <label class="form-check-label small text-muted" for="remember">Keep me signed in</label>
                            </div>

                            {{-- Submit --}}
                            <button type="submit" class="btn w-100 py-3 fw-bold text-white mb-3"
                                    style="background:var(--primary); border-radius:10px; font-size:1rem;">
                                <i class="bi bi-box-arrow-in-right me-2"></i>Sign In
                            </button>

                            <p class="text-center text-muted small mb-0">
                                Don't have an account?
                                <a href="{{ route('register') }}" class="fw-semibold" style="color:var(--primary);">Create Account</a>
                            </p>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
@if(session('success'))
<script>
Swal.fire({ icon:'success', title:'Success!', text:@json(session('success')), timer:3000, showConfirmButton:false, toast:true, position:'top-end' });
</script>
@endif
<script>
function togglePassword() {
    const input = document.getElementById('passwordInput');
    const icon  = document.getElementById('eyeIcon');
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.replace('bi-eye', 'bi-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.replace('bi-eye-slash', 'bi-eye');
    }
}
</script>
@endpush
