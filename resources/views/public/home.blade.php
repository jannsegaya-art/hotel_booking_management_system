@extends('layouts.public')
@section('title', 'Welcome — Grand Royal Hotel')

@section('content')

{{-- Hero --}}
<section style="background:linear-gradient(135deg,rgba(18,45,110,.92) 0%,rgba(26,60,143,.80) 100%),
    url('https://images.unsplash.com/photo-1566073771259-6a8506099945?w=1600&q=80') center/cover no-repeat;
    min-height:88vh; display:flex; align-items:center;">
    <div class="container text-center text-white py-5">
        <p class="mb-2" style="color:var(--secondary);font-size:.95rem;letter-spacing:3px;text-transform:uppercase;font-weight:600;">Welcome to</p>
        <h1 class="display-3 fw-bold mb-3" style="font-family:'Playfair Display',serif;text-shadow:0 2px 20px rgba(0,0,0,.3);">
            Grand Royal Hotel
        </h1>
        <p class="lead mb-4 opacity-75" style="max-width:600px; margin:0 auto 1.5rem;">
            Where luxury meets comfort. Experience world-class hospitality in the heart of the city.
        </p>
        <div class="d-flex justify-content-center gap-3 flex-wrap">
            <a href="{{ route('rooms') }}" class="btn btn-gold btn-lg px-5 py-3 rounded-pill fw-bold">
                <i class="bi bi-building me-2"></i>View Rooms
            </a>
            @guest
            <a href="{{ route('register') }}" class="btn btn-outline-light btn-lg px-5 py-3 rounded-pill fw-bold">
                <i class="bi bi-calendar-plus me-2"></i>Book Now
            </a>
            @endguest
        </div>
        <div class="row g-3 justify-content-center mt-5">
            @foreach([['50+','Luxury Rooms'],['25+','Years Experience'],['10K+','Happy Guests'],['24/7','Room Service']] as $s)
            <div class="col-6 col-md-3">
                <div class="p-3 rounded-3" style="background:rgba(255,255,255,.1);backdrop-filter:blur(10px);">
                    <div class="fw-bold fs-3" style="color:var(--secondary);">{{ $s[0] }}</div>
                    <div class="small opacity-75">{{ $s[1] }}</div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- Featured Rooms --}}
