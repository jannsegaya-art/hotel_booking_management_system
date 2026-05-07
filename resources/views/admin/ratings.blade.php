@extends('layouts.admin')
@section('title', 'Ratings & Feedback')

@section('content')
<div class="container-fluid py-4">
    <div class="mb-4">
        <h2 class="fw-bold mb-0" style="color:var(--primary)"><i class="bi bi-star me-2"></i>Ratings & Feedback</h2>
        <p class="text-muted mb-0">Monitor guest ratings and feedback for all rooms</p>
    </div>

    {{-- Filter --}}
    <div class="card border-0 shadow-sm mb-4" style="border-radius:12px;">
        <div class="card-body p-3">
            <form method="GET" class="row g-2 align-items-end">
                <div class="col-md-4">
                    <label class="form-label small fw-semibold">Room</label>
                    <select name="room_id" class="form-select form-select-sm">
                        <option value="">All Rooms</option>
                        @foreach($rooms as $room)
                        <option value="{{ $room->id }}" {{ request('room_id') == $room->id ? 'selected':'' }}>
                            Room {{ $room->room_number }} ({{ $room->room_type }})
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-semibold">Rating</label>
                    <select name="rating" class="form-select form-select-sm">
                        <option value="">All Ratings</option>
                        @for($i=5; $i>=1; $i--)
                        <option value="{{ $i }}" {{ request('rating') == $i ? 'selected':'' }}>
                            {{ $i }} Star{{ $i>1?'s':'' }}
                        </option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-sm w-100 text-white" style="background:var(--primary);">
                        <i class="bi bi-filter me-1"></i> Filter
                    </button>
                </div>
                <div class="col-md-2">
                    <a href="{{ route('admin.ratings') }}" class="btn btn-sm btn-outline-secondary w-100">Clear</a>
                </div>
            </form>
        </div>
    </div>

    {{-- Average Rating per Room --}}
    <div class="row g-3 mb-4">
        @foreach($rooms->take(4) as $room)
        @php $avg = $room->ratings->avg('rating') ?? 0; $count = $room->ratings->count(); @endphp
        <div class="col-md-3">
            <div class="card border-0 shadow-sm text-center p-3" style="border-radius:12px;">
                <div class="fw-bold" style="color:var(--primary);">Room {{ $room->room_number }}</div>
                <div class="small text-muted mb-2">{{ $room->room_type }}</div>
                <div class="d-flex justify-content-center gap-1 mb-1">
                    @for($i=1;$i<=5;$i++)
                    <i class="bi bi-star{{ $i<=$avg?'-fill':'' }}" style="color:var(--secondary);"></i>
                    @endfor
                </div>
                <div class="fw-bold">{{ number_format($avg,1) }}/5</div>
                <div class="text-muted small">{{ $count }} review(s)</div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="card border-0 shadow-sm" style="border-radius:12px;">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead style="background:#f8f9fc;">
                        <tr>
                            <th class="ps-4 py-3">Guest</th>
                            <th>Room</th>
                            <th>Rating</th>
                            <th>Comment</th>
                            <th class="pe-4">Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($ratings as $rating)
                        <tr>
                            <td class="ps-4">
                                <div class="fw-semibold">{{ $rating->user->name }}</div>
                                <div class="small text-muted">{{ $rating->user->email }}</div>
                            </td>
                            <td>
                                <span class="fw-semibold" style="color:var(--primary);">Room {{ $rating->room->room_number }}</span>
                                <div class="small text-muted">{{ $rating->room->room_type }}</div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-1">
                                    @for($i=1;$i<=5;$i++)
                                    <i class="bi bi-star{{ $i<=$rating->rating?'-fill':'' }}" style="color:var(--secondary);"></i>
                                    @endfor
                                    <span class="ms-1 fw-bold">{{ $rating->rating }}</span>
                                </div>
                            </td>
                            <td>
                                @if($rating->comment)
                                <span class="small">"{{ Str::limit($rating->comment, 80) }}"</span>
                                @else
                                <span class="text-muted small">No comment</span>
                                @endif
                            </td>
                            <td class="pe-4 text-muted small">{{ $rating->created_at->format('M d, Y') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="bi bi-star-half" style="font-size:3rem;display:block;"></i>No ratings yet.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($ratings->hasPages())
            <div class="d-flex justify-content-center py-3">{{ $ratings->appends(request()->query())->links() }}</div>
            @endif
        </div>
    </div>
</div>
@endsection
