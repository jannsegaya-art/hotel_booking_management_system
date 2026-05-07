@extends('layouts.public')
@section('title', 'Our Rooms — Grand Royal Hotel')

@section('content')

{{-- Hero --}}
<section style="background:linear-gradient(135deg,var(--primary) 0%,var(--primary-dark) 100%); padding:70px 0 50px;">
    <div class="container text-center text-white">
        <h1 class="display-5 fw-bold mb-2" style="font-family:'Playfair Display',serif;">Our Rooms & Suites</h1>
        <p class="lead mb-0 opacity-75">Discover your perfect retreat — elegance and comfort in every room</p>
    </div>
</section>

{{-- Filter Tabs --}}
<section class="py-4" style="background:#f8f9fc; border-bottom:1px solid #dee2e6;">
    <div class="container">
        <div class="d-flex flex-wrap justify-content-center gap-2">
            <button class="btn btn-sm filter-btn active px-4 py-2 rounded-pill fw-semibold" data-filter="all"
                    style="background:var(--primary); color:#fff;">All Rooms</button>
            @foreach($rooms->pluck('room_type')->unique() as $type)
            <button class="btn btn-sm filter-btn px-4 py-2 rounded-pill fw-semibold" data-filter="{{ strtolower($type) }}"
                    style="border:2px solid var(--primary); color:var(--primary);">{{ $type }}</button>
            @endforeach
        </div>
    </div>
</section>

