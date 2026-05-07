@extends('layouts.public')
@section('title', 'Contact Us - Grand Royal Hotel')

@section('content')
<section style="background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%); padding: 80px 0 60px;">
    <div class="container text-center text-white">
        <h1 class="display-5 fw-bold mb-3" style="font-family:'Playfair Display',serif;">Contact Us</h1>
        <p class="lead mb-0 opacity-75">We'd love to hear from you. Reach out any time.</p>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="row g-5">
            {{-- Contact Form --}}
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm" style="border-radius:14px;">
                    <div class="card-body p-4 p-md-5">
                        <h4 class="fw-bold mb-1" style="color:var(--primary);">Send Us a Message</h4>
                        <p class="text-muted mb-4">Fill out the form and our team will get back to you within 24 hours.</p>

                        <form id="contactForm">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Your Name</label>
                                    <input type="text" class="form-control" placeholder="John" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Email Address</label>
                                    <input type="email" class="form-control" placeholder="you@email.com" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Phone Number</label>
                                    <input type="tel" class="form-control" placeholder="+0911 1111 111">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Subject</label>
                                    <select class="form-select">
                                        <option>General Inquiry</option>
                                        <option>Booking Assistance</option>
                                        <option>Special Request</option>
                                        <option>Feedback</option>
                                        <option>Other</option>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-semibold">Message</label>
                                    <textarea class="form-control" rows="5" placeholder="Write your message here..." required></textarea>
                                </div>
                                <div class="col-12">
                                    <button type="submit" class="btn py-3 px-5 fw-bold text-white w-100" style="background:var(--primary); border-radius:8px;">
                                        <i class="bi bi-send me-2"></i> Send Message
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Contact Info --}}
            <div class="col-lg-5">
                <h4 class="fw-bold mb-4" style="color:var(--primary);">Get in Touch</h4>

                @foreach([
                    ['bi-geo-alt-fill','Address','Dingle Iloilo City'],
                    ['bi-telephone-fill','Phone','+09111111111'],
                    ['bi-envelope-fill','Email','jannlenron@grandroyal.com'],
                    ['bi-clock-fill','Front Desk Hours','Open 24 Hours, 7 Days a Week'],
                ] as $info)
                <div class="d-flex gap-3 mb-4">
                    <div class="flex-shrink-0 rounded-circle d-flex align-items-center justify-content-center"
                         style="width:48px; height:48px; background:linear-gradient(135deg,var(--primary),var(--secondary));">
                        <i class="bi {{ $info[0] }} text-white"></i>
                    </div>
                    <div>
                        <div class="fw-bold" style="color:var(--primary);">{{ $info[1] }}</div>
                        <div class="text-muted">{{ $info[2] }}</div>
                    </div>
                </div>
                @endforeach

                <div class="mt-4">
                    <div class="fw-bold mb-2" style="color:var(--primary);">Follow Us</div>
                    <div class="d-flex gap-2">
                        @foreach(['facebook','twitter','instagram','linkedin'] as $social)
                        <a href="#" class="btn btn-sm rounded-circle d-flex align-items-center justify-content-center"
                           style="width:40px; height:40px; background:var(--primary); color:#fff;">
                            <i class="bi bi-{{ $social }}"></i>
                        </a>
                        @endforeach
                    </div>
                </div>

                <div class="card border-0 mt-4" style="background:linear-gradient(135deg,var(--primary),var(--secondary)); border-radius:14px;">
                    <div class="card-body p-4 text-white text-center">
                        <i class="bi bi-calendar-heart fs-1 mb-2 d-block"></i>
                        <h5 class="fw-bold">Ready to Book?</h5>
                        <p class="opacity-75 small mb-3">Experience luxury at its finest. Book your stay today.</p>
                        <a href="{{ route('rooms') }}" class="btn fw-semibold px-4"
                           style="background:#fff; color:var(--primary); border-radius:8px;">
                            View Rooms
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
document.getElementById('contactForm').addEventListener('submit', function(e){
    e.preventDefault();
    Swal.fire({
        icon: 'success',
        title: 'Message Sent!',
        text: 'Thank you for reaching out. We will get back to you within 24 hours.',
        confirmButtonColor: 'var(--primary)',
    });
    this.reset();
});
</script>
@endpush
