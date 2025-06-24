@extends('User.layouts.app')

@section('title', 'โปรโมชั่น')

@section('content')
@push('styles')
<style>
.card-img-top {
    height: 200px;
    object-fit: cover;
    border-top-left-radius: 0.5rem;
    border-top-right-radius: 0.5rem;
}
.promotion-price-overlay {
    position: absolute;
    left: 0;
    bottom: 0;
    width: 100%;
    background: rgba(255,255,255,0.92);
    padding: 0.5rem 1rem;
    border-bottom-left-radius: 0.5rem;
    border-bottom-right-radius: 0.5rem;
    text-align: center;
}
.price-old {
    text-decoration: line-through;
    color: #dc3545;
    font-size: 1.1rem;
    margin-right: 0.5rem;
}
.price-new {
    color: #198754;
    font-size: 1.3rem;
    font-weight: bold;
}
.price-normal {
    color: #0d6efd;
    font-size: 1.2rem;
    font-weight: bold;
}
</style>
@endpush

<div class="container py-4">
    <h2 class="mb-4">โปรโมชั่น</h2>

    <!-- ตัวกรองโปรโมชั่น -->
    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('user.promotions.index') }}" method="GET" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">ค้นหา</label>
                    <input type="text" name="search" class="form-control" 
                           value="{{ request('search') }}" 
                           placeholder="ชื่อโปรโมชั่น...">
                </div>
                <div class="col-md-3">
                    <label class="form-label">ประเภท</label>
                    <select name="type" class="form-select">
                        <option value="">ทั้งหมด</option>
                        <option value="discount" {{ request('type') == 'discount' ? 'selected' : '' }}>
                            ส่วนลด
                        </option>
                        <option value="special" {{ request('type') == 'special' ? 'selected' : '' }}>
                            สิทธิพิเศษ
                        </option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">เรียงตาม</label>
                    <select name="sort" class="form-select">
                        <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>
                            ล่าสุด
                        </option>
                        <option value="popular" {{ request('sort') == 'popular' ? 'selected' : '' }}>
                            ยอดนิยม
                        </option>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search me-2"></i>ค้นหา
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- แสดงรายการโปรโมชั่น -->
    @if($promotions->isEmpty())
        <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i>ไม่พบโปรโมชั่นที่ค้นหา
        </div>
    @else
        <div class="row">
            @foreach($promotions as $promotion)
                <div class="col-md-4 mb-4">
                    <div class="card h-100 position-relative">
                        <div class="position-relative">
                            @if($promotion->image)
                                <img src="{{ asset('storage/' . $promotion->image) }}" 
                                     class="card-img-top"
                                     alt="{{ $promotion->title }}">
                            @else
                                <div class="card-img-top bg-light d-flex align-items-center justify-content-center" 
                                     style="height: 200px;">
                                    <i class="fas fa-image fa-3x text-muted"></i>
                                </div>
                            @endif
                            <!-- ราคาบนรูป -->
                            <div class="promotion-price-overlay">
                                @php
                                    $price = $promotion->price_per_person;
                                    $discount = $promotion->discount ?? 0;
                                    $priceAfterDiscount = $discount > 0
                                        ? round($price * (1 - $discount/100), 2)
                                        : $price;
                                @endphp
                                @if($discount > 0)
                                    <span class="price-old">฿{{ number_format($price, 2) }}</span>
                                    <span class="price-new">฿{{ number_format($priceAfterDiscount, 2) }}</span>
                                    <span class="text-success small ms-1">/ คน</span>
                                @else
                                    <span class="price-normal">฿{{ number_format($price, 2) }}</span>
                                    <span class="text-muted small ms-1">/ คน</span>
                                @endif
                            </div>
                        </div>
                        <div class="card-body">
                            <h5 class="card-title">{{ $promotion->title }}</h5>
                            <p class="card-text">{{ Str::limit($promotion->description, 100) }}</p>
                            
                            <div class="mb-3">
                                <small class="text-muted">
                                    <i class="far fa-calendar-alt me-1"></i>
                                    ระยะเวลา: {{ $promotion->starts_at->format('d/m/Y') }} 
                                    - {{ $promotion->ends_at->format('d/m/Y') }}
                                </small>
                            </div>

                            @if($promotion->isExpired())
                                <div class="alert alert-danger">
                                    <i class="fas fa-exclamation-circle me-2"></i>หมดเขตแล้ว
                                </div>
                            @elseif(!$promotion->hasAvailableSlots())
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle me-2"></i>เต็มแล้ว
                                </div>
                            @else
                                <a href="{{ route('user.promotions.show', $promotion) }}" 
                                   class="btn btn-primary">
                                    ดูรายละเอียด
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- แสดง pagination -->
        <div class="d-flex justify-content-center mt-4">
            {{ $promotions->links() }}
        </div>
    @endif
</div>
@endsection