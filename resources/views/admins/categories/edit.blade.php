@extends('layouts.master')

@section('content')
    <div class="container my-5">
        <div class="card shadow-lg border-0 rounded-4">
            <div class="card-header bg-gradient-mix-shade d-flex justify-content-between align-items-center rounded-top-4">
                <h3 class="mb-0 d-flex align-items-center text-white ">
                    Chỉnh sửa danh mục
                </h3>
                <a href="{{ route('admin.categories.index') }}" class="btn btn-light btn-sm">Quay lại
                </a>
            </div>

            <div class="card-body p-4 bg-light rounded-bottom-4">
                <form action="{{ route('admin.categories.update', $category->id) }}" method="POST" class="needs-validation"
                    novalidate>
                    @csrf
                    @method('PUT')

                    {{-- Tên danh mục --}}
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Tên danh mục <span class="text-danger">*</span></label>
                        <input type="text" name="name" value="{{ old('name', $category->name) }}"
                            class="form-control @error('name') is-invalid @enderror" placeholder="Nhập tên danh mục">
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Danh mục cha --}}
                    @if ($category->parent_id !== null)
                        <div class="mb-4">
                            <label for="parent_id" class="form-label fw-semibold">Danh mục cha</label>
                            <select name="parent_id" id="parent_id"
                                class="form-select @error('parent_id') is-invalid @enderror">
                                <option value="">-- Không có --</option>
                                @foreach ($categories as $cat)
                                    <option value="{{ $cat->id }}"
                                        {{ old('parent_id', $category->parent_id) == $cat->id ? 'selected' : '' }}>
                                        {{ $cat->name }}
                                    </option>
                                @endforeach
                            </select>
                        
                        </div>
                    @endif

                    {{-- Nút hành động --}}
                    <div class="d-flex justify-content-end gap-2">
                        <button type="submit" class="btn btn-warning px-4">
                            <i class="fe fe-save me-1"></i> Cập nhật
                        </button>
                        <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary px-4">
                            <i class="fe fe-x me-1"></i> Hủy
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
