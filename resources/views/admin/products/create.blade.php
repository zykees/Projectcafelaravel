@extends('admin.layouts.admin')

@section('title', 'เพิ่มสินค้าใหม่')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">เพิ่มสินค้าใหม่</h1>
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

            <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" id="productForm">
                @csrf

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="name" class="form-label required">ชื่อสินค้า</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                               id="name" name="name" value="{{ old('name') }}" required
                               placeholder="กรุณาระบุชื่อสินค้า">
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="category_id" class="form-label required">หมวดหมู่</label>
                        <div class="input-group">
                            <select class="form-select @error('category_id') is-invalid @enderror" 
                                    id="category_id" name="category_id" required>
                                <option value="">เลือกหมวดหมู่</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" 
                                        {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                                <i class="fas fa-plus"></i> เพิ่มหมวดหมู่
                            </button>
                        </div>
                        @error('category_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="row">
   
    <div class="col-md-4 mb-3">
        <label for="discount_percent" class="form-label">ส่วนลด (%)</label>
        <div class="input-group">
            <input type="number" class="form-control @error('discount_percent') is-invalid @enderror"
                   id="discount_percent" name="discount_percent"
                   value="{{ old('discount_percent', 0) }}"
                   min="0" max="100" step="0.01" placeholder="0">
            <span class="input-group-text">%</span>
        </div>
        @error('discount_percent')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
                <!-- Existing price, stock, status fields -->
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="price" class="form-label required">ราคา</label>
                        <div class="input-group">
                            <span class="input-group-text">฿</span>
                            <input type="number" class="form-control @error('price') is-invalid @enderror" 
                                   id="price" name="price" value="{{ old('price') }}" 
                                   step="0.01" min="0" required
                                   placeholder="0.00">
                        </div>
                        @error('price')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="stock" class="form-label required">จำนวนในคลัง</label>
                        <input type="number" class="form-control @error('stock') is-invalid @enderror" 
                               id="stock" name="stock" value="{{ old('stock', 0) }}" 
                               min="0" required>
                        @error('stock')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="minimum_stock" class="form-label">จำนวนขั้นต่ำ</label>
                        <input type="number" class="form-control @error('minimum_stock') is-invalid @enderror" 
                               id="minimum_stock" name="minimum_stock" value="{{ old('minimum_stock', 5) }}" 
                               min="0">
                        @error('minimum_stock')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Status and Featured fields -->
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="status" class="form-label required">สถานะ</label>
                        <select class="form-select @error('status') is-invalid @enderror" 
                                id="status" name="status" required>
                            <option value="available" {{ old('status') == 'available' ? 'selected' : '' }}>
                                พร้อมขาย
                            </option>
                            <option value="unavailable" {{ old('status') == 'unavailable' ? 'selected' : '' }}>
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

                <!-- Description field -->
                <div class="mb-3">
                    <label for="description" class="form-label">รายละเอียดสินค้า</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" 
                              id="description" name="description" rows="4"
                              placeholder="อธิบายรายละเอียดสินค้า...">{{ old('description') }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Image upload field -->
                <div class="mb-3">
                    <label for="image" class="form-label">รูปภาพสินค้า</label>
                    <input type="file" class="form-control @error('image') is-invalid @enderror" 
                           id="image" name="image" accept="image/*">
                    <small class="form-text text-muted">
                        รองรับไฟล์: JPG, PNG, GIF ขนาดไม่เกิน 2MB
                    </small>
                    @error('image')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <div id="imagePreview" class="mt-2 d-none">
                        <img src="" alt="ตัวอย่างรูปภาพ" class="img-thumbnail" style="max-height: 200px;">
                    </div>
                </div>

                <hr>

                <!-- Submit buttons -->
                <div class="d-flex justify-content-end gap-2">
                    <button type="reset" class="btn btn-secondary" id="resetBtn">
                        <i class="fas fa-redo"></i> ล้างฟอร์ม
                    </button>
                    <button type="submit" class="btn btn-primary" id="submitBtn">
                        <i class="fas fa-save"></i> บันทึกสินค้า
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Category Modal -->
<div class="modal fade" id="addCategoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">เพิ่มหมวดหมู่ใหม่</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="categoryForm">
                    @csrf
                    <div class="mb-3">
                        <label for="category_name" class="form-label required">ชื่อหมวดหมู่</label>
                        <input type="text" class="form-control" id="category_name" name="name" required>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="mb-3">
                        <label for="category_description" class="form-label">รายละเอียด</label>
                        <textarea class="form-control" id="category_description" name="description" rows="3"></textarea>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="mb-3">
                        <label for="category_status" class="form-label required">สถานะ</label>
                        <select class="form-select" id="category_status" name="status" required>
                            <option value="active">ใช้งาน</option>
                            <option value="inactive">ไม่ใช้งาน</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                <button type="button" class="btn btn-primary" id="saveCategoryBtn">
                    <span class="spinner-border spinner-border-sm d-none" role="status"></span>
                    บันทึกหมวดหมู่
                </button>
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
    .btn .spinner-border {
        margin-right: 0.5rem;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Image preview functionality
    const imageInput = document.getElementById('image');
    const imagePreview = document.getElementById('imagePreview');
    
    imageInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onloadend = function() {
                imagePreview.querySelector('img').src = reader.result;
                imagePreview.classList.remove('d-none');
            }
            reader.readAsDataURL(file);
        } else {
            imagePreview.classList.add('d-none');
        }
    });

    // Category management
    const categoryModal = document.getElementById('addCategoryModal');
    const categoryForm = document.getElementById('categoryForm');
    const saveCategoryBtn = document.getElementById('saveCategoryBtn');
    const spinner = saveCategoryBtn.querySelector('.spinner-border');

   async function saveCategory() {
    try {
        saveCategoryBtn.disabled = true;
        spinner.classList.remove('d-none');
        categoryForm.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        categoryForm.querySelectorAll('.invalid-feedback').forEach(el => el.textContent = '');

        const formData = new FormData(categoryForm);

        const response = await fetch('{{ route('admin.categories.store') }}', {
    method: 'POST',
    headers: {
        'X-CSRF-TOKEN': '{{ csrf_token() }}',
        'Accept': 'application/json'
    },
    body: formData
});

       let data;
try {
    data = await response.json();
    console.log(data); // ดูว่ามี category หรือไม่
} catch (e) {
    data = {};
}

        if (!response.ok) {
            if (response.status === 422 && data.errors) {
                Object.keys(data.errors).forEach(field => {
                    const input = document.getElementById(`category_${field}`);
                    if (input) {
                        input.classList.add('is-invalid');
                        input.nextElementSibling.textContent = data.errors[field][0];
                    }
                });
                return;
            }
            Swal.fire({
                icon: 'error',
                title: 'ข้อผิดพลาด!',
                text: data.message || 'เกิดข้อผิดพลาดในการบันทึกข้อมูล'
            });
            return;
        }

       // Add new category to select
if (data.category) {
    const categorySelect = document.getElementById('category_id');
    const option = document.createElement('option');
    option.value = data.category.id;
    option.text = data.category.name;
    option.selected = true;
    categorySelect.appendChild(option);
    categorySelect.value = data.category.id;
    categorySelect.dispatchEvent(new Event('change'));
}

        // Show success message in modal, then close modal after confirm
        Swal.fire({
            icon: 'success',
            title: 'เพิ่มหมวดหมู่สำเร็จ!',
            text: 'หมวดหมู่ใหม่ถูกเพิ่มและเลือกให้เรียบร้อยแล้ว',
            confirmButtonText: 'ตกลง'
        }).then(() => {
            const modal = bootstrap.Modal.getInstance(categoryModal);
            modal.hide();
            categoryForm.reset();
        });

    } catch (error) {
        Swal.fire({
            icon: 'error',
            title: 'ข้อผิดพลาด!',
            text: error.message || 'เกิดข้อผิดพลาด'
        });
    } finally {
        saveCategoryBtn.disabled = false;
        spinner.classList.add('d-none');
    }
}

    saveCategoryBtn.addEventListener('click', saveCategory);

    // Reset modal form when closed
    categoryModal.addEventListener('hidden.bs.modal', function() {
        categoryForm.reset();
        categoryForm.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        categoryForm.querySelectorAll('.invalid-feedback').forEach(el => el.textContent = '');
    });

    // Product form validation
    const productForm = document.getElementById('productForm');
    const submitBtn = document.getElementById('submitBtn');
    const resetBtn = document.getElementById('resetBtn');

    productForm.addEventListener('submit', function(e) {
        let isValid = true;
        this.querySelectorAll('[required]').forEach(field => {
            if (!field.value.trim()) {
                field.classList.add('is-invalid');
                isValid = false;
            } else {
                field.classList.remove('is-invalid');
            }
        });

        if (!isValid) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'กรุณาตรวจสอบข้อมูล',
                text: 'กรุณากรอกข้อมูลให้ครบถ้วน'
            });
        }
    });

    resetBtn.addEventListener('click', function(e) {
        e.preventDefault();
        Swal.fire({
            title: 'ยืนยันการล้างฟอร์ม',
            text: 'คุณแน่ใจหรือไม่ที่จะล้างข้อมูลทั้งหมด?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'ใช่, ล้างข้อมูล',
            cancelButtonText: 'ยกเลิก'
        }).then((result) => {
            if (result.isConfirmed) {
                productForm.reset();
                imagePreview.classList.add('d-none');
                productForm.querySelectorAll('.is-invalid').forEach(el => {
                    el.classList.remove('is-invalid');
                });
            }
        });
    });
});
</script>
@endpush