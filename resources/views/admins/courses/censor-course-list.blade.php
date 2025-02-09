@extends('layouts.master')

@section('title')
    Kiểm duyệt khoá học
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <h2 class="m-0">Kiểm duyệt khóa học</h2>
        </div>
        <div class="card-body">
            <table class="table table-hover border" id="table">
                <thead class="table-light">
                    <tr>
                        <th>Ảnh bìa</th>
                        <th>Tiêu đề</th>
                        <th>Danh mục</th>
                        <th>Giảng viên</th>
                        <th>Giá</th>
                        <th>Thời gian gửi</th>
                        <th>Trạng thái</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><img src="https://files.fullstack.edu.vn/f8-prod/courses/7.png" width="100px" alt=""></td>
                        <td>Khoá học Laravel A-Z</td>
                        <td>Lập trình Website</td>
                        <td>Tôn Nghệ Không</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td ><a href="" class="btn btn-info btn-sm">Kiểm tra</a></td>
                    </tr>
                </tbody>
            </table>
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