<section class="py-5" style="background:#f8f9fc;">
    <div class="container">
        <div class="text-center mb-5">
            <span class="badge px-3 py-2 mb-2 rounded-pill" style="background:var(--secondary);color:#fff;">Our Rooms</span>
            <h2 class="section-title fs-1 mb-2">Elegant Rooms & Suites</h2>
            <p class="text-muted">Each room designed for maximum comfort and luxury</p>
        </div>

        @if($featured_rooms->isEmpty())
        <div class="text-center py-5">
            <i class="bi bi-building-x text-muted" style="font-size:4rem;"></i>
            <p class="text-muted mt-3">No rooms available at the moment.</p>
        </div>
        @else
        <div class="row g-4">
            @foreach($featured_rooms as $room)
            <div class="col-md-6 col-lg-4">
                <div class="card border-0 shadow-sm h-100"
                     style="border-radius:16px; overflow:hidden; transition:transform .25s, box-shadow .25s;"
                     onmouseenter="this.style.transform='translateY(-6px)'; this.style.boxShadow='0 12px 30px rgba(0,0,0,.15)'"
                     onmouseleave="this.style.transform=''; this.style.boxShadow=''">

                    {{-- Room Photo --}}
                    <div style="height:220px; position:relative; overflow:hidden;
                                background:#f0f2f8; cursor:{{ $room->image && file_exists(public_path($room->image)) ? 'zoom-in' : 'default' }};"
                         onclick="openLightbox('{{ $room->image && file_exists(public_path($room->image)) ? asset($room->image) : '' }}', 'Room {{ $room->room_number }} — {{ $room->room_type }}')">

                        @if($room->image && file_exists(public_path($room->image)))
                            <img src="{{ asset($room->image) }}"
                                 alt="Room {{ $room->room_number }}"
                                 style="width:100%; height:100%; object-fit:cover; transition:transform .4s;"
                                 onmouseenter="this.style.transform='scale(1.06)'"
                                 onmouseleave="this.style.transform='scale(1)'">
                            <div style="position:absolute; bottom:8px; right:8px; background:rgba(0,0,0,.55);
                                        color:#fff; border-radius:6px; padding:3px 8px; font-size:.72rem;">
                                <i class="bi bi-zoom-in me-1"></i>Click to zoom
                            </div>
                        @else
                            <div style="height:100%; background:linear-gradient(135deg,var(--primary) 0%,var(--secondary) 100%);
                                        display:flex; align-items:center; justify-content:center;">
                                <i class="bi bi-building text-white" style="font-size:5rem; opacity:.35;"></i>
                            </div>
                        @endif

                        <div style="position:absolute; bottom:{{ ($room->image && file_exists(public_path($room->image))) ? '36px' : '12px' }}; left:12px;">
                            <span class="fw-bold text-white" style="font-size:1.3rem; text-shadow:0 1px 6px rgba(0,0,0,.5);">
                                ₱{{ number_format($room->price_per_night,0) }}<span style="font-size:.75rem; opacity:.8;">/night</span>
                            </span>
                        </div>
                        <div style="position:absolute; top:12px; right:12px;">
                            <span class="badge rounded-pill px-3" style="background:rgba(0,0,0,.55); color:#fff;">{{ $room->room_type }}</span>
                        </div>
                    </div>

                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-1" style="color:var(--primary);">Room {{ $room->room_number }}</h5>
                        <p class="text-muted small mb-3">{{ Str::limit($room->description, 80) }}</p>
                        <div class="d-flex gap-3 mb-3 small text-muted">
                            <span><i class="bi bi-people me-1"></i>{{ $room->capacity }} guests</span>
                            <span><i class="bi bi-layers me-1"></i>Floor {{ $room->floor }}</span>
                        </div>
                        @if($room->amenities)
                        <div class="d-flex flex-wrap gap-1 mb-3">
                            @foreach(array_slice((array)$room->amenities,0,3) as $a)
                            <span class="badge rounded-pill" style="background:#f0f4ff;color:var(--primary);font-size:.7rem;">{{ $a }}</span>
                            @endforeach
                        </div>
                        @endif
                        @auth
                            @if(auth()->user()->isCustomer())
                            <a href="{{ route('customer.bookings.create',['room_id'=>$room->id]) }}"
                               class="btn w-100 fw-semibold text-white" style="background:var(--primary);border-radius:8px;">
                                <i class="bi bi-calendar-check me-1"></i>Book Now
                            </a>
                            @else
                            <a href="{{ route('rooms') }}" class="btn w-100 fw-semibold"
                               style="border:2px solid var(--primary);color:var(--primary);border-radius:8px;">View Details</a>
                            @endif
                        @else
                        <a href="{{ route('login') }}" class="btn w-100 fw-semibold text-white"
                           style="background:var(--primary);border-radius:8px;">
                            <i class="bi bi-lock me-1"></i>Login to Book
                        </a>
                        @endauth
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        <div class="text-center mt-4">
            <a href="{{ route('rooms') }}" class="btn btn-outline-primary px-5 py-2 rounded-pill fw-semibold">
                View All Rooms <i class="bi bi-arrow-right ms-1"></i>
            </a>
        </div>
        @endif
    </div>
</section>

{{-- Why Choose Us --}}
<section class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="section-title fs-1 mb-2">Why Choose Grand Royal?</h2>
            <p class="text-muted">We go above and beyond to make your stay unforgettable</p>
        </div>
        <div class="row g-4">
            @foreach([
                ['bi-star-fill','World-Class Service','Personalized attention to every guest 24/7.'],
                ['bi-geo-alt-fill','Prime Location','Minutes from top attractions and business centers.'],
                ['bi-shield-check','Safe & Secure','24/7 security monitoring for your peace of mind.'],
                ['bi-wifi','Modern Amenities','High-speed WiFi and smart facilities throughout.'],
                ['bi-fork-knife','Fine Dining','Award-winning restaurants with international cuisine.'],
                ['bi-award-fill','Award Winning','Top luxury hotel for 5 consecutive years.'],
            ] as $f)
            <div class="col-md-6 col-lg-4">
                <div class="d-flex gap-3 p-4 rounded-3 h-100" style="background:#f8f9fc;border:1px solid #eef0f8;transition:.2s;"
                     onmouseenter="this.style.background='#fff';this.style.boxShadow='0 4px 20px rgba(0,0,0,.08)'"
                     onmouseleave="this.style.background='#f8f9fc';this.style.boxShadow=''">
                    <div class="flex-shrink-0 rounded-circle d-flex align-items-center justify-content-center"
                         style="width:52px;height:52px;background:linear-gradient(135deg,var(--primary),var(--secondary));flex:0 0 52px;">
                        <i class="bi {{ $f[0] }} text-white fs-5"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-1" style="color:var(--primary);">{{ $f[1] }}</h6>
                        <p class="text-muted small mb-0">{{ $f[2] }}</p>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

