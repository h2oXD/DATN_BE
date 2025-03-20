@extends('layouts.master')

@section('content')
    <div class="container my-5">
        <div class="card shadow-lg border-0 rounded-4">
            <div class="card-header bg-success text-white d-flex justify-content-between align-items-center rounded-top-4">
                <h3 class="mb-0 d-flex align-items-center">
                    <i class="fe fe-plus-circle me-2"></i> Thêm danh mục
                </h3>
                <a href="{{ route('admin.categories.index') }}" class="btn btn-light btn-sm">Quay lại
                </a>
            </div>

            <div class="card-body p-4 bg-light rounded-bottom-4">
                {{-- Thông báo thành công --}}
                @if (session('success'))
                    <div class="alert alert-success d-flex align-items-center" role="alert">
                        <i class="fe fe-check-circle me-2"></i>
                        <div>{{ session('success') }}</div>
                    </div>
                @endif

                <form action="{{ route('admin.categories.store') }}" method="POST" class="needs-validation" novalidate>
                    @csrf

                    {{-- Tên danh mục --}}
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Tên danh mục <span class="text-danger">*</span></label>
                        <input type="text" name="name" value="{{ old('name') }}"
                            class="form-control @error('name') is-invalid @enderror" placeholder="Nhập tên danh mục">
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Danh mục cha --}}
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Danh mục cha</label>
                        <select name="parent_id" class="form-select @error('parent_id') is-invalid @enderror">
                            <option value="">-- Không có (danh mục gốc) --</option>
                            @foreach ($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                        @error('parent_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Nút hành động --}}
                    <div class="d-flex justify-content-end gap-2">
                        <button type="submit" class="btn btn-success px-4">
                            <i class="fe fe-save me-1"></i> Thêm
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
