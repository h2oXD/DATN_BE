@extends('layouts.master')

@section('title')
    Chi tiết khóa học
@endsection

@section('content')
<div class="container my-5">
    <div class="card shadow-lg border-0 rounded-3 overflow-hidden">
        <div class="card-header bg-primary text-white py-3">
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
                                @if ($course->status == 'pending') text-warning 
                                @elseif($course->status == 'published') text-success 
                                @elseif($course->status == 'rejected') text-danger @endif">
                                @if ($course->status == 'pending') Chờ duyệt 
                                @elseif($course->status == 'published') Đã phê duyệt 
                                @elseif($course->status == 'rejected') Đã từ chối @endif
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

            {{-- Thống kê khóa học --}}
            <div class="row g-3 mb-5">
                <div class="col-md-4 col-sm-6">
                    <div class="bg-light p-4 rounded-3 shadow-sm text-center h-100 transition-transform hover-scale">
                        <div class="h5 mb-2 text-muted"><i class="bi bi-people-fill me-2"></i>Học viên</div>
                        <div class="fs-2 fw-bold text-primary">{{ $totalStudents }}</div>
                    </div>
                </div>
                <div class="col-md-4 col-sm-6">
                    <div class="bg-light p-4 rounded-3 shadow-sm text-center h-100 transition-transform hover-scale">
                        <div class="h5 mb-2 text-muted"><i class="bi bi-currency-exchange me-2"></i>Doanh thu</div>
                        <div class="fs-2 fw-bold text-success">{{ number_format($totalRevenue) }} VNĐ</div>
                    </div>
                </div>
                <div class="col-md-4 col-sm-12">
                    <div class="bg-light p-4 rounded-3 shadow-sm text-center h-100 transition-transform hover-scale">
                        <div class="h5 mb-2 text-muted"><i class="bi bi-star-fill me-2"></i>Đánh giá TB</div>
                        <div class="fs-2 fw-bold text-warning">
                            {{ number_format($averageRating, 1) }}/5 
                            <div class="text-muted fs-6 mt-1">({{ $totalReviews }} đánh giá)</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Nội dung khóa học --}}
            <div class="row g-3 mb-5 justify-content-center">
                @foreach ([
                    ['title' => 'Phần học', 'value' => $totalSections, 'icon' => 'bi-book-fill'],
                    ['title' => 'Bài học', 'value' => $totalLessons, 'icon' => 'bi-journal-text'],
                    ['title' => 'Video', 'value' => $totalVideos, 'icon' => 'bi-play-btn-fill'],
                    ['title' => 'Tài liệu', 'value' => $totalDocuments, 'icon' => 'bi-file-earmark-text-fill'],
                    ['title' => 'Coding', 'value' => $totalCodings, 'icon' => 'bi-code-slash']
                ] as $item)
                    <div class="col-md-2 col-sm-4 col-6">
                        <div class="bg-white p-3 rounded-3 shadow-sm text-center transition-transform hover-scale h-100">
                            <div class="text-muted mb-1 fw-medium"><i class="bi {{ $item['icon'] }} me-2"></i>{{ $item['title'] }}</div>
                            <div class="fs-4 fw-bold text-dark">{{ $item['value'] }}</div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Nút quay lại --}}
            <div class="mt-4 text-center">
                <a href="{{ route('admin.courses.index') }}" class="btn btn-outline-primary btn-lg rounded-pill px-4 shadow-sm">
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