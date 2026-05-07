@extends('layouts.public')
@section('title', 'Sign Up — Grand Royal Hotel')

@section('content')
<section style="min-height:85vh; display:flex; align-items:center;
    background: linear-gradient(135deg, rgba(18,45,110,0.96) 0%, rgba(26,60,143,0.88) 100%),
    url('https://images.unsplash.com/photo-1582719508461-905c673771fd?w=1600&q=80') center/cover no-repeat;">
    <div class="container py-5">
        <div class="row justify-content-center align-items-center g-5">

            {{-- Left Branding --}}
            <div class="col-lg-5 text-white text-center d-none d-lg-block">
                <i class="bi bi-building-fill" style="font-size:5rem; color:var(--secondary); opacity:.9;"></i>
                <h2 class="fw-bold mt-3 mb-2" style="font-family:'Playfair Display',serif; font-size:2.2rem;">Join Grand Royal</h2>
                <p class="opacity-75 mb-4">Create your account to start booking luxury rooms and enjoying exclusive benefits.</p>
                <div class="d-flex flex-column gap-2">
                    @foreach(['Book Rooms Instantly','View Booking History','Exclusive Member Offers','24/7 Customer Support'] as $f)
                    <div class="d-flex align-items-center gap-2 justify-content-center">
                        <i class="bi bi-check-circle-fill" style="color:var(--secondary);"></i>
                        <span class="small opacity-85">{{ $f }}</span>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Register Form --}}
            <div class="col-sm-10 col-md-8 col-lg-5">
                <div class="card border-0 shadow-lg" style="border-radius:20px; overflow:hidden;">

                    {{-- Header --}}
                    <div class="text-center py-4 px-4" style="background:var(--primary);">
                        <div class="rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center"
                             style="width:64px; height:64px; background:var(--secondary);">
                            <i class="bi bi-person-plus-fill text-white fs-3"></i>
                        </div>
                        <h4 class="text-white fw-bold mb-0" style="font-family:'Playfair Display',serif;">Create Account</h4>
                        <p class="text-white mb-0" style="opacity:.7; font-size:.9rem;">Join Grand Royal Hotel today</p>
                    </div>

                    {{-- Body --}}
                    <div class="card-body p-4 p-md-5 bg-white">

                        @if($errors->any())
                        <div class="alert alert-danger py-2 small alert-dismissible fade show">
                            <i class="bi bi-exclamation-triangle me-1"></i>
                            @foreach($errors->all() as $error)<div>{{ $error }}</div>@endforeach
                            <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert"></button>
                        </div>
                        @endif

                        <form method="POST" action="{{ route('register.post') }}">
                            @csrf

                            {{-- Role Selection --}}
                            <div class="mb-4">
                                <label class="form-label fw-semibold small" style="color:var(--primary);">Register As</label>
                                <div class="row g-2">
                                    <div class="col-6">
                                        <input type="radio" name="role" id="roleCustomer" value="customer" class="d-none"
                                               {{ old('role','customer') === 'customer' ? 'checked' : '' }}>
                                        <label for="roleCustomer" class="w-100 text-center p-3 rounded-3 border-2 fw-semibold small"
                                               style="cursor:pointer; border:2px solid {{ old('role','customer') === 'customer' ? 'var(--primary)' : '#dee2e6' }};
                                                      background:{{ old('role','customer') === 'customer' ? '#f0f4ff' : '#fff' }};
                                                      color:{{ old('role','customer') === 'customer' ? 'var(--primary)' : '#6c757d' }};"
                                               id="labelCustomer">
                                            <i class="bi bi-person-circle d-block fs-3 mb-1"></i>Customer
                                        </label>
                                    </div>
                                    <div class="col-6">
                                        <input type="radio" name="role" id="roleStaff" value="staff" class="d-none"
                                               {{ old('role') === 'staff' ? 'checked' : '' }}>
                                        <label for="roleStaff" class="w-100 text-center p-3 rounded-3 border-2 fw-semibold small"
                                               style="cursor:pointer; border:2px solid {{ old('role') === 'staff' ? 'var(--primary)' : '#dee2e6' }};
                                                      background:{{ old('role') === 'staff' ? '#f0f4ff' : '#fff' }};
                                                      color:{{ old('role') === 'staff' ? 'var(--primary)' : '#6c757d' }};"
                                               id="labelStaff">
                                            <i class="bi bi-person-badge d-block fs-3 mb-1"></i>Staff
                                        </label>
                                    </div>
                                </div>
                                <div id="staffNotice" class="alert alert-info py-2 mt-2 small {{ old('role') === 'staff' ? '' : 'd-none' }}">
                                    <i class="bi bi-info-circle me-1"></i>Staff accounts require admin approval before login.
                                </div>
                            </div>

                            {{-- Full Name --}}
                            <div class="mb-3">
                                <label class="form-label fw-semibold small" style="color:var(--primary);">Full Name</label>
                                <div class="input-group">
                                    <span class="input-group-text" style="background:#f8f9fc; border-right:none;">
                                        <i class="bi bi-person" style="color:var(--primary);"></i>
                                    </span>
                                    <input type="text" name="name"
                                           class="form-control @error('name') is-invalid @enderror"
                                           style="border-left:none;"
                                           placeholder="Your full name"
                                           value="{{ old('name') }}" required autofocus>
                                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>

                            {{-- Email --}}
                            <div class="mb-3">
                                <label class="form-label fw-semibold small" style="color:var(--primary);">Email Address</label>
                                <div class="input-group">
                                    <span class="input-group-text" style="background:#f8f9fc; border-right:none;">
                                        <i class="bi bi-envelope" style="color:var(--primary);"></i>
                                    </span>
                                    <input type="email" name="email"
                                           class="form-control @error('email') is-invalid @enderror"
                                           style="border-left:none;"
                                           placeholder="you@email.com"
                                           value="{{ old('email') }}" required>
                                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>

                            {{-- Phone --}}
                            <div class="mb-3">
                                <label class="form-label fw-semibold small" style="color:var(--primary);">
                                    Phone <span class="text-muted fw-normal">(optional)</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text" style="background:#f8f9fc; border-right:none;">
                                        <i class="bi bi-telephone" style="color:var(--primary);"></i>
                                    </span>
                                    <input type="text" name="phone" class="form-control"
                                           style="border-left:none;"
                                           placeholder="+1 (555) 000-0000"
                                           value="{{ old('phone') }}">
                                </div>
                            </div>

                            {{-- Password --}}
                            <div class="mb-3">
                                <label class="form-label fw-semibold small" style="color:var(--primary);">Password</label>
                                <div class="input-group">
                                    <span class="input-group-text" style="background:#f8f9fc; border-right:none;">
                                        <i class="bi bi-lock" style="color:var(--primary);"></i>
                                    </span>
                                    <input type="password" name="password" id="passInput"
                                           class="form-control @error('password') is-invalid @enderror"
                                           style="border-left:none; border-right:none;"
                                           placeholder="Min. 8 characters" required>
                                    <button type="button" class="input-group-text" style="background:#f8f9fc; cursor:pointer;"
                                            onclick="togglePass('passInput','eyePass')">
                                        <i class="bi bi-eye" id="eyePass" style="color:var(--primary);"></i>
                                    </button>
                                    @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>

                            {{-- Confirm Password --}}
                            <div class="mb-4">
                                <label class="form-label fw-semibold small" style="color:var(--primary);">Confirm Password</label>
                                <div class="input-group">
                                    <span class="input-group-text" style="background:#f8f9fc; border-right:none;">
                                        <i class="bi bi-lock-fill" style="color:var(--primary);"></i>
                                    </span>
                                    <input type="password" name="password_confirmation" id="passConf"
                                           class="form-control"
                                           style="border-left:none; border-right:none;"
                                           placeholder="Repeat password" required>
                                    <button type="button" class="input-group-text" style="background:#f8f9fc; cursor:pointer;"
                                            onclick="togglePass('passConf','eyeConf')">
                                        <i class="bi bi-eye" id="eyeConf" style="color:var(--primary);"></i>
                                    </button>
                                </div>
                            </div>

                            {{-- Submit --}}
                            <button type="submit" class="btn w-100 py-3 fw-bold text-white mb-3"
                                    style="background:var(--primary); border-radius:10px; font-size:1rem;">
                                <i class="bi bi-person-plus me-2"></i>Create Account
                            </button>

                            <p class="text-center text-muted small mb-0">
                                Already have an account?
                                <a href="{{ route('login') }}" class="fw-semibold" style="color:var(--primary);">Sign In</a>
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
Swal.fire({ icon:'success', title:'Account Created!', text:@json(session('success')), timer:3500, showConfirmButton:false });
</script>
@endif
<script>
function togglePass(inputId, iconId) {
    const input = document.getElementById(inputId);
    const icon  = document.getElementById(iconId);
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.replace('bi-eye','bi-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.replace('bi-eye-slash','bi-eye');
    }
}

