@extends('layouts.master')

@section('title')
    Chi tiết học viên
@endsection

@section('content')
    <div class="container my-5">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-gradient-mix-shade  d-flex justify-content-between align-items-center">
                <h2 class="m-0 text-white">Chi tiết học viên</h2>
            </div>
            <div class="card-body p-4">
                <!-- Thông tin cơ bản -->
                <div class="mb-4">
                    <h4 class="border-bottom pb-2 mb-3">Thông tin cơ bản</h4>
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Tên</label>
                            <input type="text" class="form-control" value="{{ $user->name }}" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Email</label>
                            <input type="text" class="form-control" value="{{ $user->email }}" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Số điện thoại</label>
                            <input type="text" class="form-control" value="{{ $user->phone_number }}" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Vai trò</label>
                            <input type="text" class="form-control bg-success text-white text-center" value="Học viên" readonly>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">Ngày tạo</label>
                            <input type="text" class="form-control" value="{{ $user->created_at->format('d-m-Y H:i') }}"
                                readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Ngày cập nhật</label>
                            <input type="text" class="form-control" value="{{ $user->updated_at->format('d-m-Y H:i') }}"
                                readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Ảnh đại diện</label>
                            <div>
                                <img src="{{ $user->profile_picture ? asset('storage/' . $user->profile_picture) : asset('assets/images/avatar/avatar-3.jpg') }}"
                                    alt="avatar" class="rounded-circle border border-2 border-primary shadow-sm"
                                    style="width: 60px; height: 60px; object-fit: cover;" />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Khóa học đang học -->
                <div class="mb-4">
                    <h4 class="border-bottom pb-2 mb-3">Khóa học đang học</h4>
                    @if ($inProgressCourses->isEmpty())
                        <p class="text-muted">Không có khóa học nào đang học.</p>
                    @else
                        <ul class="list-group list-group-flush">
                            @foreach ($inProgressCourses as $course)
                                <li class="list-group-item">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <strong>{{ $course->course->title ?? 'Không rõ' }}</strong>
                                        <span class="badge bg-info px-2 py-1">Tiến độ:
                                            {{ $course->progress_percent }}%</span>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>

                <!-- Khóa học đã hoàn thành -->
                <div class="mb-4">
                    <h4 class="border-bottom pb-2 mb-3">Khóa học đã hoàn thành</h4>
                    @if ($completedCourses->isEmpty())
                        <p class="text-muted">Chưa hoàn thành khóa học nào.</p>
                    @else
                        <ul class="list-group list-group-flush">
                            @foreach ($completedCourses as $course)
                                <li class="list-group-item">
                                    <strong>{{ $course->course->title ?? 'Không rõ' }}</strong>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>

                <!-- Nút quay lại -->
                <div class="text-end mt-4">
                    <a href="{{ route('admin.students.index') }}" class="btn btn-secondary px-4">Quay lại</a>
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

        .list-group-item {
            border-left: 0;
            border-right: 0;
        }

        .list-group-item:first-child {
            border-top: 0;
        }

        .list-group-item:last-child {
            border-bottom: 0;
        }

        .badge {
            font-size: 0.9rem;
        }

        .img-fluid {
            transition: transform 0.3s ease;
        }

        .img-fluid:hover {
            transform: scale(1.02);
        }
    </style>
@endsection
