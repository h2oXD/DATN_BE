@extends('layouts.master')

@section('content')
    <div class="container">
        <h2>Thêm mới Tag</h2>
        <form action="{{ route('tags.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label for="name" class="form-label">Tên Tag</label>
                <input type="text" name="name" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-success">Lưu</button>
        </form>
    </div>
@endsection