{{-- Rooms Grid --}}
<section class="py-5">
    <div class="container">
        @if($rooms->isEmpty())
        <div class="text-center py-5">
            <i class="bi bi-building-x text-muted" style="font-size:4rem;"></i>
            <p class="text-muted mt-3">No rooms available at the moment.</p>
        </div>
        @else
        <div class="row g-4" id="roomsGrid">
            @foreach($rooms as $room)
            <div class="col-md-6 col-lg-4 room-card" data-type="{{ strtolower($room->room_type) }}">
                <div class="card border-0 shadow-sm h-100"
                     style="border-radius:16px; overflow:hidden; transition:transform .25s, box-shadow .25s;"
                     onmouseenter="this.style.transform='translateY(-6px)'; this.style.boxShadow='0 12px 30px rgba(0,0,0,.15)'"
                     onmouseleave="this.style.transform=''; this.style.boxShadow=''">

                    {{-- Room Image --}}
                    <div style="height:220px; position:relative; overflow:hidden; background:#f0f2f8; cursor:zoom-in;"
                         onclick="openLightbox('{{ $room->image ? asset($room->image) : '' }}', 'Room {{ $room->room_number }} — {{ $room->room_type }}')">
                        @if($room->image && file_exists(public_path($room->image)))
                            <img src="{{ asset($room->image) }}"
                                 alt="Room {{ $room->room_number }}"
                                 style="width:100%; height:100%; object-fit:cover; transition:transform .4s;"
                                 onmouseenter="this.style.transform='scale(1.05)'"
                                 onmouseleave="this.style.transform='scale(1)'">
                        @else
                            <div style="height:100%; background:linear-gradient(135deg,var(--primary) 0%,var(--secondary) 100%);
                                        display:flex; align-items:center; justify-content:center;">
                                <div class="text-center text-white">
                                    <i class="bi bi-building" style="font-size:4rem; opacity:.5;"></i>
                                    <div class="small opacity-75 mt-1">No photo yet</div>
                                </div>
                            </div>
                        @endif

                        {{-- Zoom hint --}}
                        @if($room->image && file_exists(public_path($room->image)))
                        <div style="position:absolute; bottom:8px; right:8px; background:rgba(0,0,0,.55);
                                    color:#fff; border-radius:6px; padding:3px 8px; font-size:.72rem;">
                            <i class="bi bi-zoom-in me-1"></i>Click to zoom
                        </div>
                        @endif

                        {{-- Status badge --}}
                        <div style="position:absolute; top:12px; left:12px;">
                            @php $statusColors = ['available'=>'#28a745','occupied'=>'#dc3545','maintenance'=>'#ffc107']; @endphp
                            <span class="badge px-3 py-2 rounded-pill"
                                  style="background:{{ $statusColors[$room->status] ?? '#6c757d' }}; color:#fff; font-size:.78rem;">
                                {{ ucfirst($room->status) }}
                            </span>
                        </div>

                        {{-- Price badge --}}
                        <div style="position:absolute; top:12px; right:12px;">
                            <span class="fw-bold px-3 py-1 rounded-pill"
                                  style="background:rgba(0,0,0,.6); color:#fff; font-size:.9rem;">
                                ₱{{ number_format($room->price_per_night, 0) }}/night
                            </span>
                        </div>
                    </div>

                    {{-- Card Body --}}
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <span class="badge mb-1 rounded-pill px-3" style="background:var(--secondary); color:#fff; font-size:.75rem;">
                                    {{ $room->room_type }}
                                </span>
                                <h5 class="fw-bold mb-0" style="color:var(--primary);">Room {{ $room->room_number }}</h5>
                            </div>
                            @if($room->ratings->count() > 0)
                            <div class="text-end">
                                <div class="fw-bold small" style="color:var(--secondary);">
                                    <i class="bi bi-star-fill me-1"></i>{{ number_format($room->average_rating, 1) }}
                                </div>
                                <div class="text-muted" style="font-size:.7rem;">({{ $room->ratings->count() }} reviews)</div>
                            </div>
                            @endif
                        </div>

                        <p class="text-muted small mb-3" style="line-height:1.5;">
                            {{ Str::limit($room->description, 90) }}
                        </p>

                        <div class="d-flex gap-3 mb-3 small text-muted">
                            <span><i class="bi bi-people me-1"></i>{{ $room->capacity }} guests</span>
                            <span><i class="bi bi-layers me-1"></i>Floor {{ $room->floor }}</span>
                        </div>

                        @if($room->amenities)
                        <div class="d-flex flex-wrap gap-1 mb-3">
                            @foreach(array_slice((array)$room->amenities, 0, 4) as $a)
                            <span class="badge rounded-pill" style="background:#f0f4ff; color:var(--primary); font-size:.7rem;">{{ $a }}</span>
                            @endforeach
                            @if(count((array)$room->amenities) > 4)
                            <span class="badge rounded-pill" style="background:#f0f4ff; color:var(--primary); font-size:.7rem;">
                                +{{ count((array)$room->amenities) - 4 }} more
                            </span>
                            @endif
                        </div>
                        @endif

                        {{-- Book button --}}
                        @if($room->status === 'available')
                            @auth
                                @if(auth()->user()->isCustomer())
                                <a href="{{ route('customer.bookings.create', ['room_id' => $room->id]) }}"
                                   class="btn w-100 fw-semibold text-white" style="background:var(--primary); border-radius:8px;">
                                    <i class="bi bi-calendar-check me-1"></i>Book Now
                                </a>
                                @else
                                <button class="btn w-100 fw-semibold" disabled
                                        style="background:#e9ecef; color:#6c757d; border-radius:8px;">Available</button>
                                @endif
                            @else
                            <a href="{{ route('login') }}" class="btn w-100 fw-semibold text-white"
                               style="background:var(--primary); border-radius:8px;">
                                <i class="bi bi-lock me-1"></i>Login to Book
                            </a>
                            @endauth
                        @else
                        <button class="btn w-100 fw-semibold" disabled
                                style="background:#e9ecef; color:#6c757d; border-radius:8px;">
                            {{ ucfirst($room->status) }}
                        </button>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</section>

