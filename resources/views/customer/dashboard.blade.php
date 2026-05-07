@extends('layouts.customer')
@section('title', 'My Dashboard')

@section('content')
<div class="container-fluid py-4">
    <div class="mb-4">
        <h2 class="fw-bold mb-0" style="color:var(--primary)">
            <i class="bi bi-house-heart me-2"></i>Welcome, {{ auth()->user()->name }}!
        </h2>
        <p class="text-muted mb-0">Manage your bookings and explore our rooms.</p>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    {{-- Stats --}}
    <div class="row g-3 mb-4">
        @foreach([
            ['Total Bookings','bi-calendar','var(--primary)',$stats['total']],
            ['Pending','bi-hourglass','#ffc107',$stats['pending']],
            ['Active Stays','bi-door-open','#28a745',$stats['checked_in']],
            ['Completed','bi-check-circle','#6c757d',$stats['completed']],
            ['Cancelled','bi-x-circle','#dc3545',$stats['cancelled']],
        ] as $s)
        <div class="col-6 col-md">
            <div class="card border-0 shadow-sm text-center p-3" style="border-radius:12px;">
                <i class="bi {{ $s[1] }} mb-2" style="font-size:1.8rem;color:{{ $s[2] }};"></i>
                <div class="fw-bold fs-4" style="color:{{ $s[2] }};">{{ $s[3] }}</div>
                <div class="text-muted small">{{ $s[0] }}</div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="row g-4">
        {{-- Recent Bookings --}}
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm" style="border-radius:12px;">
                <div class="card-header py-3 d-flex justify-content-between align-items-center" style="background:var(--primary);border-radius:12px 12px 0 0;">
                    <h5 class="text-white mb-0"><i class="bi bi-calendar-check me-2"></i>My Recent Bookings</h5>
                    <a href="{{ route('customer.bookings.index') }}" class="btn btn-sm text-white" style="border:1px solid rgba(255,255,255,0.5);">View All</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead style="background:#f8f9fc;">
                                <tr><th class="ps-4">Ref</th><th>Room</th><th>Check-In</th><th>Total</th><th>Status</th><th class="pe-4">Action</th></tr>
                            </thead>
                            <tbody>
                                @forelse($recent_bookings as $b)
                                <tr>
                                    <td class="ps-4 fw-semibold" style="color:var(--primary);">{{ $b->booking_reference }}</td>
                                    <td>Room {{ $b->room->room_number }}</td>
                                    <td>{{ $b->check_in_date->format('M d, Y') }}</td>
                                    <td class="fw-semibold" style="color:var(--secondary);">${{ number_format($b->total_amount,2) }}</td>
                                    <td>
                                        @php $sc=['pending'=>'warning text-dark','confirmed'=>'primary','checked_in'=>'success','checked_out'=>'secondary','cancelled'=>'danger']; @endphp
                                        <span class="badge bg-{{ $sc[$b->status] ?? 'secondary' }}">{{ ucfirst(str_replace('_',' ',$b->status)) }}</span>
                                    </td>
                                    <td class="pe-4">
                                        <a href="{{ route('customer.bookings.show', $b) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="6" class="text-center py-4 text-muted">No bookings yet. <a href="{{ route('customer.bookings.create') }}">Book a room!</a></td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="col-lg-4">
            {{-- Book CTA --}}
            <div class="card border-0 mb-4 text-center p-4" style="background:linear-gradient(135deg,var(--primary),var(--secondary));border-radius:12px;">
                <i class="bi bi-calendar-plus text-white" style="font-size:3rem;"></i>
                <h5 class="text-white fw-bold mt-3 mb-2">Ready for your next stay?</h5>
                <p class="text-white opacity-75 small mb-3">Browse our available rooms and book instantly.</p>
                <a href="{{ route('customer.bookings.create') }}" class="btn fw-semibold"
                   style="background:#fff;color:var(--primary);border-radius:8px;">
                    <i class="bi bi-search me-2"></i>Browse Rooms
                </a>
            </div>

            {{-- Notifications --}}
            <div class="card border-0 shadow-sm" style="border-radius:12px;">
                <div class="card-header py-3" style="background:var(--secondary);border-radius:12px 12px 0 0;">
                    <h6 class="text-white mb-0"><i class="bi bi-bell me-2"></i>Notifications</h6>
                </div>
                <div class="card-body p-3">
                    @forelse($notifications as $n)
                    <div class="d-flex gap-2 mb-2 p-2 rounded" style="background:#f8f9fc;">
                        <i class="bi bi-{{ ['success'=>'check-circle','info'=>'info-circle','warning'=>'exclamation-triangle','danger'=>'x-circle'][$n->type] ?? 'bell' }}"
                           style="color:{{ ['success'=>'#28a745','info'=>'#17a2b8','warning'=>'#ffc107','danger'=>'#dc3545'][$n->type] ?? 'var(--primary)' }};flex-shrink:0;margin-top:2px;"></i>
                        <div>
                            <div class="small fw-semibold">{{ $n->title }}</div>
                            <div class="small text-muted">{{ Str::limit($n->message,60) }}</div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center text-muted py-3 small">No new notifications.</div>
                    @endforelse
                    <a href="{{ route('customer.notifications.index') }}" class="btn btn-sm w-100 mt-2" style="border:1px solid var(--secondary);color:var(--secondary);">View All</a>
                </div>
            </div>
        </div>
    </div>

    {{-- Featured Rooms --}}
    @if($featured_rooms->count() > 0)
    <div class="mt-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-bold mb-0" style="color:var(--primary);">
                <i class="bi bi-building me-2"></i>Available Rooms
            </h5>
            <a href="{{ route('rooms') }}" class="btn btn-sm btn-outline-primary rounded-pill" target="_blank">
                View All <i class="bi bi-arrow-right ms-1"></i>
            </a>
        </div>
        <div class="row g-3">
            @foreach($featured_rooms as $room)
            <div class="col-md-6 col-lg-3">
                <div class="card border-0 shadow-sm h-100" style="border-radius:12px; overflow:hidden; transition:transform .2s;"
                     onmouseenter="this.style.transform='translateY(-4px)'"
                     onmouseleave="this.style.transform=''">

                    {{-- Room Photo --}}
                    <div style="height:140px; position:relative; overflow:hidden; cursor:{{ $room->image && file_exists(public_path($room->image)) ? 'zoom-in' : 'default' }};"
                         onclick="openLightbox('{{ $room->image && file_exists(public_path($room->image)) ? asset($room->image) : '' }}', 'Room {{ $room->room_number }} — {{ $room->room_type }}')">
                        @if($room->image && file_exists(public_path($room->image)))
                            <img src="{{ asset($room->image) }}"
                                 alt="Room {{ $room->room_number }}"
                                 style="width:100%; height:100%; object-fit:cover; transition:transform .4s;"
                                 onmouseenter="this.style.transform='scale(1.08)'"
                                 onmouseleave="this.style.transform='scale(1)'">
                            <div style="position:absolute; bottom:6px; right:6px; background:rgba(0,0,0,.55);
                                        color:#fff; border-radius:5px; padding:2px 7px; font-size:.68rem;">
                                <i class="bi bi-zoom-in me-1"></i>Zoom
                            </div>
                        @else
                            <div style="height:100%; background:linear-gradient(135deg,var(--primary),var(--secondary));
                                        display:flex; align-items:center; justify-content:center;">
                                <i class="bi bi-building text-white" style="font-size:3rem; opacity:.5;"></i>
                            </div>
                        @endif

                        {{-- Price overlay --}}
                        <div style="position:absolute; top:6px; right:6px;">
                            <span style="background:rgba(0,0,0,.6); color:#fff; border-radius:5px;
                                         padding:2px 8px; font-size:.75rem; font-weight:600;">
                                ₱{{ number_format($room->price_per_night,0) }}/night
                            </span>
                        </div>
                    </div>

                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="badge rounded-pill" style="background:var(--secondary);color:#fff;font-size:.7rem;">
                                {{ $room->room_type }}
                            </span>
                        </div>
                        <div class="fw-bold small mb-1" style="color:var(--primary);">Room {{ $room->room_number }}</div>
                        <div class="text-muted mb-2" style="font-size:.75rem;">
                            <i class="bi bi-people me-1"></i>{{ $room->capacity }} guests ·
                            <i class="bi bi-layers me-1"></i>Floor {{ $room->floor }}
                        </div>
                        <a href="{{ route('customer.bookings.create', ['room_id' => $room->id]) }}"
                           class="btn btn-sm w-100 fw-semibold text-white" style="background:var(--primary); border-radius:6px;">
                            <i class="bi bi-calendar-plus me-1"></i>Book Now
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>

