@extends('layouts.master')

@section('title')
    Kiểm duyệt người dùng
@endsection

@section('content')
    <div class="m-3">
        <div class="card">
            <div class="card-header">
                <h2 class="m-0">Phê duyệt giảng viên</h2>
            </div>
            <div class="card-body">
                <table class="table table-hover border" id="table">
                    <thead class="table-light">
                        <tr>
                            <th class="border-end">Người dùng</th>
                            <th class="border-end">Email</th>
                            <th class="border-end">Thời gian gửi</th>
                            <th class="border-end">Trạng thái</th>
                            <th class="border-end">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ( $lecturerRegisters as $lecturerRegister)
                            <tr>
                                <td class="border-end">
                                    <div class="d-flex align-items-center flex-row gap-2">
                                        <div class="position-relative">
                                            <img src="{{ Storage::url($lecturerRegister->user->profile_picture) }}" alt="avatar" class="rounded-circle avatar-md">
                                            <a href="#" class="position-absolute mt-5 ms-n4">
                                                <span class="status bg-success"></span> </a>
                                        </div>
                                        <h5 class="mb-0">{{ $lecturerRegister->user->name }}</h5>
                                    </div>
                                </td>
                                <td class="border-end">{{ $lecturerRegister->user->email}}</td>
                                <td class="border-end">{{ $lecturerRegister->updated_at }}</td>
                                <td class="border-end">
                                    @if ($lecturerRegister->status == 'pending')
                                        <span class="badge bg-warning">Chờ duyệt</span>
                                    @endif
                                    @if ($lecturerRegister->status == 'approved')
                                        <span class="badge bg-success">Đã duyệt</span>
                                    @endif
                                    @if ($lecturerRegister->status == 'rejected')
                                        <span class="badge bg-danger">Từ chối</span>
                                    @endif
                                
                                </td>
                                <td class="border-end"><a href="{{ route('admin.lecturer_registers.show', $lecturerRegister->id) }}"
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
