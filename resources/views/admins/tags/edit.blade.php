@extends('layouts.master')

@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-content-center">
            <h2>Chỉnh sửa Tag</h2>
        </div>
        <div class="card-body">
            <form action="{{ route('tags.update', $item) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label for="name" class="form-label">Tên Tag</label>
                    <input type="text" name="name" class="form-control" value="{{ $item->name }}" required>
                    @error('name')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <button type="submit" class="btn btn-primary">Cập nhật</button>
                <a href="{{ route('tags.index') }}" class="btn btn-secondary">Quay lại</a>
            </form>
        </div>
    </div>
@endsection
