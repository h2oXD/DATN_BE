@extends('layouts.master')

@section('content')
    <div class="container mt-3">
        <div class="card bg-white shadow">
            <div class="card-header">
                <h2>Chỉnh sửa danh mục</h2>
            </div>

            <div class="card-body">
                <form action="{{ route('admin.categories.update', $category->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label">Tên danh mục</label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                            value="{{ old('name', $category->name) }}">
                        @error('name')
                            <div class="text-danger mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    @if ($category->parent_id !== null)
                        <div class="mb-3">
                            <label class="form-label">Danh mục cha</label>
                            <select name="parent_id" class="form-control @error('parent_id') is-invalid @enderror">
                                <option value="">Không có</option>
                                @foreach ($categories as $cat)
                                    <option value="{{ $cat->id }}"
                                        {{ old('parent_id', $category->parent_id) == $cat->id ? 'selected' : '' }}>
                                        {{ $cat->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    <button type="submit" class="btn btn-warning">Cập nhật</button>
                    <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">Quay lại</a>
                </form>
            </div>

        </div>




    </div>
@endsection
