@extends('layouts.master')

@section('content')
    <div class="container mt-3">
        <div class="card bg-white shadow">
            <div class="card-header">
                <h2>Thêm Banner</h2>
            </div>
            <div class="card-body">
                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                <form action="{{ route('admin.banners.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Tiêu đề</label>
                        <input type="text" name="title" value="{{ old('title') }}"
                            class="form-control @error('title') is-invalid @enderror">
                        @error('title')
                            <div class="text-danger mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Hình ảnh</label>
                        <input type="file" name="image" class="form-control @error('image') is-invalid @enderror">
                        @error('image')
                            <div class="text-danger mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Đường dẫn (Link)</label>
                        <input type="url" name="link" value="{{ old('link') }}"
                            class="form-control @error('link') is-invalid @enderror"
                            placeholder="Nhập đường dẫn banner (nếu có)">
                        @error('link')
                            <div class="text-danger mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Trạng thái</label>
                        <select name="status" class="form-control @error('status') is-invalid @enderror">
                            <option value="1" {{ old('status') == '1' ? 'selected' : '' }}>Hoạt động</option>
                            <option value="0" {{ old('status') == '0' ? 'selected' : '' }}>Không hoạt động</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-success">Thêm</button>
                    <a href="{{ route('admin.banners.index') }}" class="btn btn-secondary">Quay lại</a>
                </form>
            </div>
        </div>
    </div>
@endsection
