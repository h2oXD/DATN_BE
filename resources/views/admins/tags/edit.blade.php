@extends('layouts.master')

@section('content')
    <div class="container">
        <h2>Chỉnh sửa Tag</h2>
        <form action="{{ route('tags.update', $tag) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="mb-3">
                <label for="name" class="form-label">Tên Tag</label>
                <input type="text" name="name" class="form-control" value="{{ $tag->name }}" required>
            </div>
            <button type="submit" class="btn btn-primary">Cập nhật</button>
        </form>
    </div>
@endsection