// Role card toggle styling
const roleCustomer = document.getElementById('roleCustomer');
const roleStaff    = document.getElementById('roleStaff');
const labelCus     = document.getElementById('labelCustomer');
const labelStaff   = document.getElementById('labelStaff');
const staffNotice  = document.getElementById('staffNotice');

function applyRoleStyle(selected) {
    if (selected === 'customer') {
        labelCus.style.borderColor   = 'var(--primary)';
        labelCus.style.background    = '#f0f4ff';
        labelCus.style.color         = 'var(--primary)';
        labelStaff.style.borderColor = '#dee2e6';
        labelStaff.style.background  = '#fff';
        labelStaff.style.color       = '#6c757d';
        staffNotice.classList.add('d-none');
    } else {
        labelStaff.style.borderColor = 'var(--primary)';
        labelStaff.style.background  = '#f0f4ff';
        labelStaff.style.color       = 'var(--primary)';
        labelCus.style.borderColor   = '#dee2e6';
        labelCus.style.background    = '#fff';
        labelCus.style.color         = '#6c757d';
        staffNotice.classList.remove('d-none');
    }
}

roleCustomer.addEventListener('change', () => applyRoleStyle('customer'));
roleStaff.addEventListener('change',    () => applyRoleStyle('staff'));
</script>
@endpush
