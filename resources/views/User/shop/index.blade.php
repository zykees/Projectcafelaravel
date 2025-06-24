@extends('User.layouts.app')

@section('title', 'ร้านค้า')

@section('content')
<div class="container py-4">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3 mb-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-3">หมวดหมู่</h5>
                    <div class="list-group">
                        <a href="{{ route('user.shop.index') }}" 
                           class="list-group-item list-group-item-action {{ !request('category') ? 'active' : '' }}">
                            ทั้งหมด
                        </a>
                        @foreach($categories as $category)
                            <a href="{{ route('user.shop.index', ['category' => $category->id]) }}" 
                               class="list-group-item list-group-item-action {{ request('category') == $category->id ? 'active' : '' }}">
                                {{ $category->name }}
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Products Grid -->
        <div class="col-md-9">
            <!-- Search Bar -->
            <form action="{{ route('user.shop.index') }}" method="GET" class="mb-4">
                <div class="input-group">
                    <input type="text" name="search" class="form-control" 
                           placeholder="ค้นหาสินค้า..." value="{{ request('search') }}">
                    <button class="btn btn-primary" type="submit">
                        <i class="fas fa-search"></i> ค้นหา
                    </button>
                </div>
            </form>

            @if($products->isEmpty())
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>ไม่พบสินค้าที่ค้นหา
                </div>
            @else
                <div class="row row-cols-1 row-cols-md-3 g-4">
                    @foreach($products as $product)
                        <div class="col">
                            <div class="card h-100">
                                @if($product->image)
                                    <img src="{{ asset('storage/' . $product->image) }}" 
                                         class="card-img-top" alt="{{ $product->name }}">
                                @endif
                                <div class="card-body">
    <h5 class="card-title">{{ $product->name }}</h5>
    <p class="card-text text-muted">{{ Str::limit($product->description, 100) }}</p>
    <p class="card-text">
        @if($product->discount_percent > 0)
            <span class="h5 text-danger text-decoration-line-through">
                ฿{{ number_format($product->price, 2) }}
            </span>
            <span class="h5 text-success ms-2">
                ฿{{ number_format($product->price * (1 - $product->discount_percent/100), 2) }}
            </span>
            <span class="badge bg-success ms-1">
                -{{ $product->discount_percent }}%
            </span>
        @else
            <span class="h5 text-primary">
                ฿{{ number_format($product->price, 2) }}
            </span>
        @endif
        <br>
        <small class="text-muted">
            สินค้าคงเหลือ: 
            @if($product->stock > 0)
                <span class="text-success">{{ $product->stock }} ชิ้น</span>
            @else
                <span class="text-danger">สินค้าหมด</span>
            @endif
        </small>
    </p>
    <div class="d-flex gap-2">
        <a href="{{ route('user.shop.product', $product) }}" class="btn btn-outline-primary w-100">
            <i class="fas fa-eye me-1"></i> ดูรายละเอียด
        </a>
        <form action="{{ route('user.shop.add-to-cart', $product) }}" method="POST" class="w-100">
            @csrf
            <button type="submit" class="btn btn-primary w-100" {{ $product->stock <= 0 ? 'disabled' : '' }}>
                <i class="fas fa-cart-plus me-2"></i>
                @if($product->stock > 0)
                    เพิ่มลงตะกร้า
                @else
                    สินค้าหมด
                @endif
            </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="d-flex justify-content-center mt-4">
                    {{ $products->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection