@extends('layouts.master')

@section('content')
    <div class="container my-5">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-content-center">
                <h2 class="m-0">Danh sách Tags</h2>
                <a href="{{ route('tags.create') }}" class="btn btn-primary">Thêm mới</a>
            </div>
            <div class="card-body">
                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                <table class="table table-bordered my-2">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Tên Tag</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($items as $tag)
                            <tr>
                                <td>{{ $tag->id }}</td>
                                <td>{{ $tag->name }}</td>
                                <td>
                                    <a href="{{ route('tags.show', $tag) }}" class="btn btn-info">Xem</a>
                                    <a href="{{ route('tags.edit', $tag) }}" class="btn btn-warning">Sửa</a>
                                    <form action="{{ route('tags.destroy', $tag) }}" method="POST"
                                        style="display:inline-block;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" onclick="return confirm('are you sure?')" class="btn btn-danger">Xóa</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                {{ $items->links() }}
            </div>
        </div>
    </div>
@endsection
