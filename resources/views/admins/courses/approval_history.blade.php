@extends('layouts.master')

@section('title')
    Lịch sử kiểm duyệt khoá học
@endsection

@section('content')
    <div class="m-3">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h2 class="m-0">Lịch sử kiểm duyệt khóa học</h2>
                <a href="{{ route('admin.censor.courses.list') }}" class="btn btn-primary btn-sm">Quay lại</a>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('admin.courses.approval.history') }}" class="row gx-3 mb-2">

                    <div class="col-lg-4 col-12 mb-2">
                        <input type="text" name="search" class="form-control"
                            placeholder="Tìm theo tên khóa học hoặc người duyệt" value="{{ request('search') }}">
                    </div>
                    <div class="col-lg-2 col-12 mb-2">
                        <select name="category" class="form-select ms-2 text-dark">
                            <option value="">Tất cả danh mục</option>
                            @foreach ($parentCategory as $category)
                                <option value="{{ $category->id }}"
                                    {{ request('category') == $category->id ? 'selected' : '' }}>{{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @php
                        $statusLabels = [
                            'pending' => 'Chờ duyệt',
                            'approved' => 'Đã duyệt',
                            'rejected' => 'Từ chối',
                        ];
                    @endphp
                    <div class="col-lg-2 col-12 mb-2">
                        <select name="status" class="form-select ms-2 text-dark">
                            <option value="">Tất cả trạng thái</option>
                            @foreach ($statuses as $sta)
                                <option value="{{ $sta }}" {{ request('status') == $sta ? 'selected' : '' }}>
                                    {{ $statusLabels[$sta] ?? $sta }}</option>
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
                            <th class="border-end">Khoá học</th>
                            <th class="border-end">Người kiểm duyệt</th>
                            <th class="border-end">Trạng thái</th>
                            <th class="border-end">Lý do (nếu có)</th>
                            <th class="border-end">Thời gian</th>
                            <th class="border-end text-center">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($courses as $history)
                            <tr>
                                <td class="border-end">{{ $history->course->title }}</td>
                                <td class="border-end">{{ $history->user->name }}</td>
                                <td class="border-end">
                                    @if ($history->status == 'approved')
                                        <span class="badge bg-success">Đã duyệt</span>
                                    @elseif ($history->status == 'rejected')
                                        <span class="badge bg-danger">Từ chối</span>
                                    @endif
                                </td>
                                <td class="border-end">{{ $history->comment ?? 'Không có' }}</td>
                                <td class="border-end">{{ $history->created_at->format('d/m/Y H:i') }}</td>
                                <td class="border-end text-center">
                                    <a href="{{ route('admin.courses.history.show', $history->course->id) }}"
                                        class="btn btn-info btn-sm">
                                        <i class="fas fa-eye"></i> Xem chi tiết
                                    </a>

                                </td>

                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="d-flex justify-content-center mt-3">
                    {{ $courses->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection

@section('style')
    <link rel="stylesheet" href="https://cdn.datatables.net/2.2.2/css/dataTables.bootstrap5.min.css">
    <style>
        #table {
            font-size: 14px;
        }

        #table th,
        #table td {
            padding: 10px;
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
