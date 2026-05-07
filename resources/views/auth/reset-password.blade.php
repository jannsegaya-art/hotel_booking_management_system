@extends('layouts.public')
@section('title','Reset Password')

@section('content')
<section class="py-5" style="background:linear-gradient(135deg,var(--primary),var(--primary-dark)); min-height:85vh; display:flex; align-items:center;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="card border-0 shadow-lg" style="border-radius:16px; overflow:hidden;">
                    <div class="card-header text-center py-4" style="background:var(--primary); border:none;">
                        <i class="bi bi-lock-fill" style="font-size:2.5rem; color:var(--secondary);"></i>
                        <h4 class="text-white fw-bold mb-0 mt-2">Reset Password</h4>
                        <p class="text-white-50 small mb-0">Enter your new password below</p>
                    </div>
                    <div class="card-body p-4 p-md-5">
                        @if($errors->any())
                        <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
                        @endif

                        <form method="POST" action="{{ route('password.update') }}">
                            @csrf
                            <input type="hidden" name="token" value="{{ $request->route('token') }}">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Email Address</label>
                                <input type="email" name="email" class="form-control"
                                       value="{{ old('email', $request->email) }}" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">New Password</label>
                                <input type="password" name="password" class="form-control"
                                       placeholder="Min. 8 characters" required>
                            </div>
                            <div class="mb-4">
                                <label class="form-label fw-semibold">Confirm Password</label>
                                <input type="password" name="password_confirmation" class="form-control"
                                       placeholder="Repeat new password" required>
                            </div>
                            <button type="submit" class="btn w-100 py-3 fw-bold text-white" style="background:var(--primary); border-radius:8px;">
                                <i class="bi bi-check-circle me-2"></i> Reset Password
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
