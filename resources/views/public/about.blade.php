@extends('layouts.public')
@section('title', 'About Us - Grand Royal Hotel')

@section('content')
{{-- Hero --}}
<section style="background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%); padding: 80px 0 60px;">
    <div class="container text-center text-white">
        <h1 class="display-5 fw-bold mb-3" style="font-family:'Playfair Display',serif;">About Grand Royal Hotel</h1>
        <p class="lead mb-0 opacity-75">A Legacy of Luxury & Exceptional Hospitality</p>
    </div>
</section>

{{-- Story --}}
<section class="py-5">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-6">
                <span class="badge mb-3 px-3 py-2" style="background:var(--secondary); color:#fff; border-radius:20px;">Our Story</span>
                <h2 class="fw-bold mb-3" style="font-family:'Playfair Display',serif; color:var(--primary);">
                    Where Every Guest Feels Like Royalty
                </h2>
                <p class="text-muted mb-3">
                    Founded in 1998, Grand Royal Hotel has been a beacon of elegance and comfort for over two decades.
                    Nestled in the heart of the city, our hotel combines timeless architecture with modern amenities
                    to create an unforgettable experience for every guest.
                </p>
                <p class="text-muted mb-4">
                    From our world-class dining to our meticulously designed rooms, we believe that every detail matters.
                    Our dedicated team of professionals works tirelessly to ensure that your stay exceeds expectations.
                </p>
                <div class="row g-3">
                    @foreach([['25+','Years of Excellence'],['50+','Luxury Rooms'],['10K+','Happy Guests'],['15+','Awards Won']] as $s)
                    <div class="col-6">
                        <div class="text-center p-3 rounded" style="background:#f8f9fc; border:1px solid #dee2e6;">
                            <div class="fw-bold" style="font-size:1.8rem; color:var(--primary);">{{ $s[0] }}</div>
                            <div class="small text-muted">{{ $s[1] }}</div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            <div class="col-lg-6">
                <div class="rounded-4 overflow-hidden shadow-lg" style="height:400px; background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%); display:flex; align-items:center; justify-content:center;">
                    <div class="text-center text-white p-4">
                        <i class="bi bi-building" style="font-size:6rem; opacity:0.8;"></i>
                        <h3 class="mt-3 fw-bold" style="font-family:'Playfair Display',serif;">Grand Royal Hotel</h3>
                        <p class="opacity-75">Est. 1998</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Values --}}
<section class="py-5" style="background:#f8f9fc;">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold" style="font-family:'Playfair Display',serif; color:var(--primary);">Our Core Values</h2>
            <p class="text-muted">The principles that guide everything we do</p>
        </div>
        <div class="row g-4">
            @foreach([
                ['bi-star-fill','Excellence','We strive for perfection in every aspect of our service, ensuring every stay is memorable.'],
                ['bi-heart-fill','Hospitality','Warm, genuine hospitality is at the heart of everything we do — making guests feel truly at home.'],
                ['bi-shield-fill','Integrity','We operate with transparency and honesty, building trust with every guest and partner.'],
                ['bi-gem','Luxury','Every detail is thoughtfully curated to provide an elevated, luxurious experience.'],
            ] as $v)
            <div class="col-md-6 col-lg-3">
                <div class="card border-0 shadow-sm h-100 text-center p-4" style="border-radius:12px;">
                    <div class="mx-auto mb-3 rounded-circle d-flex align-items-center justify-content-center"
                         style="width:64px; height:64px; background:linear-gradient(135deg,var(--primary),var(--secondary));">
                        <i class="bi {{ $v[0] }} text-white fs-4"></i>
                    </div>
                    <h5 class="fw-bold" style="color:var(--primary);">{{ $v[1] }}</h5>
                    <p class="text-muted small mb-0">{{ $v[2] }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- Team --}}
<section class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold" style="font-family:'Playfair Display',serif; color:var(--primary);">Meet Our Team</h2>
            <p class="text-muted">The dedicated professionals behind your exceptional experience</p>
        </div>
        <div class="row g-4 justify-content-center">
            @foreach([
                ['General Manager','Jann Lenron Segaya','Oversees all hotel operations with 15+ years of luxury hospitality experience.'],
                ['Head of Hospitality','Noemay Thyrn Francelizo','Ensures every guest receives personalized, five-star treatment.'],
                ['Executive Chef','Hannah Daras','Creates exquisite culinary experiences using locally sourced ingredients.'],
                ['Front Desk Manager','John Anthony Lozada & Rey Valen Cabayao','Leads our welcoming team to ensure smooth check-ins and guest satisfaction.'],
            ] as $t)
            <div class="col-sm-6 col-lg-3">
                <div class="card border-0 shadow-sm text-center p-4" style="border-radius:12px;">
                    <div class="mx-auto mb-3 rounded-circle d-flex align-items-center justify-content-center"
                         style="width:80px; height:80px; background:linear-gradient(135deg,var(--primary),var(--secondary));">
                        <i class="bi bi-person-fill text-white" style="font-size:2rem;"></i>
                    </div>
                    <h6 class="fw-bold mb-0" style="color:var(--primary);">{{ $t[1] }}</h6>
                    <small class="text-muted d-block mb-2" style="color:var(--secondary) !important;">{{ $t[0] }}</small>
                    <p class="text-muted small mb-0">{{ $t[2] }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endsection
