@extends('layouts.master')

@section('title')
    Kiểm duyệt khoá học
@endsection

@section('content')
    <div class="m-3">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h2 class="m-0">Kiểm duyệt khóa học</h2>
                <a href="{{ route('admin.courses.approval.history') }}" class="btn btn-primary btn-sm">Lịch sử kiểm duyệt</a>
            </div>
            <div class="card-body">
                @if (session()->has('errors') && !session()->get('errors'))
                    <div class="alert alert-danger">
                        {{ session()->get('errors') }}
                    </div>
                @endif

                @if (session()->has('success') && session()->get('success'))
                    <div class="alert alert-info">
                        Thao tác thành công!
                    </div>
                @endif
                <form method="GET" action="{{ route('admin.censor.courses.list') }}" class="row gx-3 mb-2">
               
                <div class="col-lg-4 col-12 mb-2">
                    <input type="text" name="search" class="form-control"
                        placeholder="Tìm theo tên khóa học hoặc giảng viên" value="{{ request('search') }}">
                </div>
                <div class="col-lg-2 col-12 mb-2">
                    <select name="category" class="form-select ms-2 text-dark">
                        <option value="">Tất cả danh mục</option>
                        @foreach ($parentCategory as $category)
                            <option value="{{ $category->id }}"
                                {{ request('category') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-2 col-12 mb-2">
                    <select name="language" class="form-select ms-2 text-dark">
                        <option value="">Tất cả ngôn ngữ</option>
                        @foreach ($languages as $language)
                            <option value="{{ $language }}" {{ request('language') == $language ? 'selected' : '' }}>
                                {{ $language }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-2 col-12 mb-2">
                    <select name="level" class="form-select ms-2 text-dark">
                        <option value="">Tất cả trình độ</option>
                        @foreach ($levels as $level)
                            <option value="{{ $level }}" {{ request('level') == $level ? 'selected' : '' }}>
                                {{ $level }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-2 col-12 mb-2">
                    <button type="submit" class="btn btn-info ms-2">Tìm kiếm</button>
                </div>
            </form>
                <table class="table table-hover border" id="table">
                    
                    <thead class="table-light">
                        <tr>
                            <th class="border-end">Ảnh bìa</th>
                            <th class="border-end">Tiêu đề</th>
                            <th class="border-end">Danh mục</th>
                            <th class="border-end">Giảng viên</th>
                            <th class="border-end">Giá</th>
                            <th class="border-end">Thời gian gửi</th>
                            <th class="border-end">Trạng thái</th>
                            <th class="border-end">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($courses as $course)
                            <tr>
                                <td class="border-end"><img src="{{ Storage::url($course->thumbnail) }}" width="100px"
                                        alt="">
                                </td>
                                <td class="border-end">{{ $course->title }}</td>
                                <td class="border-end">{{ $course->category->name }}</td>
                                <td class="border-end">{{ $course->user->name }}</td>
                                @if ($course->is_free)
                                    <td class="border-end">Miễn phí</td>
                                @elseif($course->price_sale)
                                    <td class="border-end">
                                        <span
                                            style="text-decoration: line-through; color: gray;">{{ $course->price_regular }}
                                            VNĐ</span>
                                        {{ $course->price_sale }} VNĐ
                                    </td>
                                @else
                                    <td class="border-end">{{ $course->price_regular }} VNĐ</td>
                                @endif
                                <td class="border-end">{{ $course->submited_at }}</td>
                                <td class="border-end">
                                    @if ($course->status == 'pending')
                                        <span class="badge bg-warning">Chờ duyệt</span>
                                    @endif
                                </td>
                                <td class="border-end"><a href="{{ route('admin.check.course', $course->id) }}"
                                        class="btn btn-info btn-sm">Kiểm tra</a></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('style')
    <link rel="stylesheet" href="https://cdn.datatables.net/2.2.2/css/dataTables.bootstrap5.min.css">
    <style>
        #table {
            font-size: 14px;
            /* Giảm kích thước font chữ */
        }

        #table th,
        #table td {
            padding: 10px;
            /* Giảm padding */
        }
    </style>
@endsection

@section('script')
    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.datatables.net/2.2.2/js/dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/2.2.2/js/dataTables.bootstrap5.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#table').DataTable({});
        });
    </script>
@endsection
