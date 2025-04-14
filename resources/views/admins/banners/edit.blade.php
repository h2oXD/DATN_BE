@extends('layouts.master')

@section('content')
    <div class="container mt-3">
        <div class="card bg-white shadow ">
            <div class="card-header bg-gradient-mix-shade">
                <h2 class="text-white">Chỉnh sửa Banner</h2>
            </div>

            <div class="card-body">
                <form action="{{ route('admin.banners.update', $banner->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="row g-4 pb-4">
                        <div class="col-md-6">
                            <label class="form-label">Tiêu đề</label>
                            <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
                                value="{{ old('title', $banner->title) }}">
                            @error('title')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Mô tả</label>
                            <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="3"
                                placeholder="Nhập mô tả banner">{{ old('description', $banner->description) }}</textarea>
                            @error('description')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Hình ảnh hiện tại</label>
                            <div>
                                <img src="{{ asset('storage/' . $banner->image) }}" width="150">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Cập nhật hình ảnh</label>
                            <input type="file" name="image" class="form-control @error('image') is-invalid @enderror">
                            @error('image')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Đường dẫn (Link)</label>
                            <input type="url" name="link" class="form-control @error('link') is-invalid @enderror"
                                value="{{ old('link', $banner->link) }}" placeholder="Nhập đường dẫn banner (nếu có)">
                            @error('link')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Trạng thái</label>
                            <select name="status" class="form-control @error('status') is-invalid @enderror">
                                <option value="1" {{ old('status', $banner->status) == '1' ? 'selected' : '' }}>Hoạt
                                    động
                                </option>
                                <option value="0" {{ old('status', $banner->status) == '0' ? 'selected' : '' }}>Không
                                    hoạt
                                    động</option>
                            </select>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-warning">Cập nhật</button>
                    <a href="{{ route('admin.banners.index') }}" class="btn btn-secondary">Quay lại</a>
                </form>
            </div>
        </div>
    </div>
@endsection
