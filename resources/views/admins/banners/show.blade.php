@extends('layouts.master')

@section('content')
    <div class="container my-5">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-gradient-mix-shade  d-flex justify-content-between align-items-center">
                <h2 class="m-0 text-white">Chi tiết Banner</h2>
            </div>
            <div class="card-body p-4">
                <div class="row g-4">
                    <!-- Tiêu đề -->
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Tiêu đề</label>
                        <input type="text" class="form-control" value="{{ $banner->title }}" readonly>
                    </div>

                    <!-- Trạng thái -->
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Trạng thái</label>
                        <input type="text"
                            class="form-control text-white  
                               @if ($banner->status) bg-success 
                               @else bg-danger @endif"
                            value="@if ($banner->status) Hoạt động 
                                     @else Không hoạt động @endif"
                            readonly>
                    </div>

                    <!-- Mô tả -->
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Mô tả</label>
                        <textarea class="form-control" rows="4" readonly>{{ trim($banner->description ?? 'Không có') }}</textarea>
                    </div>

                    <!-- Đường dẫn -->
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Đường dẫn</label>
                        <input type="text" class="form-control" value="{{ $banner->link ?? 'Không có' }}" readonly>
                    </div>

                    <!-- Hình ảnh -->
                    <div class="col-12">
                        <label class="form-label fw-bold">Hình ảnh</label>
                        <div>
                            <img src="{{ asset('storage/' . $banner->image) }}" alt="Banner Image"
                                class="img-fluid rounded shadow-sm" style="max-width: 500px; height: auto;" />
                        </div>
                    </div>
                </div>

                <!-- Nút quay lại -->
                <div class="mt-4 text-end">
                    <a href="{{ route('admin.banners.index') }}" class="btn btn-secondary px-4">Quay lại</a>
                </div>
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

        .form-control[readonly] {
            background-color: #f8f9fa;
            opacity: 1;
        }

        .img-fluid {
            transition: transform 0.3s ease;
        }

        .img-fluid:hover {
            transform: scale(1.02);
        }
    </style>
@endsection
