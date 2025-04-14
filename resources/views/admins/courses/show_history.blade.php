@extends('layouts.master')

@section('title')
    Lịch sử kiểm duyệt khóa học
@endsection

@section('content')
<div class="container my-5">
    <div class="card shadow-lg border-0 rounded-3 overflow-hidden">
        <div class="card-header bg-gradient-mix-shade text-white py-3">
            <h3 class="mb-0 fw-bold text-center">Chi tiết khóa học</h3>
        </div>
        <div class="card-body p-4">
            {{-- Thông tin cơ bản --}}
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <div class="bg-light p-3 rounded-3 shadow-sm h-100">
                        <label class="fw-bold text-muted mb-2 d-block">Tiêu đề</label>
                        <div class="fs-5 text-dark text-break">{{ $course->title }}</div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="bg-light p-3 rounded-3 shadow-sm h-100">
                        <label class="fw-bold text-muted mb-2 d-block">Danh mục</label>
                        <div class="fs-5 text-dark">{{ $course->category->name }}</div>
                    </div>
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <div class="bg-light p-3 rounded-3 shadow-sm h-100 d-flex align-items-center gap-3">
                        @if ($course->user->profile_picture)
                            <img src="{{ Storage::url($course->user->profile_picture) }}" alt="Ảnh giảng viên" class="rounded-circle shadow-sm" width="60" height="60">
                        @else
                            <img src="{{ asset('assets/avatarDefault.jpg') }}" alt="Ảnh mặc định" class="rounded-circle shadow-sm" width="60" height="60">
                        @endif
                        <div class="flex-grow-1">
                            <label class="fw-bold text-muted mb-2 d-block">Giảng viên</label>
                            <div class="fs-5 text-dark">{{ $course->user->name }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="bg-light p-3 rounded-3 shadow-sm h-100 d-flex align-items-center">
                        <div class="w-100">
                            <label class="fw-bold text-muted mb-2 d-block">Trạng thái</label>
                            <div class="fs-5 fw-bold 
                            @if ($approvalHistories->status == 'approved') text-success 
                            @elseif($approvalHistories->status == 'rejected') text-danger @endif">
                            @if ($approvalHistories->status == 'approved') Đã phê duyệt 
                            @elseif($approvalHistories->status == 'rejected') Đã từ chối @endif
                        </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <div class="bg-light p-3 rounded-3 shadow-sm h-100">
                        <label class="fw-bold text-muted mb-2 d-block">Mô tả</label>
                        <div class="text-dark lh-base" style="min-height: 80px;">{{ $course->description }}</div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="bg-light p-3 rounded-3 shadow-sm h-100">
                        <label class="fw-bold text-muted mb-2 d-block">Ngày tạo</label>
                        <div class="fs-5 text-dark">{{ $course->created_at->format('d/m/Y H:i') }}</div>
                    </div>
                </div>
            </div>

            {{-- Ảnh khóa học --}}
            <div class="mb-4">
                <label class="fw-bold text-muted mb-2 d-block">Ảnh khóa học</label>
                <div class="shadow-sm rounded-3 overflow-hidden w-100" style="max-width: 400px;">
                    <img src="{{ Storage::url($course->thumbnail) }}" alt="thumbnail" class="img-fluid w-100" style="object-fit: cover;">
                </div>
            </div>

         

            {{-- Nút quay lại --}}
            <div class="mt-4 text-center">
                <a href="{{ route('admin.courses.approval.history') }}" class="btn btn-outline-primary btn-lg rounded-pill px-4 shadow-sm">
                    <i class="bi bi-arrow-left-circle me-2"></i>Quay lại danh sách
                </a>
            </div>
        </div>
    </div>
</div>

<style>
    .transition-transform {
        transition: transform 0.2s ease-in-out;
    }
    .hover-scale:hover {
        transform: scale(1.03);
    }
    .card-body {
        background-color: #f8f9fa;
    }
    .bi {
        font-size: 1.2rem;
        vertical-align: middle;
    }
</style>
@endsection

