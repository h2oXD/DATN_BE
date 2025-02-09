@extends('layouts.master')

@section('title')
    Danh sách khóa học
@endsection

@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-content-center">
            <h2 class="m-0">Kiểm duyệt khoá học</h2>
        </div>
        <div class="card-body p-0">
            <form method="GET" action="{{ route('courses.index') }}" class="row gx-3 m-2">
                <div class="col-lg-8 col-12 mb-2">
                    <input type="text" name="search" class="form-control" placeholder="Tìm kiếm khóa học"
                        value="{{ request('search') }}">
                </div>
                <div class="col-lg-2 col-12 mb-2">
                    <select name="category" class="form-select ms-2 text-dark">
                        <option value="">Tất cả danh mục</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}"
                                {{ request('category') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-2 col-12 mb-2">
                    <button type="submit" class="btn btn-info ms-2">Tìm kiếm</button>
                </div>
            </form>
            @if (session()->has('success') && !session()->get('success'))
                <div class="alert alert-danger">
                    {{ session()->get('error') }}
                </div>
            @endif

            @if (session()->has('success') && session()->get('success'))
                <div class="alert alert-info">
                    Thao tác thành công!
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <div class="table-responsive">
                <table class="table mb-3 text-nowrap table-hover table-centered">
                    <thead class="table-light">
                        <tr>
                            <th scope="col">Tên khóa học</th>
                            <th scope="col">Danh mục</th>
                            <th scope="col">Giảng viên</th>
                            <th scope="col">Trạng thái</th>
                            <th scope="col">Thao tác</th>
                            <th></th>
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

                                <td>
                                    @if ($course->status == 'draft')
                                        <form action="{{ route('courses.approve', $course->id) }}" method="POST"
                                            style="display:inline-block;">
                                            @csrf
                                            <button type="submit" class="btn btn-success btn-sm">Phê duyệt</button>
                                        </form>
                                        <form action="{{ route('courses.reject', $course->id) }}" method="POST"
                                            style="display:inline-block;">
                                            @csrf
                                            <button type="submit" class="btn btn-danger btn-sm">Từ chối</button>
                                        </form>
                                    @endif

                                </td>
                                <td>
                                    <span class="dropdown dropstart">
                                        <a class="btn-icon btn btn-ghost btn-sm rounded-circle" href="#"
                                            role="button" data-bs-toggle="dropdown" data-bs-offset="-20,20"
                                            aria-expanded="false">
                                            <i class="fe fe-more-vertical"></i>
                                        </a>
                                        <span class="dropdown-menu">
                                            <span class="dropdown-header">Settings</span>
                                            <a href="{{ route('courses.show', $course->id) }}" class="dropdown-item">
                                                <svg class="w-10 me-2" xmlns="http://www.w3.org/2000/svg" fill="none"
                                                    viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                                    class="size-6">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                                </svg>
                                                Xem
                                            </a>

                                            <a href="{{ route('courses.edit', $course->id) }}" class="dropdown-item">
                                                <svg class="w-10 me-2" xmlns="http://www.w3.org/2000/svg" fill="none"
                                                    viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                                    class="size-6">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                                </svg>
                                                Sửa
                                            </a>
                                        </span>
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                {{ $courses->links() }}
            </div>
        </div>
    </div>
@endsection
