@extends('admin.layouts.admin')

@section('title', 'จัดการกิจกรรม')

@section('content')
@push('styles')
<style>
.img-thumbnail {
    cursor: pointer;
    transition: transform 0.2s;
}

.img-thumbnail:hover {
    transform: scale(1.1);
}

.promotion-image-container {
    position: relative;
    overflow: hidden;
    height: 50px;
    width: 50px;
}

.promotion-image-container img {
    object-fit: cover;
    width: 100%;
    height: 100%;
}
</style>
@endpush
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">จัดการกิจกรรม</h1>
        <a href="{{ route('admin.promotions.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus"></i> สร้างกิจกรรมใหม่
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">กิจกรรมทั้งหมด</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="promotionsTable">
                    <thead>
                        <tr>
                            <th>รูปภาพ</th>
                            <th>ชื่อกิจกรรม</th>
                            <th>วันที่จัด</th>
                            <th>ผู้เข้าร่วม</th>
                            <th>ราคา/คน</th>
                            <th>ส่วนลด</th>
                            <th>สถานะ</th>
                            <th>จัดการ</th>
                            <th>แนะนำ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($promotions as $promotion)
                            <tr>
<td>
                     @if($promotion->image && Storage::disk('public')->exists($promotion->image))
        <img src="{{ Storage::url($promotion->image) }}" 
             alt="{{ $promotion->title }}"
             class="img-thumbnail"
             style="max-height: 50px; width: auto;"
             onclick="window.open(this.src, '_blank')"
             title="คลิกเพื่อดูรูปขนาดเต็ม">
    @else
        <span class="text-muted">
            <i class="fas fa-image"></i> ไม่มีรูป
        </span>
    @endif
                    </td>
                                <td>{{ $promotion->title }}</td>
                                <td>
                                    {{ $promotion->starts_at->format('d/m/Y H:i') }}
                                    <br>ถึง<br>
                                    {{ $promotion->ends_at->format('d/m/Y H:i') }}
                                </td>
                                <td>
                                    {{ $promotion->current_participants }}/{{ $promotion->max_participants }}
                                    <div class="progress" style="height: 5px;">
                                        <div class="progress-bar" role="progressbar" 
                                             style="width: {{ ($promotion->current_participants / $promotion->max_participants) * 100 }}%">
                                        </div>
                                    </div>
                                </td>
                                <td>฿{{ number_format($promotion->price_per_person, 2) }}</td>
                                <td>{{ $promotion->discount }}%</td>
                                <td>
                                    <span class="badge bg-{{ $promotion->status === 'active' ? 'success' : 'danger' }}">
                                        {{ $promotion->status === 'active' ? 'เปิดรับจอง' : 'ปิดรับจอง' }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('admin.promotions.show', $promotion) }}" 
                                       class="btn btn-info btn-sm">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.promotions.edit', $promotion) }}" 
                                       class="btn btn-primary btn-sm">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.promotions.destroy', $promotion) }}" 
                                          method="POST" 
                                          class="d-inline"
                                          onsubmit="return confirm('ยืนยันการลบกิจกรรม?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                                <td>
    @if($promotion->is_featured)
        <span class="badge bg-success">แนะนำ</span>
    @else
        <span class="badge bg-secondary">-</span>
    @endif
</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">ไม่พบข้อมูลกิจกรรม</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#promotionsTable').DataTable({
        order: [[1, 'asc']],
        pageLength: 10,
        language: {
            url: '//cdn.datatables.net/plug-ins/1.10.24/i18n/Thai.json'
        }
    });
});
</script>
@endpush