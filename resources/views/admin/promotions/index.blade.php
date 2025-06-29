@extends('admin.layouts.admin')

@section('title', 'จัดการกิจกรรม')

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
.stat-card {
    border-radius: 0.5rem;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    padding: 1.25rem 1rem;
    margin-bottom: 1.5rem;
    background: #fff;
    transition: box-shadow 0.2s;
}
.stat-card:hover {
    box-shadow: 0 4px 16px rgba(0,0,0,0.10);
}
.stat-icon {
    font-size: 2rem;
    margin-bottom: 0.5rem;
}
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">จัดการกิจกรรม</h1>
        <a href="{{ route('admin.promotions.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus"></i> สร้างกิจกรรมใหม่
        </a>
    </div>

    {{-- สรุป 4 กล่อง --}}
    <div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">กิจกรรมทั้งหมด</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total'] }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-list fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">กิจกรรมเปิดรับจอง</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['active'] }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-danger shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">กิจกรรมปิดรับจอง</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['inactive'] }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-times-circle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">กิจกรรมแนะนำ</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['featured'] }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-star fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
    {{-- จบ 4 กล่อง --}}

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
<form action="{{ route('admin.promotions.index') }}" method="GET" class="row g-3 align-items-center mb-3">
    <div class="col-auto">
        <select name="status" class="form-select" onchange="this.form.submit()">
            <option value="">ทุกสถานะ</option>
            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>เปิดรับจอง</option>
            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>ปิดรับจอง</option>
        </select>
    </div>
    <div class="col-auto">
        <select name="is_featured" class="form-select" onchange="this.form.submit()">
            <option value="">ทั้งหมด</option>
            <option value="1" {{ request('is_featured') === '1' ? 'selected' : '' }}>เฉพาะกิจกรรมแนะนำ</option>
            <option value="0" {{ request('is_featured') === '0' ? 'selected' : '' }}>ไม่ใช่กิจกรรมแนะนำ</option>
        </select>
    </div>
    <div class="col-auto">
        <div class="input-group">
            <input type="date" name="starts_at" class="form-control" value="{{ request('starts_at') }}">
            <span class="input-group-text">ถึง</span>
            <input type="date" name="ends_at" class="form-control" value="{{ request('ends_at') }}">
        </div>
    </div>
    <div class="col-auto">
        <div class="input-group">
            <input type="text" name="search" class="form-control" placeholder="ค้นหาชื่อกิจกรรม..." value="{{ request('search') }}">
            <button class="btn btn-primary" type="submit">
                <i class="fas fa-search"></i>
            </button>
        </div>
    </div>
</form>


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
                                    {{ $promotion->starts_at ? $promotion->starts_at->format('d/m/Y H:i') : '-' }}
                                    <br>ถึง<br>
                                    {{ $promotion->ends_at ? $promotion->ends_at->format('d/m/Y H:i') : '-' }}
                                </td>
                                <td>
                                    {{ $promotion->current_participants }}/{{ $promotion->max_participants }}
                                    <div class="progress" style="height: 5px;">
                                        <div class="progress-bar" role="progressbar" 
                                             style="width: {{ $promotion->max_participants > 0 ? ($promotion->current_participants / $promotion->max_participants) * 100 : 0 }}%">
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
                                <td colspan="9" class="text-center">ไม่พบข้อมูลกิจกรรม</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-center mt-3">
                {{ $promotions->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

