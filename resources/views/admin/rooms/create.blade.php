@extends('layouts.admin')
@section('title', isset($room) ? 'Edit Room' : 'Add Room')

@section('content')
<div class="container-fluid py-4">
    <div class="mb-4">
        <a href="{{ route('admin.rooms.index') }}" class="btn btn-sm btn-outline-secondary mb-2">
            <i class="bi bi-arrow-left me-1"></i> Back to Rooms
        </a>
        <h2 class="fw-bold mb-0" style="color:var(--primary)">
            <i class="bi bi-{{ isset($room) ? 'pencil' : 'plus-circle' }} me-2"></i>
            {{ isset($room) ? 'Edit Room '.$room->room_number : 'Add New Room' }}
        </h2>
    </div>

    @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show">
        <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="card border-0 shadow-sm" style="border-radius:12px;">
        <div class="card-body p-4 p-md-5">
            <form method="POST"
                  action="{{ isset($room) ? route('admin.rooms.update', $room) : route('admin.rooms.store') }}"
                  enctype="multipart/form-data">
                @csrf
                @if(isset($room)) @method('PUT') @endif

                <div class="row g-4">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Room Number <span class="text-danger">*</span></label>
                        <input type="text" name="room_number" class="form-control @error('room_number') is-invalid @enderror"
                               value="{{ old('room_number', $room->room_number ?? '') }}" placeholder="e.g. 101" required>
                        @error('room_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Room Type <span class="text-danger">*</span></label>
                        <select name="room_type" class="form-select @error('room_type') is-invalid @enderror" required>
                            <option value="">-- Select Type --</option>
                            @foreach(['Standard','Deluxe','Suite','Family','Presidential'] as $t)
                            <option value="{{ $t }}" {{ old('room_type', $room->room_type ?? '') === $t ? 'selected' : '' }}>{{ $t }}</option>
                            @endforeach
                        </select>
                        @error('room_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Floor <span class="text-danger">*</span></label>
                        <input type="number" name="floor" class="form-control @error('floor') is-invalid @enderror"
                               value="{{ old('floor', $room->floor ?? 1) }}" min="1" max="99" required>
                        @error('floor')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Price Per Night (₱) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">₱</span>
                            <input type="number" name="price_per_night" step="0.01" min="0"
                                   class="form-control @error('price_per_night') is-invalid @enderror"
                                   value="{{ old('price_per_night', $room->price_per_night ?? '') }}" placeholder="0.00" required>
                            @error('price_per_night')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Capacity (Guests) <span class="text-danger">*</span></label>
                        <input type="number" name="capacity" class="form-control @error('capacity') is-invalid @enderror"
                               value="{{ old('capacity', $room->capacity ?? 2) }}" min="1" max="20" required>
                        @error('capacity')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Status</label>
                        <select name="status" class="form-select">
                            @foreach(['available','occupied','maintenance'] as $s)
                            <option value="{{ $s }}" {{ old('status', $room->status ?? 'available') === $s ? 'selected' : '' }}>
                                {{ ucfirst($s) }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-semibold">Description</label>
                        <textarea name="description" class="form-control" rows="3"
                                  placeholder="Describe the room features...">{{ old('description', $room->description ?? '') }}</textarea>
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-semibold">Amenities</label>
                        <div class="row g-2">
                            @php
                            $allAmenities = ['WiFi','TV','AC','Mini Bar','Jacuzzi','Room Service','Kitchen','Living Room','Balcony','Butler Service','Extra Beds','Safe','Hair Dryer','Coffee Maker'];
                            $selectedAmenities = old('amenities', isset($room) && $room->amenities ? $room->amenities : []);
                            @endphp
                            @foreach($allAmenities as $amenity)
                            <div class="col-6 col-md-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="amenities[]"
                                           value="{{ $amenity }}" id="am_{{ Str::slug($amenity) }}"
                                           {{ in_array($amenity, (array)$selectedAmenities) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="am_{{ Str::slug($amenity) }}">{{ $amenity }}</label>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="col-12 d-flex gap-2 justify-content-end">
                        <a href="{{ route('admin.rooms.index') }}" class="btn btn-outline-secondary px-4">Cancel</a>
                        <button type="submit" class="btn px-5 fw-bold text-white" style="background:var(--primary);">
                            <i class="bi bi-save me-2"></i> {{ isset($room) ? 'Update Room' : 'Add Room' }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
