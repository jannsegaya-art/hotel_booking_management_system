@extends('layouts.admin')
@section('title', 'Room Management')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h2 class="fw-bold mb-0" style="color:var(--primary)"><i class="bi bi-building me-2"></i>Room Management</h2>
            <p class="text-muted mb-0">Manage hotel rooms and availability</p>
        </div>
        <a href="{{ route('admin.rooms.create') }}" class="btn text-white fw-semibold" style="background:var(--primary);">
            <i class="bi bi-plus-circle me-1"></i> Add Room
        </a>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    {{-- Summary Cards --}}
    <div class="row g-3 mb-4">
        @php
        $allRooms       = $rooms ?? \App\Models\Room::all();
        $availableCount = $allRooms->where('status','available')->count();
        $occupiedCount  = $allRooms->where('status','occupied')->count();
        $maintCount     = $allRooms->where('status','maintenance')->count();
        @endphp
        @foreach([
            ['total', 'Total Rooms', $allRooms->count(), 'building', 'var(--primary)'],
            ['available', 'Available', $availableCount, 'check-circle-fill', '#28a745'],
            ['occupied', 'Occupied', $occupiedCount, 'x-circle-fill', '#dc3545'],
            ['maintenance', 'Maintenance', $maintCount, 'tools', '#ffc107'],
        ] as $card)
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm text-center p-3" style="border-radius:12px;">
                <i class="bi bi-{{ $card[3] }} mb-2" style="font-size:2rem; color:{{ $card[4] }};"></i>
                <div class="fw-bold fs-3">{{ $card[2] }}</div>
                <div class="text-muted small">{{ $card[1] }}</div>
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
                            <th class="ps-4 py-3">Room #</th>
                            <th>Type</th>
                            <th>Floor</th>
                            <th>Capacity</th>
                            <th>Price/Night</th>
                            <th>Status</th>
                            <th>Rating</th>
                            <th class="pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rooms as $room)
                        <tr>
                            <td class="ps-4 fw-bold" style="color:var(--primary);">{{ $room->room_number }}</td>
                            <td><span class="badge" style="background:var(--secondary); color:#fff;">{{ $room->room_type }}</span></td>
                            <td>{{ $room->floor }}</td>
                            <td>{{ $room->capacity }} guests</td>
                            <td class="fw-semibold" style="color:var(--secondary);">₱ {{ number_format($room->price_per_night, 2) }}</td>
                            <td>
                                @php $sc = ['available'=>'success','occupied'=>'danger','maintenance'=>'warning text-dark']; @endphp
                                <span class="badge bg-{{ $sc[$room->status] ?? 'secondary' }}">{{ ucfirst($room->status) }}</span>
                            </td>
                            <td>
                                @if($room->ratings->count() > 0)
                                <span><i class="bi bi-star-fill me-1" style="color:var(--secondary);"></i>{{ number_format($room->average_rating, 1) }}</span>
                                @else
                                <span class="text-muted small">No ratings</span>
                                @endif
                            </td>
                            <td class="pe-4">
                                <div class="d-flex gap-1">
                                    <a href="{{ route('admin.rooms.edit', $room) }}" class="btn btn-sm btn-outline-warning">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <button class="btn btn-sm btn-outline-danger delete-btn"
                                            data-id="{{ $room->id }}" data-num="{{ $room->room_number }}">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                                <form id="delRoom-{{ $room->id }}" method="POST" action="{{ route('admin.rooms.destroy', $room) }}" class="d-none">
                                    @csrf @method('DELETE')
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="bi bi-building-x" style="font-size:3rem; display:block;"></i>No rooms found.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if(method_exists($rooms, 'hasPages') && $rooms->hasPages())
            <div class="d-flex justify-content-center py-3">{{ $rooms->links() }}</div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.querySelectorAll('.delete-btn').forEach(btn => {
    btn.addEventListener('click', function(){
        Swal.fire({ title:'Delete Room?', html:`Delete room <strong>${this.dataset.num}</strong>?`, icon:'warning',
           showCancelButton:true, confirmButtonColor:'#dc3545', confirmButtonText:'Yes, Delete'
        }).then(r => { if(r.isConfirmed) document.getElementById('delRoom-' + this.dataset.id).submit(); });
    });
});
</script>
@endpush
