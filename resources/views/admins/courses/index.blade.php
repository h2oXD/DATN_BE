@extends('layouts.master')

@section('title')
    Danh sách khóa học
@endsection

@section('content')
    <div class="container my-5">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-content-center">
                <h2 class="m-0">Danh sách khóa học</h2>
            </div>
            <div class="card-body p-0">
                <form method="GET" action="{{ route('courses.index') }}" class="row gx-3 m-2">
                    <div class="col-lg-8 col-12 mb-2">
                        <input type="text" name="search" class="form-control" placeholder="Tìm kiếm khóa học"
                            value="{{ request('search') }}">
                    </div>
                    <div class="col-lg-2 col-12 mb-2">
                        <select name="category" class="form-select ms-2 text-dark">
                            <option value="">Chọn danh mục</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-2 col-12 mb-2">
                        <button type="submit" class="btn btn-info ms-2">Tìm kiếm</button>
                    </div>
                </form>
                <div class="table-responsive">
                    <table class="table mb-3 text-nowrap table-hover table-centered">
                        <thead class="table-light">
                            <tr>
                                <th scope="col">Tên khóa học</th>
                                <th scope="col">Danh mục</th>
                                <th scope="col">Giảng viên</th>
                                <th scope="col">Trạng thái</th>
                                <th scope="col">Ngày tạo</th>
                                <th scope="col">Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($courses as $course)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center flex-row gap-2">
                                            <h5 class="mb-0">{{ $course->title }}</h5>
                                        </div>
                                    </td>
                                    <td>{{ $course->category->name }}</td>
                                    <td>{{ $course->lecturer->name }}</td>
                                    <td>
                                        @if ($course->status == 'draft')
                                            <span class="badge bg-warning">Chờ duyệt</span>
                                        @elseif($course->status == 'published')
                                            <span class="badge bg-success">Đã phê duyệt</span>
                                        @elseif($course->status == 'rejected')
                                            <span class="badge bg-danger">Đã từ chối</span>
                                        @endif
                                    </td>
                                    <td>{{ $course->created_at }}</td>
                                    <td>
                                        <a href="{{ route('courses.show', $course->id) }}" class="btn btn-info btn-sm">Xem</a>
                                        @if ($course->status == 'draft')
                                            <form action="{{ route('courses.approve', $course->id) }}" method="POST" style="display:inline-block;">
                                                @csrf
                                                <button type="submit" class="btn btn-success btn-sm">Phê duyệt</button>
                                            </form>
                                            <form action="{{ route('courses.reject', $course->id) }}" method="POST" style="display:inline-block;">
                                                @csrf
                                                <button type="submit" class="btn btn-danger btn-sm">Từ chối</button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    {{ $courses->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