{{-- Lightbox --}}
<div id="lightboxModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; z-index:9999;
     background:rgba(0,0,0,.92); align-items:center; justify-content:center; flex-direction:column;">
    <div style="position:relative; max-width:90vw; max-height:85vh;">
        <img id="lightboxImg" src="" alt=""
             style="max-width:90vw; max-height:80vh; object-fit:contain; border-radius:8px;
                    box-shadow:0 20px 60px rgba(0,0,0,.5); transition:transform .3s;">
        <div style="position:absolute; bottom:-50px; left:50%; transform:translateX(-50%); display:flex; gap:12px; align-items:center;">
            <button onclick="zoomOut()" class="btn btn-sm btn-outline-light rounded-circle" style="width:38px;height:38px;"><i class="bi bi-zoom-out"></i></button>
            <span id="zoomLevel" style="color:#fff;font-size:.85rem;min-width:45px;text-align:center;">100%</span>
            <button onclick="zoomIn()" class="btn btn-sm btn-outline-light rounded-circle" style="width:38px;height:38px;"><i class="bi bi-zoom-in"></i></button>
        </div>
    </div>
    <div id="lightboxCaption" style="color:rgba(255,255,255,.8);margin-top:60px;font-size:.9rem;text-align:center;"></div>
    <button onclick="closeLightbox()" style="position:fixed;top:20px;right:24px;background:none;border:none;color:#fff;font-size:2rem;cursor:pointer;">
        <i class="bi bi-x-circle-fill"></i>
    </button>
</div>

@endsection


@push('scripts')
@if(session('success'))
<script>
Swal.fire({icon:'success',title:'Success!',text:@json(session('success')),timer:2500,showConfirmButton:false,toast:true,position:'top-end'});
</script>
@endif
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
