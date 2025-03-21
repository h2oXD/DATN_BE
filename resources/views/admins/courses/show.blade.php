@extends('layouts.master')

@section('title')
    Chi tiết khóa học
@endsection

@section('content')
    <div class="container my-5">
        <div class="card">
            <div class="card-header">
                <h2 class="m-0">Chi tiết khóa học</h2>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <strong>Tiêu đề:</strong> {{ $course->title }}
                </div>
                <div class="mb-3">
                    <strong>Mô tả:</strong> {{ $course->description }}
                </div>
                <div class="mb-3">
                    <strong>Danh mục:</strong> {{ $course->category->name }}
                </div>
                <div class="mb-3">
                    <strong>Giảng viên:</strong> {{ $course->user->name }}
                </div>
                <div class="mb-3">
                    <strong>Trạng thái:</strong>
                    @if ($course->status == 'pending')
                        <span class="badge bg-warning">Chờ duyệt</span>
                    @elseif($course->status == 'published')
                        <span class="badge bg-success">Đã phê duyệt</span>
                    @elseif($course->status == 'draft')
                        <span class="badge bg-danger">Đã từ chối</span>
                    @endif
                </div>
                <div class="mb-3">
                    <strong>Ngày tạo:</strong> {{ $course->created_at }}
                </div>
                <div class="mb-3">
                    <strong>Ảnh:</strong>
                    <img src="{{ Storage::url($course->thumbnail) }}" alt="thumbnail" height="100" width="100">
                </div>
                <a href="{{ route('admin.courses.index') }}" class="btn btn-secondary">Quay lại danh sách khóa học</a>
            </div>
        </div>
    </div>
@endsection
