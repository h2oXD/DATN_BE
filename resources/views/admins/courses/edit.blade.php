@extends('layouts.master')

@section('title')
    Chỉnh sửa khóa học
@endsection

@section('content')
    <div class="container my-5">
        <div class="card">
            <div class="card-header">
                <h2 class="m-0">Chỉnh sửa khóa học</h2>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('courses.update', $course->id) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <!-- Tiêu đề khóa học -->
                    <div class="mb-3">
                        <label for="title" class="form-label">Tiêu đề khóa học</label>
                        <input type="text" class="form-control" id="title" name="title" value="{{ $course->title }}" required>
                    </div>

                    <!-- Mô tả khóa học -->
                    <div class="mb-3">
                        <label for="description" class="form-label">Mô tả khóa học</label>
                        <textarea class="form-control" id="description" name="description" rows="5" required>{{ $course->description }}</textarea>
                    </div>

                    <!-- Danh mục khóa học -->
                    <div class="mb-3">
                        <label for="category" class="form-label">Danh mục khóa học</label>
                        <select name="category_id" class="form-select" id="category" required>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" {{ $course->category_id == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Tags -->
                    <div class="mb-3">
                        <label for="tags" class="form-label">Tags</label>
                        <select name="tags[]" class="form-select" id="tags" multiple>
                            @foreach ($tags as $tag)
                                <option value="{{ $tag->id }}" {{ in_array($tag->id, $course->tags->pluck('id')->toArray()) ? 'selected' : '' }}>{{ $tag->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Giá -->
                    <div class="mb-3">
                        <label for="price" class="form-label">Giá khóa học</label>
                        <input type="number" class="form-control" id="price" name="price" value="{{ $course->price }}">
                    </div>

                    <!-- Trạng thái -->
                    <div class="mb-3">
                        <label for="status" class="form-label">Trạng thái khóa học</label>
                        <select name="status" class="form-select" id="status" required>
                            <option value="draft" {{ $course->status == 'draft' ? 'selected' : '' }}>Chờ duyệt</option>
                            <option value="published" {{ $course->status == 'published' ? 'selected' : '' }}>Đã phê duyệt</option>
                        </select>
                    </div>

                    <!-- Ảnh đại diện -->
                    <div class="mb-3">
                        <label for="thumbnail" class="form-label">Ảnh đại diện</label>
                        <input type="file" class="form-control" id="thumbnail" name="thumbnail">
                        @if($course->thumbnail)
                            <img src="{{ Storage::url($course->thumbnail) }}" alt="thumbnail" class="img-fluid mt-2" width="150">
                        @endif
                    </div>

                    <!-- Submit -->
                    <button type="submit" class="btn btn-primary">Cập nhật</button>
                </form>
            </div>
        </div>
    </div>
@endsection
