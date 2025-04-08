@extends('layouts.master')

@section('title')
    Chi tiết giảng viên
@endsection

@section('content')
    <div class="container my-5">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-gradient-mix-shade  d-flex justify-content-between align-items-center">
                <h2 class="m-0 text-white">Chi tiết giảng viên</h2>
            </div>
            <div class="card-body p-4">
                <!-- Thông tin giảng viên -->
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
                            <input type="text" class="form-control bg-warning text-dark text-center" value="Giảng viên" readonly>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Ngày tạo</label>
                            <input type="text" class="form-control" value="{{ $user->created_at->format('d-m-Y H:i') }}" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Ngày cập nhật</label>
                            <input type="text" class="form-control" value="{{ $user->updated_at->format('d-m-Y H:i') }}" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Ảnh đại diện</label>
                            <div>
                                <img src="{{ $user->profile_picture ? asset('storage/' . $user->profile_picture) : asset('assets/images/avatar/avatar-3.jpg') }}"
                                     alt="avatar" class="rounded-circle border border-2 border-warning shadow-sm"
                                     style="width: 60px; height: 60px; object-fit: cover;" />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Thống kê -->
                <div class="mb-4">
                    <h4 class="border-bottom pb-2 mb-3">Thống kê</h4>
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Tổng số khóa học</label>
                            <input type="text" class="form-control" value="{{ $totalCourses }}" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Tổng số học viên</label>
                            <input type="text" class="form-control" value="{{ $totalStudents }}" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Tổng doanh thu</label>
                            <input type="text" class="form-control" value="{{ number_format($totalRevenue, 0, ',', '.') }} VNĐ" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Lợi nhuận giảng viên</label>
                            <input type="text" class="form-control" value="{{ number_format($totalLecturerEarning, 0, ',', '.') }} VNĐ" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Lợi nhuận hệ thống</label>
                            <input type="text" class="form-control" value="{{ number_format($totalAdminEarning, 0, ',', '.') }} VNĐ" readonly>
                        </div>
                    </div>
                </div>

                <!-- Danh sách khóa học -->
                <div class="mb-4">
                    <h4 class="border-bottom pb-2 mb-3">Danh sách khóa học</h4>
                    <div class="table-responsive">
                        <table class="table table-hover table-striped align-middle">
                            <thead class="bg-gradient-mix-shade ">
                                <tr>
                                    <th scope="col" class="text-white">Ảnh</th>
                                    <th scope="col" class="text-white">Tên khóa học</th>
                                    <th scope="col" class="text-white">Số học viên</th>
                                    <th scope="col" class="text-white">Doanh thu</th>
                                    <th scope="col" class="text-white">Lợi nhuận giảng viên</th>
                                    <th scope="col" class="text-white">Lợi nhuận hệ thống</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($courseRevenues as $course)
                                    <tr>
                                        <td>
                                            <img src="{{ $course['thumbnail'] ? asset('storage/' . $course['thumbnail']) : asset('assets/images/default-course.jpg') }}"
                                                 alt="course image" class="rounded shadow-sm"
                                                 style="width: 60px; height: 40px; object-fit: cover;">
                                        </td>
                                        <td>{{ $course['title'] }}</td>
                                        <td>{{ $course['enrollments_count'] }}</td>
                                        <td>{{ number_format($course['revenue'], 0, ',', '.') }} VNĐ</td>
                                        <td>{{ number_format($course['lecturer_earning'], 0, ',', '.') }} VNĐ</td>
                                        <td>{{ number_format($course['admin_earning'], 0, ',', '.') }} VNĐ</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-3">Không có khóa học nào.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                        <!-- Phân trang -->
                        @if ($courses->hasPages())
                            <div class="mt-3">
                                {{ $courses->links() }}
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Nút quay lại -->
                <div class="text-end mt-4">
                    <a href="{{ route('admin.lecturers.index') }}" class="btn btn-secondary px-4">Quay lại</a>
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
        .table th, .table td {
            vertical-align: middle;
        }
        .table-hover tbody tr:hover {
            background-color: #f8f9fa;
        }
        .img-fluid {
            transition: transform 0.3s ease;
        }
        .img-fluid:hover {
            transform: scale(1.02);
        }
    </style>
@endsection