@if($ratings->count() > 0)
<section class="py-5" style="background:#f8f9fc;">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="section-title fs-1 mb-2">What Our Guests Say</h2>
        </div>
        <div class="row g-4">
            @foreach($ratings as $r)
            <div class="col-md-6 col-lg-4">
                <div class="card border-0 shadow-sm p-4 h-100" style="border-radius:14px;">
                    <div class="d-flex gap-1 mb-2">
                        @for($i=1;$i<=5;$i++)<i class="bi bi-star{{ $i<=$r->rating?'-fill':'' }}" style="color:var(--secondary);"></i>@endfor
                    </div>
                    <p class="text-muted small mb-3">"{{ Str::limit($r->comment, 120) }}"</p>
                    <div class="d-flex align-items-center gap-2 mt-auto">
                        <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold"
                             style="width:38px;height:38px;background:var(--primary);font-size:.9rem;">
                            {{ strtoupper(substr($r->user->name,0,1)) }}
                        </div>
                        <div>
                            <div class="fw-semibold small">{{ $r->user->name }}</div>
                            <div class="text-muted" style="font-size:.72rem;">Room {{ $r->room->room_number }}</div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

<section style="background:linear-gradient(135deg,var(--primary),var(--secondary));padding:80px 0;">
    <div class="container text-center text-white">
        <h2 class="fw-bold mb-3" style="font-family:'Playfair Display',serif;font-size:2.5rem;">Ready for an Unforgettable Stay?</h2>
        <p class="lead mb-4 opacity-75">Book your room today and experience the Grand Royal difference.</p>
        <div class="d-flex justify-content-center gap-3 flex-wrap">
            <a href="{{ route('rooms') }}" class="btn btn-light btn-lg px-5 py-3 rounded-pill fw-bold" style="color:var(--primary);">
                <i class="bi bi-building me-2"></i>Browse Rooms
            </a>
            <a href="{{ route('contact') }}" class="btn btn-outline-light btn-lg px-5 py-3 rounded-pill fw-bold">
                <i class="bi bi-telephone me-2"></i>Contact Us
            </a>
        </div>
    </div>
</section>

{{-- Lightbox --}}
<div id="lightboxModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; z-index:9999;
     background:rgba(0,0,0,.92); align-items:center; justify-content:center; flex-direction:column;">
    <div style="position:relative; max-width:90vw; max-height:85vh;">
        <img id="lightboxImg" src="" alt=""
             style="max-width:90vw; max-height:80vh; object-fit:contain; border-radius:8px; box-shadow:0 20px 60px rgba(0,0,0,.5); transition:transform .3s;">
        <div style="position:absolute; bottom:-50px; left:50%; transform:translateX(-50%); display:flex; gap:12px; align-items:center;">
            <button onclick="zoomOut()" class="btn btn-sm btn-outline-light rounded-circle" style="width:38px;height:38px;"><i class="bi bi-zoom-out"></i></button>
            <span id="zoomLevel" style="color:#fff;font-size:.85rem;min-width:45px;text-align:center;">100%</span>
            <button onclick="zoomIn()" class="btn btn-sm btn-outline-light rounded-circle" style="width:38px;height:38px;"><i class="bi bi-zoom-in"></i></button>
        </div>
    </div>
    <div id="lightboxCaption" style="color:rgba(255,255,255,.8);margin-top:60px;font-size:.9rem;text-align:center;"></div>
    <button onclick="closeLightbox()" style="position:fixed;top:20px;right:24px;background:none;border:none;color:#fff;font-size:2rem;cursor:pointer;line-height:1;">
        <i class="bi bi-x-circle-fill"></i>
    </button>
</div>

@endsection

@push('scripts')
<script>
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
document.getElementById('lightboxModal').addEventListener('click', function(e) { if (e.target === this) closeLightbox(); });
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeLightbox();
    if (e.key === '+' || e.key === '=') zoomIn();
    if (e.key === '-') zoomOut();
});
</script>
@endpush
