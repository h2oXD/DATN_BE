@extends('layouts.master')

@section('title')
    Lịch sử mua khóa học
@endsection

@section('content')

    <div class="card m-3">
        <div class="card-header d-flex justify-content-between align-content-center">
            <h2 class="m-0">Lịch sử mua khóa học</h2>
        </div>
        <div class="card-body p-0">

            <form method="GET" action="{{ route('admin.transaction-courses.index') }}" class="row gx-3 m-2">
                <div class="col-lg-8 col-12 mb-2">
                    <input type="text" name="search" class="form-control" placeholder="Tìm kiếm lịch sử mua khóa học"
                        value="{{ request('search') }}">
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
                        <th class="border-end">ID</th>
                        <th class="border-end">Tên người dùng</th>
                        <th class="border-end">Email</th>
                        <th class="border-end">Tên khóa học</th>
                        <th class="border-end">Số tiền thanh toán</th>
                        <th class="border-end">Phương thức thanh toán</th>
                        <th class="border-end">Ngày giao dịch</th>
                        <th class="border-end">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($transactions as $transaction)
                        <tr>
                            <td class="border-end">{{ $transaction->id }}</td>
                            <td class="border-end">{{ $transaction->user_name }}</td>
                            <td class="border-end">{{ $transaction->user_email }}</td>
                            <td class="border-end">{{ $transaction->course_title }}</td>
                            <td class="border-end">{{ number_format($transaction->amount) }} VND</td>
                            <td class="border-end">
                                @if ($transaction->payment_method === 'wallet')
                                    <p>Ví điện tử</p>
                                @elseif ($transaction->payment_method === 'bank_transfer')
                                    <p>Chuyển khoản ngân hàng</p>
                                @elseif ($transaction->payment_method === 'credit_card')
                                    <p>Thẻ tín dụng</p>
                                @elseif ($transaction->payment_method === 'paypal')
                                    <p>Paypal</p>
                                @endif
                            </td>
                            <td class="border-end">{{ \Carbon\Carbon::parse($transaction->transaction_date)->format('d/m/Y H:i:s') }}</td>
                            <td class="border-end">
                                <a href="{{ route('admin.transaction-courses.show', $transaction->id) }}"
                                    class="btn btn-info btn-sm">
                                    Xem chi tiết
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

@endsection

@section('script')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function confirmDelete(id) {
            Swal.fire({
                title: "Bạn có chắc chắn muốn xoá?",
                text: "Hành động này không thể hoàn tác!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "Xoá",
                cancelButtonText: "Hủy"
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById(`delete-voucher-${id}`).submit();
                }
            });
        }

        // table form
        $(document).ready(function() {
            $('#table').DataTable({
                "order": []
            });
        });
    </script>
@endsection
