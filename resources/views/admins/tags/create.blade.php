@extends('layouts.master')

@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-content-center">
            <h2>Thêm mới Tag</h2>
        </div>
        <div class="card-body">
            <form action="{{ route('tags.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="name" class="form-label">Tên Tag</label>
                    <input type="text" name="name" class="form-control">
                    @error('name')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <button type="submit" class="btn btn-success">Lưu</button>
                <a href="{{ route('tags.index') }}" class="btn btn-secondary">Quay lại</a>
            </form>
        </div>
    </div>
@endsection
