@extends('layouts.master')

@section('content')
    <div class="container mt-3">
        <div class="card bg-white shadow">
            <div class="card-header">
                <h2>Thêm danh mục</h2>
            </div>
            <div class="card-body">
                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                <form action="{{ route('admin.categories.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Tên danh mục</label>
                        <input type="text" name="name" value="{{ old('name') }}"
                            class="form-control @error('name') is-invalid @enderror">
                        @error('name')
                            <div class="text-danger mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Danh mục cha</label>
                        <select name="parent_id" class="form-control @error('parent_id') is-invalid @enderror">
                            <option value="">Không có (danh mục gốc)</option>
                            @foreach ($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>

                    </div>

                    <button type="submit" class="btn btn-success">Thêm</button>
                    <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">Quay lại</a>
                </form>
            </div>
        </div>
    </div>

    </div>
@endsection
