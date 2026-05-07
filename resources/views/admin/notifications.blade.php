@extends('layouts.admin')
@section('title','Notifications')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h2 class="fw-bold mb-0" style="color:var(--primary)"><i class="bi bi-bell me-2"></i>Notifications</h2>
            <p class="text-muted mb-0">Manage and send notifications</p>
        </div>
        <form method="POST" action="{{ route('admin.notifications.readAll') }}">
            @csrf
            <button type="submit" class="btn btn-outline-primary btn-sm">
                <i class="bi bi-check-all me-1"></i> Mark All Read
            </button>
        </form>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="row g-4">
        {{-- Send Notification Panel --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm" style="border-radius:12px;">
                <div class="card-header py-3" style="background:var(--secondary); border-radius:12px 12px 0 0;">
                    <h5 class="text-white mb-0"><i class="bi bi-send me-2"></i>Send Notification</h5>
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('admin.notifications.send') }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Recipient</label>
                            <select name="user_id" class="form-select" required>
                                <option value="all">📢 All Users</option>
                                @foreach(\App\Models\User::orderBy('name')->get() as $u)
                                <option value="{{ $u->id }}">{{ $u->name }} ({{ ucfirst($u->role) }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Type</label>
                            <select name="type" class="form-select">
                                <option value="info">ℹ️ Info</option>
                                <option value="success">✅ Success</option>
                                <option value="warning">⚠️ Warning</option>
                                <option value="danger">❌ Danger</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Title</label>
                            <input type="text" name="title" class="form-control" placeholder="Notification title" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Message</label>
                            <textarea name="message" class="form-control" rows="4" placeholder="Write your message..." required></textarea>
                        </div>
                        <button type="submit" class="btn w-100 fw-bold text-white" style="background:var(--primary);">
                            <i class="bi bi-send me-2"></i> Send Notification
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Notifications List --}}
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm" style="border-radius:12px;">
                <div class="card-header py-3" style="background:var(--primary); border-radius:12px 12px 0 0;">
                    <h5 class="text-white mb-0"><i class="bi bi-bell me-2"></i>My Notifications</h5>
                </div>
                <div class="card-body p-0">
                    @forelse($notifications as $notif)
                    <div class="d-flex align-items-start gap-3 p-3 border-bottom {{ $notif->is_read ? '' : 'bg-light' }}">
                        <div class="flex-shrink-0 rounded-circle d-flex align-items-center justify-content-center"
                             style="width:40px;height:40px;background:{{ ['info'=>'#0dcaf0','success'=>'#198754','warning'=>'#ffc107','danger'=>'#dc3545'][$notif->type] ?? '#0dcaf0' }};">
                            <i class="bi bi-{{ ['info'=>'info-circle','success'=>'check-circle','warning'=>'exclamation-triangle','danger'=>'x-circle'][$notif->type] ?? 'bell' }} text-white"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="fw-semibold {{ $notif->is_read ? 'text-muted' : '' }}">{{ $notif->title }}</div>
                            <div class="text-muted small">{{ $notif->message }}</div>
                            <div class="text-muted" style="font-size:.72rem;">{{ $notif->created_at->diffForHumans() }}</div>
                        </div>
                        <div class="d-flex flex-column gap-1">
                            @if(!$notif->is_read)
                            <form method="POST" action="{{ route('admin.notifications.read', $notif) }}">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-outline-success" title="Mark Read">
                                    <i class="bi bi-check"></i>
                                </button>
                            </form>
                            @endif
                            <form method="POST" action="{{ route('admin.notifications.destroy', $notif) }}">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-bell-slash" style="font-size:3rem; display:block; margin-bottom:8px;"></i>
                        No notifications yet.
                    </div>
                    @endforelse
                </div>
                @if($notifications->hasPages())
                <div class="d-flex justify-content-center py-3">{{ $notifications->links() }}</div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
