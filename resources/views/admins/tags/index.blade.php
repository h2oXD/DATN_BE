@extends('layouts.master')

@section('content')
    <div class="container my-5">
        <h2>Danh sách Tags</h2>
        <a href="{{ route('tags.create') }}" class="btn btn-primary">Thêm Tag</a>

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
                @foreach ($tags as $tag)
                    <tr>
                        <td>{{ $tag->id }}</td>
                        <td>{{ $tag->name }}</td>
                        <td>
                            <a href="{{ route('tags.show', $tag) }}" class="btn btn-info">Xem</a>
                            <a href="{{ route('tags.edit', $tag) }}" class="btn btn-warning">Sửa</a>
                            <form action="{{ route('tags.destroy', $tag) }}" method="POST" style="display:inline-block;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">Xóa</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{ $tags->links() }}
    </div>
@endsection
