@php
    $layout = auth()->user()->role === 'admin'
        ? 'layouts.admin'
        : (auth()->user()->role === 'staff' ? 'layouts.staff' : 'layouts.customer');
@endphp
@extends($layout)
@section('title', 'My Profile')

@section('content')
<div class="container-fluid py-2">

    <div class="mb-4">
        <h4 class="fw-bold mb-0" style="color:var(--primary);">
            <i class="bi bi-person-circle me-2"></i>My Profile
        </h4>
        <p class="text-muted small mb-0">Update your account photo, name, and password.</p>
    </div>

    {{-- Success / Error Alerts --}}
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
    @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show shadow-sm">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>
        <ul class="mb-0 ps-3">
            @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="row g-4">

        {{-- ── Profile Form ── --}}
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm" style="border-radius:14px;">
                <div class="card-header py-3"
                     style="background:var(--primary); border-radius:14px 14px 0 0;">
                    <h5 class="text-white mb-0 fw-semibold">
                        <i class="bi bi-person-fill me-2"></i>Profile Information
                    </h5>
                </div>
                <div class="card-body p-4">

                    {{-- IMPORTANT: enctype="multipart/form-data" is required for file upload --}}
                    <form method="POST"
                          action="{{ route('profile.update') }}"
                          enctype="multipart/form-data"
                          id="profileForm">
                        @csrf

                        {{-- ── Photo Upload ── --}}
                        <div class="text-center mb-4">
                            <div class="position-relative d-inline-block mb-2">
                                <img id="photoPreview"
                                     src="{{ $user->profile_photo_url }}"
                                     alt="Your Photo"
                                     class="rounded-circle shadow"
                                     style="width:120px; height:120px; object-fit:cover;
                                            border:3px solid var(--secondary);"
                                     onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=1a3c8f&color=fff&size=128&bold=true'">

                                {{-- Camera Overlay Button --}}
                                <label for="photoFile"
                                       class="position-absolute bottom-0 end-0 rounded-circle
                                              d-flex align-items-center justify-content-center shadow"
                                       style="width:36px; height:36px; background:var(--secondary);
                                              cursor:pointer; border:2px solid #fff; bottom:0; right:0;"
                                       title="Click to upload photo">
                                    <i class="bi bi-camera-fill text-white" style="font-size:.9rem;"></i>
                                </label>
                            </div>

                            {{-- Hidden file input --}}
                            <input type="file"
                                   id="photoFile"
                                   name="photo"
                                   class="d-none"
                                   accept="image/jpeg,image/png,image/jpg,image/gif">

                            <div class="text-muted small mt-1">
                                Click the <i class="bi bi-camera-fill" style="color:var(--secondary);"></i>
                                to change photo
                            </div>
                            <div class="text-muted" style="font-size:.75rem;">JPG, PNG or GIF · Max 5MB</div>

                            {{-- Show selected filename --}}
                            <div id="fileNameDisplay" class="mt-1 small text-success d-none">
                                <i class="bi bi-check-circle me-1"></i>
                                <span id="fileNameText"></span> selected — click Save to upload
                            </div>

                            @error('photo')
                            <div class="text-danger small mt-1">
                                <i class="bi bi-exclamation-circle me-1"></i>{{ $message }}
                            </div>
                            @enderror
                        </div>

                        {{-- ── Text Fields ── --}}
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label fw-semibold small">
                                    Full Name <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light">
                                        <i class="bi bi-person" style="color:var(--primary);"></i>
                                    </span>
                                    <input type="text"
                                           name="name"
                                           class="form-control @error('name') is-invalid @enderror"
                                           value="{{ old('name', $user->name) }}"
                                           placeholder="Your full name"
                                           required>
                                    @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold small">Email Address</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light">
                                        <i class="bi bi-envelope" style="color:var(--primary);"></i>
                                    </span>
                                    <input type="email"
                                           class="form-control bg-light"
                                           value="{{ $user->email }}"
                                           disabled>
                                </div>
                                <div class="form-text small text-muted">Email cannot be changed.</div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Phone Number</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light">
                                        <i class="bi bi-telephone" style="color:var(--primary);"></i>
                                    </span>
                                    <input type="text"
                                           name="phone"
                                           class="form-control"
                                           value="{{ old('phone', $user->phone) }}"
                                           placeholder="+63 9xx-xxx-xxxx">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Role</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light">
                                        <i class="bi bi-shield-fill" style="color:var(--primary);"></i>
                                    </span>
                                    <input type="text"
                                           class="form-control bg-light text-capitalize fw-semibold"
                                           value="{{ $user->role }}"
                                           disabled>
                                </div>
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold small">Address</label>
                                <textarea name="address"
                                          class="form-control"
                                          rows="2"
                                          placeholder="Your home address">{{ old('address', $user->address) }}</textarea>
                            </div>

                            <div class="col-12 pt-1">
                                <button type="submit"
                                        class="btn w-100 py-2 fw-semibold text-white"
                                        style="background:var(--primary); border-radius:8px; font-size:.95rem;">
                                    <i class="bi bi-save me-2"></i>Save Profile
                                </button>
                            </div>
                        </div>
                    </form>

                </div>
            </div>
        </div>

        {{-- ── Right Column ── --}}
        <div class="col-lg-5">

            {{-- Change Password --}}
            <div class="card border-0 shadow-sm mb-4" style="border-radius:14px;">
                <div class="card-header py-3"
                     style="background:var(--primary); border-radius:14px 14px 0 0;">
                    <h5 class="text-white mb-0 fw-semibold">
                        <i class="bi bi-lock-fill me-2"></i>Change Password
                    </h5>
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('profile.password') }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Current Password</label>
                            <input type="password"
                                   name="current_password"
                                   class="form-control @error('current_password') is-invalid @enderror"
                                   placeholder="Enter current password"
                                   required>
                            @error('current_password')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold small">New Password</label>
                            <input type="password"
                                   name="password"
                                   class="form-control @error('password') is-invalid @enderror"
                                   placeholder="Min. 8 characters"
                                   required>
                            @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-semibold small">Confirm New Password</label>
                            <input type="password"
                                   name="password_confirmation"
                                   class="form-control"
                                   placeholder="Repeat new password"
                                   required>
                        </div>
                        <button type="submit"
                                class="btn w-100 py-2 fw-semibold text-white"
                                style="background:var(--secondary); border-radius:8px;">
                            <i class="bi bi-key me-2"></i>Change Password
                        </button>
                    </form>
                </div>
            </div>

            {{-- Account Details --}}
            <div class="card border-0 shadow-sm" style="border-radius:14px;">
                <div class="card-header py-3"
                     style="background:var(--secondary); border-radius:14px 14px 0 0;">
                    <h5 class="text-white mb-0 fw-semibold">
                        <i class="bi bi-info-circle me-2"></i>Account Details
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="d-flex flex-column gap-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted small fw-semibold">Role</span>
                            <span class="badge px-3 py-2 text-capitalize"
                                  style="background:{{ $user->role === 'admin' ? 'var(--primary)' : ($user->role === 'staff' ? 'var(--secondary)' : '#198754') }};
                                         color:#fff; font-size:.8rem;">
                                {{ $user->role }}
                            </span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted small fw-semibold">Status</span>
                            <span class="badge px-3 py-2 bg-{{ $user->status === 'active' ? 'success' : ($user->status === 'pending' ? 'warning text-dark' : 'danger') }}"
                                  style="font-size:.8rem;">
                                {{ ucfirst($user->status) }}
                            </span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted small fw-semibold">Member Since</span>
                            <span class="small">{{ $user->created_at->format('M d, Y') }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted small fw-semibold">Last Updated</span>
                            <span class="small text-muted">{{ $user->updated_at->diffForHumans() }}</span>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection

@push('scripts')
@if(session('success'))
<script>
Swal.fire({
    icon: 'success',
    title: 'Saved!',
    text: @json(session('success')),
    timer: 2500,
    showConfirmButton: false,
    toast: true,
    position: 'top-end'
});
</script>
@endif

<script>
document.getElementById('photoFile').addEventListener('change', function () {
    if (!this.files || !this.files[0]) return;

    var file = this.files[0];

    // Validate size (5MB)
    if (file.size > 5 * 1024 * 1024) {
        Swal.fire({
            icon: 'error',
            title: 'File Too Large',
            text: 'Please select an image smaller than 5MB.',
            confirmButtonColor: '#1a3c8f'
        });
        this.value = '';
        return;
    }

    // Validate type
    var allowed = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
    if (!allowed.includes(file.type)) {
        Swal.fire({
            icon: 'error',
            title: 'Wrong File Type',
            text: 'Only JPG, PNG, or GIF images are allowed.',
            confirmButtonColor: '#1a3c8f'
        });
        this.value = '';
        return;
    }

    // Preview the selected photo
    var reader = new FileReader();
    reader.onload = function (e) {
        document.getElementById('photoPreview').src = e.target.result;
    };
    reader.readAsDataURL(file);

    // Show filename
    document.getElementById('fileNameText').textContent = file.name;
    document.getElementById('fileNameDisplay').classList.remove('d-none');
});
</script>
@endpush
