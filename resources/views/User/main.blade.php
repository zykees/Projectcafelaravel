{{-- filepath: resources/views/User/main.blade.php --}}
@extends('User.layouts.app')

@section('title', 'หน้าแรก')

@section('content')


<div class="container py-4">

    {{-- โปรโมชั่นแนะนำ --}}
    <div class="mb-5">
        <h2 class="mb-3"><i class="fas fa-bullhorn text-primary me-2"></i> โปรโมชั่นแนะนำ</h2>
        <div class="row">
            @forelse($promotions as $promotion)
                <div class="col-md-4 mb-4">
                    <div class="card h-100 shadow-sm">
                        @if($promotion->image_url)
                            <img src="{{ $promotion->image_url }}" class="card-img-top" alt="{{ $promotion->title }}">
                        @endif
                        <div class="card-body">
                            <h5 class="card-title">{{ $promotion->title }}</h5>
                            <div class="mb-2 text-muted small">
                                {{ $promotion->starts_at ? $promotion->starts_at->format('d/m/Y') : '' }} - 
                                {{ $promotion->ends_at ? $promotion->ends_at->format('d/m/Y') : '' }}
                            </div>
                            <div class="mb-2">
                                ส่วนลด: {{ $promotion->discount }}%
                            </div>
                            <a href="{{ route('user.promotions.show', $promotion->id) }}" class="btn btn-primary btn-sm">
                                ดูรายละเอียด
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-muted">ยังไม่มีโปรโมชั่นแนะนำ</div>
            @endforelse
        </div>
    </div>

    {{-- สินค้าแนะนำ --}}
    <div class="mb-5">
        <h2 class="mb-3"><i class="fas fa-star text-warning me-2"></i> สินค้าแนะนำ</h2>
        <div class="row">
            @forelse($products as $product)
                <div class="col-md-3 mb-4">
                    <div class="card h-100 shadow-sm">
                        @if($product->image_url)
                            <img src="{{ $product->image_url }}" class="card-img-top" alt="{{ $product->name }}">
                        @endif
                        <div class="card-body">
                            <h5 class="card-title">{{ $product->name }}</h5>
                            <div class="mb-2 text-muted small">
                                {{ $product->category->name ?? '' }}
                            </div>
                            <div class="mb-2">
    @if($product->discount_percent > 0)
        <span class="fw-bold text-danger text-decoration-line-through">
            ฿{{ number_format($product->price, 2) }}
        </span>
        <span class="fw-bold text-success ms-2">
            ฿{{ number_format($product->price * (1 - $product->discount_percent/100), 2) }}
        </span>
        <span class="badge bg-success ms-2">
            -{{ $product->discount_percent }}%
        </span>
    @else
        <span class="fw-bold text-success">
            ฿{{ number_format($product->price, 2) }}
        </span>
    @endif
</div>
                            <a href="{{ route('user.shop.product', $product->id) }}" class="btn btn-outline-primary btn-sm">
                                ดูรายละเอียด
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-muted">ยังไม่มีสินค้าแนะนำ</div>
            @endforelse
        </div>
    </div>

</div>
@endsection