@extends('layouts.master')

@section('title')
    Kiểm duyệt khoá học
@endsection

@section('content')
    <div class="m-3">
        <div class="card">
            <div class="card-header">
                <h2 class="m-0">Kiểm duyệt khóa học</h2>
            </div>
            <div class="card-body">
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