{{-- ── LIGHTBOX MODAL ── --}}
<div id="lightboxModal"
     style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; z-index:9999;
            background:rgba(0,0,0,.92); align-items:center; justify-content:center; flex-direction:column;">
    <div style="position:relative; max-width:90vw; max-height:85vh;">
        <img id="lightboxImg" src="" alt=""
             style="max-width:90vw; max-height:80vh; object-fit:contain; border-radius:8px;
                    box-shadow:0 20px 60px rgba(0,0,0,.5); transition:transform .3s;"
             id="lightboxImg">

        {{-- Zoom controls --}}
        <div style="position:absolute; bottom:-50px; left:50%; transform:translateX(-50%);
                    display:flex; gap:12px; align-items:center;">
            <button onclick="zoomOut()" class="btn btn-sm btn-outline-light rounded-circle" style="width:38px;height:38px;">
                <i class="bi bi-zoom-out"></i>
            </button>
            <span id="zoomLevel" style="color:#fff; font-size:.85rem; min-width:45px; text-align:center;">100%</span>
            <button onclick="zoomIn()" class="btn btn-sm btn-outline-light rounded-circle" style="width:38px;height:38px;">
                <i class="bi bi-zoom-in"></i>
            </button>
        </div>
    </div>

    {{-- Caption --}}
    <div id="lightboxCaption"
         style="color:rgba(255,255,255,.8); margin-top:60px; font-size:.9rem; text-align:center;"></div>

    {{-- Close button --}}
    <button onclick="closeLightbox()"
            style="position:fixed; top:20px; right:24px; background:none; border:none;
                   color:#fff; font-size:2rem; cursor:pointer; line-height:1;">
        <i class="bi bi-x-circle-fill"></i>
    </button>

    <div style="position:fixed; bottom:16px; color:rgba(255,255,255,.4); font-size:.78rem;">
        Press ESC or click outside to close
    </div>
</div>

@endsection

@push('scripts')
<script>
// ── Filter tabs ──
document.querySelectorAll('.filter-btn').forEach(function(btn) {
    btn.addEventListener('click', function() {
        document.querySelectorAll('.filter-btn').forEach(function(b) {
            b.style.background = 'transparent';
            b.style.color = 'var(--primary)';
        });
        this.style.background = 'var(--primary)';
        this.style.color = '#fff';
        var filter = this.dataset.filter;
        document.querySelectorAll('.room-card').forEach(function(card) {
            card.style.display = (filter === 'all' || card.dataset.type === filter) ? '' : 'none';
        });
    });
});

// ── Lightbox ──
var currentZoom = 1;

function openLightbox(src, caption) {
    if (!src) return;
    currentZoom = 1;
    document.getElementById('lightboxImg').src = src;
    document.getElementById('lightboxImg').style.transform = 'scale(1)';
    document.getElementById('lightboxCaption').textContent = caption || '';
    document.getElementById('zoomLevel').textContent = '100%';
    document.getElementById('lightboxModal').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function closeLightbox() {
    document.getElementById('lightboxModal').style.display = 'none';
    document.body.style.overflow = '';
    currentZoom = 1;
}

function zoomIn() {
    if (currentZoom >= 3) return;
    currentZoom = Math.min(3, currentZoom + 0.25);
    document.getElementById('lightboxImg').style.transform = 'scale(' + currentZoom + ')';
    document.getElementById('zoomLevel').textContent = Math.round(currentZoom * 100) + '%';
}

function zoomOut() {
    if (currentZoom <= 0.5) return;
    currentZoom = Math.max(0.5, currentZoom - 0.25);
    document.getElementById('lightboxImg').style.transform = 'scale(' + currentZoom + ')';
    document.getElementById('zoomLevel').textContent = Math.round(currentZoom * 100) + '%';
}

// Close on backdrop click
document.getElementById('lightboxModal').addEventListener('click', function(e) {
    if (e.target === this) closeLightbox();
});

// Close on ESC
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeLightbox();
    if (e.key === '+' || e.key === '=') zoomIn();
    if (e.key === '-') zoomOut();
});
</script>
@endpush
