@extends('layouts.master')

@section('title')
    Chi tiết yêu cầu
@endsection

@section('content')
    <div class="container my-5">
        <div class="card shadow-lg">
            <div class="card-header text-white">
                <h2 class="m-0">Chi tiết yêu cầu</h2>
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- Cột bên trái: Thông tin người dùng và giao dịch -->
                    <div class="col-md-6">
                        <!-- Thông tin người dùng -->
                        <div class="card mb-3">
                            <div class="card-header bg-gradient-mix-shade text-white">Thông tin người dùng</div>
                            <div class="card-body">
                                <p><strong>Tên:</strong> {{ $transaction->wallet->user->name }}</p>
                                <p><strong>Email:</strong> {{ $transaction->wallet->user->email }}</p>
                                <p><strong>Số điện thoại:</strong> {{ $transaction->wallet->user->phone_number }}</p>
                            </div>
                        </div>

                        <!-- Thông tin giao dịch -->
                        <div class="card mb-3">
                            <div class="card-header bg-gradient-mix-shade text-white">Thông tin giao dịch</div>
                            <div class="card-body">
                                <p><strong>Ngày gửi yêu cầu:</strong> {{ Carbon\Carbon::parse($transaction->transaction_date)->format('d/m/Y H:i:s') }}</p>
                                <p><strong>Số tiền rút:</strong> {{ number_format($transaction->amount) }} VND</p>
                                <p><strong>Số dư ví hiện tại:</strong> {{ number_format($transaction->wallet->balance) }} VND</p>
                            </div>
                        </div>
                    </div>

                    <!-- Cột bên phải: Thông tin ngân hàng và mã QR -->
                    <div class="col-md-6">
                        <!-- Thông tin ngân hàng -->
                        <div class="card mb-3">
                            <div class="card-header bg-gradient-mix-shade text-white">Thông tin thanh toán</div>
                            <div class="card-body">
                                <p><strong>Ngân hàng:</strong> {{ $transaction->bank_name }}</p>
                                <p><strong>Tên người sở hữu:</strong> {{ $transaction->bank_nameUser }}</p>
                                <p><strong>Số tài khoản:</strong> {{ $transaction->bank_number }}</p>
                            </div>
                        </div>

                        <!-- Mã QR -->
                        <div class="card mb-3">
                            <div class="card-header bg-gradient-mix-shade text-white">Mã QR</div>
                            <div class="card-body text-center">
                                @if ($transaction->qr_image)
                                    <img src="{{ Storage::url($transaction->qr_image) }}" class="img-fluid rounded zoomable-image"
                                    style="max-width: 150px;" data-bs-toggle="modal" data-bs-target="#imageModal">
                                @else
                                    <p class="text-muted">Không có mã QR</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Các nút thao tác -->
                <div class="d-flex justify-content-center gap-3">
                    <!-- Đồng ý -->
                    <button type="button" class="btn btn-success" data-bs-toggle="modal"
                        data-bs-target="#exampleModalCenter">
                        Đồng ý
                    </button>

                    <!-- Từ chối -->
                    <form action="{{ route('admin.censor-withdraw.reject', $transaction->id) }}" method="POST"
                        style="display:inline-block;" id="update-transaction-{{ $transaction->id }}">
                        @csrf
                        @method('PUT')
                        <button type="button" class="btn btn-danger" onclick="confirmDelete({{ $transaction->id }})">Từ
                            chối</button>
                    </form>

                    <!-- Quay lại -->
                    <a href="{{ route('admin.censor-withdraw.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Quay lại
                    </a>
                </div>
                
            </div>
        </div>
    </div>

    <!-- Thông báo xác nhận -->
    <div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
        aria-hidden="true">
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
                    <form action="{{ route('admin.censor-withdraw.accept', $transaction->id) }}" method="post"
                        style="display:inline;">
                        @csrf
                        @method('PUT')
                        <button type="submit" class="btn btn-primary">Xác nhận</button>
                    </form>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Phóng to ảnh --}}
    <div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md">  <div class="modal-content">
                <div class="modal-body">
                    <img src="" class="img-fluid" id="modalImage">
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

        // Phóng to ảnh
        document.addEventListener('DOMContentLoaded', function() {
            const zoomableImages = document.querySelectorAll('.zoomable-image');
            const modalImage = document.getElementById('modalImage');

            zoomableImages.forEach(img => {
                img.addEventListener('click', () => {
                    modalImage.src = img.src;
                });
            });
        });

    </script>
@endsection
