@extends('admin.layouts.admin')

@section('title', 'สร้างกิจกรรมใหม่')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">สร้างกิจกรรมใหม่</h1>
        <a href="{{ route('admin.promotions.index') }}" class="btn btn-sm btn-secondary">
            <i class="fas fa-arrow-left"></i> กลับไปรายการ
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <form action="{{ route('admin.promotions.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="mb-3">
                    <label for="title" class="form-label">ชื่อกิจกรรม</label>
                    <input type="text" class="form-control @error('title') is-invalid @enderror" 
                           id="title" name="title" value="{{ old('title') }}" required>
                    @error('title')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label for="description" class="form-label">รายละเอียดย่อ</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  id="description" name="description" rows="2">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label for="activity_details" class="form-label">รายละเอียดกิจกรรม</label>
                        <textarea class="form-control @error('activity_details') is-invalid @enderror" 
                                  id="activity_details" name="activity_details" rows="4">{{ old('activity_details') }}</textarea>
                        @error('activity_details')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="max_participants" class="form-label">จำนวนผู้เข้าร่วมสูงสุด</label>
                        <input type="number" class="form-control @error('max_participants') is-invalid @enderror" 
                               id="max_participants" name="max_participants" 
                               value="{{ old('max_participants') }}" min="1" required>
                        @error('max_participants')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="price_per_person" class="form-label">ราคาต่อคน</label>
                        <div class="input-group">
                            <span class="input-group-text">฿</span>
                            <input type="number" class="form-control @error('price_per_person') is-invalid @enderror" 
                                   id="price_per_person" name="price_per_person" 
                                   value="{{ old('price_per_person') }}" min="0" step="0.01" required>
                        </div>
                        @error('price_per_person')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="discount" class="form-label">ส่วนลด (%)</label>
                        <div class="input-group">
                            <input type="number" class="form-control @error('discount') is-invalid @enderror" 
                                   id="discount" name="discount" 
                                   value="{{ old('discount', 0) }}" 
                                   min="0" max="100" step="0.01">
                            <span class="input-group-text">%</span>
                        </div>
                        @error('discount')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="location" class="form-label">สถานที่จัดกิจกรรม</label>
                        <input type="text" class="form-control @error('location') is-invalid @enderror" 
                               id="location" name="location" 
                               value="{{ old('location') }}" required>
                        @error('location')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="starts_at" class="form-label">วันและเวลาเริ่มกิจกรรม</label>
                        <input type="datetime-local" class="form-control @error('starts_at') is-invalid @enderror" 
                               id="starts_at" name="starts_at" 
                               value="{{ old('starts_at') }}" required>
                        @error('starts_at')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="ends_at" class="form-label">วันและเวลาสิ้นสุดกิจกรรม</label>
                        <input type="datetime-local" class="form-control @error('ends_at') is-invalid @enderror" 
                               id="ends_at" name="ends_at" 
                               value="{{ old('ends_at') }}" required>
                        @error('ends_at')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label for="included_items" class="form-label">สิ่งที่ผู้เข้าร่วมจะได้รับ</label>
                    <textarea class="form-control @error('included_items') is-invalid @enderror" 
                              id="included_items" name="included_items" rows="3">{{ old('included_items') }}</textarea>
                    @error('included_items')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="image" class="form-label">รูปภาพกิจกรรม</label>
                    <input type="file" class="form-control @error('image') is-invalid @enderror" 
                           id="image" name="image" accept="image/*">
                    @error('image')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="status" class="form-label">สถานะ</label>
                    <select class="form-select @error('status') is-invalid @enderror" 
                            id="status" name="status" required>
                        <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>เปิดรับจอง</option>
                        <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>ปิดรับจอง</option>
                    </select>
                    @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
    <div class="form-check">
        <input class="form-check-input" type="checkbox" id="is_featured" name="is_featured"
            value="1" {{ old('is_featured', isset($promotion) ? $promotion->is_featured : false) ? 'checked' : '' }}>
        <label class="form-check-label" for="is_featured">
            โปรโมชั่นแนะนำ
        </label>
    </div>
</div>

                <button type="submit" class="btn btn-primary">สร้างกิจกรรม</button>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css" rel="stylesheet">
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>
<script>
$(document).ready(function() {
    $('#activity_details').summernote({
        height: 200,
        toolbar: [
            ['style', ['style']],
            ['font', ['bold', 'underline', 'clear']],
            ['color', ['color']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['table', ['table']],
            ['insert', ['link']],
            ['view', ['fullscreen', 'codeview', 'help']]
        ]
    });
});
</script>
@endpush