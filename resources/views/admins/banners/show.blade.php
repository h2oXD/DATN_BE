@extends('layouts.master')

@section('content')
    <div class="container mt-3">
        <div class="card bg-white shadow">
            <div class="card-header">
                <h2 class="mb-0">Chi tiết Banner</h2>
            </div>
            <div class="card-body">
                <p><strong>Tiêu đề:</strong> {{ $banner->title }}</p>
                <p><strong>Trạng thái:</strong> 
                    @if ($banner->status)
                        <span class="badge bg-success">Hoạt động</span>
                    @else
                        <span class="badge bg-danger">Không hoạt động</span>
                    @endif
                </p>
                <p><strong>Hình ảnh:</strong></p>
                <img src="{{ asset('storage/' . $banner->image) }}" width="400" class="img-thumbnail">

                <p class="mt-3">
                    <a href="{{ route('admin.banners.index') }}" class="btn btn-secondary">Quay lại</a>
                </p>
            </div>
        </div>
    </div>
@endsection
