@extends('layouts.master')

@section('title')
    Lịch sử kiểm duyệt khóa học
@endsection

@section('content')
    <div class="container my-5">
        <div class="card shadow-lg rounded-4 border-0">
            <div class="card-header bg-gradient-mix-shade text-white rounded-top-4">
                <h2 class="m-0">Chi tiết khóa học</h2>
            </div>
            <div class="card-body p-4">
                <div class="row mb-4">
                    <div class="col-md-8">
                        <table class="table table-borderless">
                            <tbody>
                                <tr>
                                    <th class="text-muted w-25">Tiêu đề:</th>
                                    <td>{{ $course->title }}</td>
                                </tr>
                                <tr>
                                    <th class="text-muted">Danh mục:</th>
                                    <td>{{ $course->category->name }}</td>
                                </tr>
                                <tr>
                                    <th class="text-muted">Giảng viên:</th>
                                    <td>{{ $course->user->name }}</td>
                                </tr>
                                @foreach ($approvalHistories as $history)
                                    <tr>
                                        <th class="text-muted">Lý do từ chối:</th>
                                        <td>{{ $history->comment ?? 'Không có' }}</td>
                                    </tr>
                                @endforeach
                                <tr>
                                    <th class="text-muted">Trạng thái:</th>
                                    <td>
                                        @if ($course->status == 'pending')
                                            <span class="badge bg-warning text-dark">Chờ duyệt</span>
                                        @elseif($course->status == 'published')
                                            <span class="badge bg-success">Đã phê duyệt</span>
                                        @elseif($course->status == 'draft')
                                            <span class="badge bg-danger">Đã từ chối</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th class="text-muted">Ngày tạo:</th>
                                    <td>{{ $course->created_at->format('d/m/Y H:i') }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-md-4 text-center">
                        <strong class="text-muted d-block mb-2">Ảnh đại diện:</strong>
                        <img src="{{ Storage::url($course->thumbnail) }}" alt="thumbnail"
                            class="img-thumbnail rounded shadow-sm" style="max-width: 200px;">
                    </div>
                </div>

                <div class="text-end">
                    <a href="{{ route('admin.courses.approval.history') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left-circle"></i> Quay lại
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
