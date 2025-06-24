@extends('admin.layouts.admin')

@section('title', 'แก้ไขสินค้า')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">แก้ไขสินค้า: {{ $product->name }}</h1>
        <a href="{{ route('admin.products.index') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> กลับไปหน้ารายการ
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="name" class="form-label required">ชื่อสินค้า</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                               id="name" name="name" value="{{ old('name', $product->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="category_id" class="form-label required">หมวดหมู่</label>
                        <select class="form-select @error('category_id') is-invalid @enderror" 
                                id="category_id" name="category_id" required>
                            <option value="">เลือกหมวดหมู่</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" 
                                    {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="price" class="form-label required">ราคา</label>
                        <div class="input-group">
                            <span class="input-group-text">฿</span>
                            <input type="number" class="form-control @error('price') is-invalid @enderror" 
                                   id="price" name="price" value="{{ old('price', $product->price) }}" 
                                   step="0.01" min="0" required>
                        </div>
                        @error('price')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4 mb-3">
    <label for="discount_percent" class="form-label">ส่วนลด (%)</label>
    <div class="input-group">
        <input type="number" class="form-control @error('discount_percent') is-invalid @enderror"
               id="discount_percent" name="discount_percent"
               value="{{ old('discount_percent', $product->discount_percent) }}"
               min="0" max="100" step="0.01" placeholder="0">
        <span class="input-group-text">%</span>
    </div>
    @error('discount_percent')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>
                    <div class="col-md-4 mb-3">
                        <label for="stock" class="form-label required">จำนวนในคลัง</label>
                        <input type="number" class="form-control @error('stock') is-invalid @enderror" 
                               id="stock" name="stock" value="{{ old('stock', $product->stock) }}" 
                               min="0" required>
                        @error('stock')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="minimum_stock" class="form-label">จำนวนขั้นต่ำ</label>
                        <input type="number" class="form-control @error('minimum_stock') is-invalid @enderror" 
                               id="minimum_stock" name="minimum_stock" 
                               value="{{ old('minimum_stock', $product->minimum_stock) }}" min="0">
                        @error('minimum_stock')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="status" class="form-label required">สถานะ</label>
                        <select class="form-select @error('status') is-invalid @enderror" 
                                id="status" name="status" required>
                            <option value="available" {{ old('status', $product->status) == 'available' ? 'selected' : '' }}>
                                พร้อมขาย
                            </option>
                            <option value="unavailable" {{ old('status', $product->status) == 'unavailable' ? 'selected' : '' }}>
                                ไม่พร้อมขาย
                            </option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label d-block">ตัวเลือกเพิ่มเติม</label>
                        <div class="form-check form-check-inline">
    <input class="form-check-input" type="checkbox" 
           id="featured" name="featured" value="1" 
           {{ old('featured', $product->featured ?? false) ? 'checked' : '' }}>
    <label class="form-check-label" for="featured">
        แสดงในสินค้าแนะนำ
    </label>
</div>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">รายละเอียดสินค้า</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" 
                              id="description" name="description" rows="4">{{ old('description', $product->description) }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="image" class="form-label">รูปภาพสินค้า</label>
                    <input type="file" class="form-control @error('image') is-invalid @enderror" 
                           id="image" name="image" accept="image/*">
                    <small class="form-text text-muted">
                        ปล่อยว่างไว้หากไม่ต้องการเปลี่ยนรูปภาพ (รองรับไฟล์: JPG, PNG, GIF ขนาดไม่เกิน 2MB)
                    </small>
                    @error('image')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    @if($product->image)
                        <div id="currentImage" class="mt-2">
                            <p class="mb-2">รูปภาพปัจจุบัน:</p>
                            <img src="{{ $product->image_url }}" 
                                 alt="{{ $product->name }}" 
                                 class="img-thumbnail" 
                                 style="max-height: 200px;">
                        </div>
                    @endif
                    <div id="imagePreview" class="mt-2 d-none">
                        <p class="mb-2">ตัวอย่างรูปภาพใหม่:</p>
                        <img src="" alt="ตัวอย่าง" class="img-thumbnail" style="max-height: 200px;">
                    </div>
                </div>

                <hr>

                <div class="d-flex justify-content-between">
                    <button type="button" class="btn btn-danger" onclick="confirmDelete()">
                        <i class="fas fa-trash"></i> ลบสินค้า
                    </button>
                    <div class="d-flex gap-2">
                        <button type="reset" class="btn btn-secondary">
                            <i class="fas fa-redo"></i> คืนค่าเดิม
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> บันทึกการเปลี่ยนแปลง
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">ยืนยันการลบสินค้า</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>คุณแน่ใจหรือไม่ที่จะลบสินค้า "{{ $product->name }}"?</p>
                <p class="text-danger">การดำเนินการนี้ไม่สามารถยกเลิกได้</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                <form action="{{ route('admin.products.destroy', $product) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">ยืนยันการลบ</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .required:after {
        content: ' *';
        color: red;
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Image preview
    const imageInput = document.getElementById('image');
    const imagePreview = document.getElementById('imagePreview');
    
    imageInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        const reader = new FileReader();

        reader.onloadend = function() {
            imagePreview.querySelector('img').src = reader.result;
            imagePreview.classList.remove('d-none');
        }

        if (file) {
            reader.readAsDataURL(file);
        } else {
            imagePreview.classList.add('d-none');
        }
    });

    // Form reset confirmation
    document.querySelector('button[type="reset"]').addEventListener('click', function(e) {
        e.preventDefault();
        if(confirm('คุณแน่ใจหรือไม่ที่จะยกเลิกการแก้ไข?')) {
            this.form.reset();
            imagePreview.classList.add('d-none');
        }
    });
});

// Delete confirmation
function confirmDelete() {
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
}
</script>
@endpush