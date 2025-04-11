@extends('layouts.master')

@section('title')
    Kiểm duyệt yêu cầu khiếu nại
@endsection

@section('content')
    <div class="m-3">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-content-center">
                <h2 class="m-0">Kiểm duyệt yêu cầu khiếu nại</h2>

                <a href="{{ route('admin.censor-complain.history') }}" class="btn btn-primary">Lịch sử kiểm duyệt</a>
            </div>
            <div class="card-body">
                @if (count($items) <= 0)
                    <h3 class="text-center">Chưa có khiếu nại nào</h3>
                @else
                    <form method="GET" action="{{ route('admin.censor-complain.index') }}" class="row gx-3">
                        <div class="col-lg-8 col-12 mb-2">
                            <input type="text" name="search" class="form-control"
                                placeholder="Tìm kiếm yêu cầu khiếu nại" value="{{ request('search') }}">
                        </div>
                        <div class="col-lg-2 col-12 mb-2">
                            <select name="category" class="form-select ms-2 text-dark">
                                <option value="">Chọn cột</option>
                                @foreach ($columns as $key => $label)
                                    <option value="{{ $key }}" {{ request('category') == $key ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-2 col-12 mb-2">
                            <button type="submit" class="btn btn-info ms-2">Tìm kiếm</button>
                        </div>
                    </form>

                    @if (session()->has('error') && session()->get('error'))
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

                    <table class="table table-hover border" id="table">
                        <thead class="table-light">
                            <tr>
                                <th class="border-end">STT</th>
                                <th class="border-end">Tên người dùng</th>
                                <th class="border-end">Email</th>
                                <th class="border-end">Ngày gửi yêu cầu</th>
                                <th class="border-end">Trạng thái</th>
                                <th class="border-end">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($items as $complain)
                                <tr>
                                    <td class="border-end">{{ $complain->id }}</td>
                                    <td class="border-end">{{ $complain->transaction_wallets->wallet->user->name }}</td>
                                    <td class="border-end">{{ $complain->transaction_wallets->wallet->user->email }}</td>
                                    <td class="border-end">
                                        {{ Carbon\Carbon::parse($complain->request_date)->format('d/m/Y H:i:s') }}</td>
                                    <td class="border-end">
                                        @if ($complain->status)
                                            <span class="badge bg-warning">Chờ duyệt</span>
                                        @endif
                                    </td>
                                    <td class="border-end">
                                        <a href="{{ route('admin.censor-complain.show', $complain->id) }}"
                                            class="btn btn-info btn-sm">
                                            Kiểm tra
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
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
            $('#table').DataTable({
                "order": []
            });
        });
    </script>
@endsection
