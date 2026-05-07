@extends('layouts.customer')
@section('title', 'My Notifications')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h2 class="fw-bold mb-0" style="color:var(--primary)"><i class="bi bi-bell me-2"></i>My Notifications</h2>
            <p class="text-muted mb-0">Stay updated with your booking activities</p>
        </div>
        <form method="POST" action="{{ route('customer.notifications.readAll') }}">
            @csrf
            <button type="submit" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-check-all me-1"></i> Mark All as Read
            </button>
        </form>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="card border-0 shadow-sm" style="border-radius:12px;">
        <div class="card-body p-0">
            @forelse($notifications as $n)
            <div class="d-flex align-items-start gap-3 p-4 border-bottom {{ !$n->is_read ? '' : 'opacity-75' }}"
                 style="{{ !$n->is_read ? 'background:#f8f9ff;' : '' }}">
                @php
                $icons  = ['success'=>'check-circle-fill','info'=>'info-circle-fill','warning'=>'exclamation-triangle-fill','danger'=>'x-circle-fill'];
                $colors = ['success'=>'#28a745','info'=>'#17a2b8','warning'=>'#ffc107','danger'=>'#dc3545'];
                @endphp
                <div class="flex-shrink-0 rounded-circle d-flex align-items-center justify-content-center"
                     style="width:44px;height:44px;background:{{ ($colors[$n->type] ?? 'var(--primary)') }}20;">
                    <i class="bi bi-{{ $icons[$n->type] ?? 'bell-fill' }}"
                       style="color:{{ $colors[$n->type] ?? 'var(--primary)' }};font-size:1.2rem;"></i>
                </div>
                <div class="flex-grow-1">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="fw-semibold {{ !$n->is_read ? '' : 'text-muted' }}">{{ $n->title }}</div>
                            <div class="small text-muted">{{ $n->message }}</div>
                            <div class="small text-muted mt-1">{{ $n->created_at->diffForHumans() }}</div>
                        </div>
                        <div class="ms-3 d-flex align-items-center gap-2 flex-shrink-0">
                            @if(!$n->is_read)
                            <span class="badge rounded-pill" style="background:var(--primary);color:#fff;">New</span>
                            <form method="POST" action="{{ route('customer.notifications.read', $n) }}">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-outline-success" title="Mark as Read">
                                    <i class="bi bi-check"></i>
                                </button>
                            </form>
                            @else
                            <span class="text-muted small"><i class="bi bi-check-all"></i> Read</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="text-center py-5 text-muted">
                <i class="bi bi-bell-slash" style="font-size:3rem;display:block;margin-bottom:8px;"></i>
                No notifications yet.
            </div>
            @endforelse
            @if($notifications->hasPages())
            <div class="d-flex justify-content-center py-3">{{ $notifications->links() }}</div>
            @endif
        </div>
    </div>
</div>
@endsection
