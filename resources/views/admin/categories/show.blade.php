@extends('admin.layouts.admin')

@section('title', 'รายละเอียดหมวดหมู่')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">รายละเอียดหมวดหมู่: {{ $category->name }}</h1>
        <div>
            <a href="{{ route('admin.categories.edit', $category) }}" class="btn btn-primary btn-sm">
                <i class="fas fa-edit"></i> แก้ไข
            </a>
            <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> กลับ
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">ข้อมูลหมวดหมู่</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <tr>
                                <th style="width: 200px;">รหัสหมวดหมู่</th>
                                <td>{{ $category->id }}</td>
                            </tr>
                            <tr>
                                <th>ชื่อหมวดหมู่</th>
                                <td>{{ $category->name }}</td>
                            </tr>
                            <tr>
                                <th>Slug</th>
                                <td>{{ $category->slug }}</td>
                            </tr>
                            <tr>
                                <th>รายละเอียด</th>
                                <td>{{ $category->description ?: 'ไม่มีรายละเอียด' }}</td>
                            </tr>
                            <tr>
                                <th>สถานะ</th>
                                <td>
                                    <span class="badge bg-{{ $category->status === 'active' ? 'success' : 'danger' }}">
                                        {{ $category->status === 'active' ? 'ใช้งาน' : 'ไม่ใช้งาน' }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <th>จำนวนสินค้า</th>
                                <td>{{ $category->products_count }} รายการ</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection