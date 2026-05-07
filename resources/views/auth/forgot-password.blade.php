@extends('layouts.public')
@section('title','Forgot Password')

@section('content')
<section class="py-5" style="background:linear-gradient(135deg,var(--primary),var(--primary-dark)); min-height:85vh; display:flex; align-items:center;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="card border-0 shadow-lg" style="border-radius:16px; overflow:hidden;">
                    <div class="card-header text-center py-4" style="background:var(--primary); border:none;">
                        <i class="bi bi-key" style="font-size:2.5rem; color:var(--secondary);"></i>
                        <h4 class="text-white fw-bold mb-0 mt-2">Forgot Password?</h4>
                        <p class="text-white-50 small mb-0">Enter your email to receive a reset link</p>
                    </div>
                    <div class="card-body p-4 p-md-5">
                        @if(session('success'))
                        <div class="alert alert-success"><i class="bi bi-check-circle me-2"></i>{{ session('success') }}</div>
                        @endif
                        @if($errors->any())
                        <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
                        @endif

                        <form method="POST" action="{{ route('password.email') }}">
                            @csrf
                            <div class="mb-4">
                                <label class="form-label fw-semibold">Email Address</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                    <input type="email" name="email" class="form-control"
                                           value="{{ old('email') }}" placeholder="you@email.com" required autofocus>
                                </div>
                            </div>
                            <button type="submit" class="btn w-100 py-3 fw-bold text-white" style="background:var(--primary); border-radius:8px;">
                                <i class="bi bi-send me-2"></i> Send Reset Link
                            </button>
                        </form>
                        <div class="text-center mt-3">
                            <a href="{{ route('login') }}" style="color:var(--primary);">
                                <i class="bi bi-arrow-left me-1"></i> Back to Login
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
