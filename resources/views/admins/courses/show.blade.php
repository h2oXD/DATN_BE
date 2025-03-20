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
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Tiêu đề</label>
                        <input type="text" class="form-control" value="{{ $course->title }}" readonly>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Danh mục</label>
                        <input type="text" class="form-control" value="{{ $course->category->name }}" readonly>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Mô tả</label>
                        <textarea class="form-control" rows="3" readonly>{{ $course->description }}</textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Giảng viên</label>
                        <input type="text" class="form-control" value="{{ $course->user->name }}" readonly>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Trạng thái</label>
                        <input type="text"
                            class="form-control 
                        @if ($course->status == 'pending') bg-warning 
                        @elseif($course->status == 'published') bg-success 
                        @elseif($course->status == 'rejected') bg-danger @endif 
                        text-white"
                            value="@if ($course->status == 'pending') Chờ duyệt 
                               @elseif($course->status == 'published') Đã phê duyệt 
                               @elseif($course->status == 'rejected') Đã từ chối @endif"
                            readonly>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Ngày tạo</label>
                        <input type="text" class="form-control" value="{{ $course->created_at }}" readonly>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Ảnh khóa học</label>
                    <div>
                        <img src="{{ Storage::url($course->thumbnail) }}" alt="thumbnail" height="100" width="100"
                            style="border-radius: 8px;">
                    </div>
                </div>

                <div class="mt-4">
                    <a href="{{ route('admin.courses.index') }}" class="btn btn-secondary">Quay lại danh sách khóa học</a>
                </div>
            </div>
        </div>
    </div>
@endsection
