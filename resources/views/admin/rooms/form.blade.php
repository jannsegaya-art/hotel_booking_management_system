@extends('layouts.admin')
@section('title', isset($room) ? 'Edit Room ' . $room->room_number : 'Add New Room')

@section('content')
<div class="container-fluid py-4">
    <div class="mb-4">
        <a href="{{ route('admin.rooms.index') }}" class="btn btn-sm btn-outline-secondary mb-2">
            <i class="bi bi-arrow-left me-1"></i> Back to Rooms
        </a>
        <h4 class="fw-bold mb-0" style="color:var(--primary);">
            <i class="bi bi-{{ isset($room) ? 'pencil' : 'plus-circle' }} me-2"></i>
            {{ isset($room) ? 'Edit Room ' . $room->room_number : 'Add New Room' }}
        </h4>
    </div>

    @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show shadow-sm">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>
        <ul class="mb-0 ps-3">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="card border-0 shadow-sm" style="border-radius:14px;">
        <div class="card-body p-4 p-md-5">
            <form method="POST"
                  action="{{ isset($room) ? route('admin.rooms.update', $room) : route('admin.rooms.store') }}"
                  enctype="multipart/form-data">
                @csrf
                @if(isset($room)) @method('PUT') @endif

                <div class="row g-4">

                    {{-- ── Room Photo Upload ── --}}
                    <div class="col-12">
                        <label class="form-label fw-semibold">
                            <i class="bi bi-image me-1" style="color:var(--secondary);"></i>
                            Room Photo
                        </label>
                        <div class="row g-3 align-items-center">

                            {{-- Preview Box --}}
                            <div class="col-md-4">
                                <div id="imagePreviewBox"
                                     style="width:100%; height:200px; border-radius:12px; overflow:hidden;
                                            border:2px dashed {{ isset($room) && $room->image ? 'var(--secondary)' : '#dee2e6' }};
                                            background:#f8f9fc; display:flex; align-items:center; justify-content:center;
                                            position:relative; cursor:pointer;"
                                     onclick="document.getElementById('roomImageInput').click()">
                                    @if(isset($room) && $room->image)
                                        <img id="roomImagePreview"
                                             src="{{ asset($room->image) }}"
                                             alt="Room Image"
                                             style="width:100%; height:100%; object-fit:cover;">
                                    @else
                                        <div id="roomImagePlaceholder" class="text-center p-3">
                                            <i class="bi bi-camera-fill d-block mb-2" style="font-size:2.5rem; color:#dee2e6;"></i>
                                            <span class="text-muted small">Click to upload photo</span>
                                        </div>
                                        <img id="roomImagePreview" src="" alt=""
                                             style="width:100%; height:100%; object-fit:cover; display:none;">
                                    @endif
                                    {{-- Upload overlay --}}
                                    <div style="position:absolute; bottom:0; left:0; right:0;
                                                background:rgba(26,60,143,.7); color:#fff; text-align:center;
                                                padding:.4rem; font-size:.78rem;">
                                        <i class="bi bi-upload me-1"></i>
                                        {{ isset($room) && $room->image ? 'Click to change photo' : 'Click to upload photo' }}
                                    </div>
                                </div>
                                <input type="file" id="roomImageInput" name="image"
                                       class="d-none" accept="image/jpeg,image/png,image/jpg,image/gif">
                                <div class="text-muted mt-1" style="font-size:.75rem;">
                                    JPG, PNG or GIF · Max 5MB
                                </div>
                                @error('image')
                                <div class="text-danger small mt-1"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</div>
                                @enderror

                                {{-- Remove existing image --}}
                                @if(isset($room) && $room->image)
                                <div class="form-check mt-2">
                                    <input class="form-check-input" type="checkbox" name="remove_image" id="removeImage" value="1">
                                    <label class="form-check-label small text-danger" for="removeImage">
                                        Remove current photo
                                    </label>
                                </div>
                                @endif
                            </div>

                            {{-- Room Info Fields beside photo --}}
                            <div class="col-md-8">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold small">Room Number <span class="text-danger">*</span></label>
                                        <input type="text" name="room_number"
                                               class="form-control @error('room_number') is-invalid @enderror"
                                               value="{{ old('room_number', $room->room_number ?? '') }}"
                                               placeholder="e.g. 101" required>
                                        @error('room_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold small">Room Type <span class="text-danger">*</span></label>
                                        <select name="room_type" class="form-select @error('room_type') is-invalid @enderror" required>
                                            <option value="">-- Select Type --</option>
                                            @foreach(['Standard','Deluxe','Suite','Family','Presidential'] as $type)
                                            <option value="{{ $type }}" {{ old('room_type', $room->room_type ?? '') === $type ? 'selected' : '' }}>
                                                {{ $type }}
                                            </option>
                                            @endforeach
                                        </select>
                                        @error('room_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold small">Floor <span class="text-danger">*</span></label>
                                        <input type="number" name="floor"
                                               class="form-control @error('floor') is-invalid @enderror"
                                               value="{{ old('floor', $room->floor ?? 1) }}" min="1" required>
                                        @error('floor')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold small">Price/Night (₱) <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text">₱</span>
                                            <input type="number" name="price_per_night" step="0.01"
                                                   class="form-control @error('price_per_night') is-invalid @enderror"
                                                   value="{{ old('price_per_night', $room->price_per_night ?? '') }}"
                                                   placeholder="0.00" required>
                                            @error('price_per_night')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold small">Capacity <span class="text-danger">*</span></label>
                                        <input type="number" name="capacity"
                                               class="form-control @error('capacity') is-invalid @enderror"
                                               value="{{ old('capacity', $room->capacity ?? 2) }}" min="1" max="20" required>
                                        @error('capacity')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold small">Status <span class="text-danger">*</span></label>
                                        <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                                            @foreach(['available','occupied','maintenance'] as $s)
                                            <option value="{{ $s }}" {{ old('status', $room->status ?? 'available') === $s ? 'selected' : '' }}>
                                                {{ ucfirst($s) }}
                                            </option>
                                            @endforeach
                                        </select>
                                        @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Description --}}
                    <div class="col-12">
                        <label class="form-label fw-semibold small">Description</label>
                        <textarea name="description" class="form-control" rows="3"
                                  placeholder="Describe this room...">{{ old('description', $room->description ?? '') }}</textarea>
                    </div>

                    {{-- Amenities --}}
                    <div class="col-12">
                        <label class="form-label fw-semibold small">Amenities</label>
                        @php
                        $allAmenities  = ['WiFi','TV','AC','Mini Bar','Jacuzzi','Room Service','Kitchen','Living Room','Balcony','Butler Service','Extra Beds','Safe','Hair Dryer','Bathrobe'];
                        $roomAmenities = isset($room) && $room->amenities ? (array)$room->amenities : [];
                        @endphp
                        <div class="row g-2">
                            @foreach($allAmenities as $amenity)
                            <div class="col-6 col-md-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="amenities[]"
                                           value="{{ $amenity }}" id="am_{{ Str::slug($amenity) }}"
                                           {{ in_array($amenity, old('amenities', $roomAmenities)) ? 'checked' : '' }}>
                                    <label class="form-check-label small" for="am_{{ Str::slug($amenity) }}">{{ $amenity }}</label>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Buttons --}}
                    <div class="col-12 d-flex gap-2 justify-content-end pt-2">
                        <a href="{{ route('admin.rooms.index') }}" class="btn btn-outline-secondary px-4">Cancel</a>
                        <button type="submit" class="btn px-5 fw-semibold text-white" style="background:var(--primary); border-radius:8px;">
                            <i class="bi bi-save me-2"></i>{{ isset($room) ? 'Update Room' : 'Add Room' }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('roomImageInput').addEventListener('change', function() {
    if (!this.files || !this.files[0]) return;
    var file = this.files[0];
    if (file.size > 5 * 1024 * 1024) {
        Swal.fire({ icon:'error', title:'Too Large', text:'Please select an image under 5MB.', confirmButtonColor:'#1a3c8f' });
        this.value = ''; return;
    }
    var reader = new FileReader();
    reader.onload = function(e) {
        var preview = document.getElementById('roomImagePreview');
        var placeholder = document.getElementById('roomImagePlaceholder');
        preview.src = e.target.result;
        preview.style.display = 'block';
        if (placeholder) placeholder.style.display = 'none';
        document.getElementById('imagePreviewBox').style.borderColor = 'var(--secondary)';
        document.getElementById('imagePreviewBox').style.borderStyle = 'solid';
    };
    reader.readAsDataURL(file);
});
</script>
@endpush
