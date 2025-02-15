@extends('layouts.master')

@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-content-center">
            <h2>Thùng rác Tags</h2>
            <a href="{{ route('admin.tags.index') }}" class="btn btn-primary">Quay lại</a>
        </div>
        <div class="card-body">
            @if (session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif
            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Tên</th>
                        <th>Khóa học liên quan</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($trashedTags as $tag)
                        <tr>
                            <td>{{ $tag->id }}</td>
                            <td>{{ $tag->name }}</td>
                            <td>
                                @if ($tag->courses->count() > 0)
                                    <ul>
                                        @foreach ($tag->courses as $course)
                                            <li>{{ $course->title }}</li>
                                        @endforeach
                                    </ul>
                                @else
                                    Không có
                                @endif
                            </td>
                            <td>
                                <form action="{{ route('admin.tags.restore', $tag->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-success">Khôi phục</button>
                                </form>

                                @if ($tag->courses->count() == 0)
                                    <form action="{{ route('admin.tags.forceDelete', $tag->id) }}" method="POST"
                                        style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger">Xóa vĩnh viễn</button>
                                    </form>
                                @else
                                    <button class="btn btn-danger" disabled title="Cần xóa các khóa học trước">Xóa vĩnh
                                        viễn</button>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
