@extends('layouts.master')

@section('content')
    <div class="container mt-4">
        <div class="card bg-white shadow-sm border-0">
            <div class="card-header bg-gradient-mix-shade text-white d-flex justify-content-between align-items-center">
                <h2 class="mb-0 text-white ">Thêm Banner</h2>
            </div>
            <div class="card-body p-4">
                <!-- Thông báo thành công -->
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <!-- Form thêm banner -->
                <form action="{{ route('admin.banners.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row g-3">
                        <!-- Tiêu đề -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Tiêu đề</label>
                            <input type="text" name="title" value="{{ old('title') }}"
                                class="form-control @error('title') is-invalid @enderror"
                                placeholder="Nhập tiêu đề banner">
                            @error('title')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Trạng thái -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Trạng thái</label>
                            <select name="status" class="form-select @error('status') is-invalid @enderror">
                                <option value="1" {{ old('status') == '1' ? 'selected' : '' }}>Hoạt động</option>
                                <option value="0" {{ old('status') == '0' ? 'selected' : '' }}>Không hoạt động</option>
                            </select>
                            @error('status')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Mô tả -->
                        <div class="col-12">
                            <label class="form-label fw-bold">Mô tả</label>
                            <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="4"
                                placeholder="Nhập mô tả banner">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Hình ảnh -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Hình ảnh</label>
                            <input type="file" name="image" class="form-control @error('image') is-invalid @enderror">
                            @error('image')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Đường dẫn -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Đường dẫn (Link)</label>
                            <input type="url" name="link" value="{{ old('link') }}"
                                class="form-control @error('link') is-invalid @enderror"
                                placeholder="Nhập đường dẫn banner (nếu có)">
                            @error('link')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Nút hành động -->
                    <div class="mt-4 text-end">
                        <button type="submit" class="btn btn-success px-4 me-2">Thêm</button>
                        <a href="{{ route('admin.banners.index') }}" class="btn btn-secondary px-4">Quay lại</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('styles')
    <style>
        .card-header {
            border-radius: 0.5rem 0.5rem 0 0;
        }
        .form-label {
            margin-bottom: 0.3rem;
        }
        .alert-dismissible .btn-close {
            padding: 0.5rem 1rem;
        }
    </style>
@endsection