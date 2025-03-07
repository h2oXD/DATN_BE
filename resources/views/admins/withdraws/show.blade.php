@extends('layouts.master')

@section('title')
    Chi tiết giao dịch
@endsection

@section('content')
    <div class="container my-5">
        <div class="card">
            <div class="card-header">
                <h2 class="m-0">Chi tiết giao dịch</h2>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <strong>Tên người dùng:</strong> {{ $transaction->wallet->user->name }}
                </div>
                <div class="mb-3">
                    <strong>Email:</strong> {{ $transaction->wallet->user->email }}
                </div>
                <div class="mb-3">
                    <strong>Số điện thoại:</strong> {{ $transaction->wallet->user->phone_number }}
                </div>
                <div class="mb-3">
                    <strong>Ngày gửi yêu cầu:</strong> {{ $transaction->transaction_date }}
                </div>
                <div class="mb-3">
                    <strong>Số tiền rút:</strong> {{ number_format($transaction->amount) }} VND
                </div>
                <div class="mb-3">
                    <strong>Ngân hàng:</strong> {{ $transaction->bank_name }}
                </div>
                <div class="mb-3">
                    <strong>Tên người sở hữu:</strong> {{ $transaction->bank_nameUser }}
                </div>
                <div class="mb-3">
                    <strong>Số tài khoản:</strong> {{ $transaction->bank_number }}
                </div>
                <div class="mb-3">
                    <strong>Mã QR:</strong>
                    @if ($transaction->qr_image)
                        <img src="{{ Storage::url($transaction->qr_image) }}" alt="thumbnail" max-height="100" width="100">
                    @else
                        <span>Trống</span>
                    @endif
                </div>
                <!-- Nút xác nhận -->
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#exampleModalCenter">
                    Đồng ý
                </button>

                <form action="{{ route('admin.censor-withdraw.reject', $transaction->id) }}" method="POST"
                    style="display:inline-block;" id="update-transaction-{{ $transaction->id }}">
                    @csrf
                    @method('PUT')
                    <button type="button" class="btn btn-danger" onclick="confirmDelete({{ $transaction->id }})">Từ chối</button>
                </form>

                <a href="{{ route('admin.censor-withdraw.index') }}" class="btn btn-secondary">Quay lại</a>
            </div>

            <!-- Thông báo xác nhận -->
            <div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">

                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalCenterTitle">Thông báo xác nhận</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>

                        <div class="modal-body">
                            Bạn đã chuyển tiền thành công?
                        </div>
                        
                        <div class="modal-footer">
                            <form action="{{ route('admin.censor-withdraw.accept', $transaction->id) }}" method="post" style="display:inline;">
                                @csrf
                                @method('PUT')
                                <button type="submit" class="btn btn-primary">Xác nhận</button>
                            </form>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection

@section('script')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function confirmDelete(id) {
            Swal.fire({
                title: "Bạn có chắc chắn không?",
                text: "Hành động này không thể hoàn tác!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "Xác nhận",
                cancelButtonText: "Hủy"
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById(`update-transaction-${id}`).submit();
                }
            });
        }
    </script>
@endsection