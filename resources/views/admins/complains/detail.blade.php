@extends('layouts.master')

@section('title')
    Chi tiết kiểm duyệt
@endsection

@section('content')
    <div class="container my-5">
        <div class="card shadow-lg">
            <div class="card-header text-white">
                <h2 class="m-0">Chi tiết kiểm duyệt</h2>
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- Cột bên trái: Thông tin người dùng -->
                    <div class="col-md-6">
                        <div class="card mb-3">
                            <div class="card-header bg-gradient-mix-shade text-white">Thông tin người dùng</div>
                            <div class="card-body">
                                <p><strong>Tên:</strong> {{ $complain->transaction_wallets->wallet->user->name }}</p>
                                <p><strong>Email:</strong> {{ $complain->transaction_wallets->wallet->user->email }}</p>
                                <p><strong>Số điện thoại:</strong> {{ $complain->transaction_wallets->wallet->user->phone_number }}</p>
                            </div>
                        </div>

                        <div class="card mb-3">
                            <div class="card-header bg-gradient-mix-shade text-white">Thông tin giao dịch</div>
                            <div class="card-body">
                                <p><strong>Ngày gửi yêu cầu:</strong>
                                    {{ Carbon\Carbon::parse($complain->transaction_wallets->transaction_date)->format('d/m/Y H:i:s') }}</p>
                                <p><strong>Ngày xác nhận:</strong>
                                    {{ Carbon\Carbon::parse($complain->transaction_wallets->censor_date)->format('d/m/Y H:i:s') }}
                                </p>
                                <p><strong>Số tiền rút:</strong> {{ number_format($complain->transaction_wallets->amount) }} VND</p>
                                <p><strong>Số dư ví sau kiểm duyệt:</strong> {{ number_format($complain->transaction_wallets->balance) }} VND
                                </p>
                            </div>
                        </div>

                        <div class="card mb-3">
                            <div class="card-header bg-gradient-mix-shade text-white">Thông tin thanh toán</div>
                            <div class="card-body">
                                <p><strong>Ngân hàng:</strong> {{ $complain->transaction_wallets->bank_name }}</p>
                                <p><strong>Tên người sở hữu:</strong> {{ $complain->transaction_wallets->bank_nameUser }}</p>
                                <p><strong>Số tài khoản:</strong> {{ $complain->transaction_wallets->bank_number }}</p>
                            </div>
                        </div>

                        <div class="card mb-3">
                            <div class="card-header bg-gradient-mix-shade text-white">Trạng thái giao dịch</div>
                            <div class="card-body">
                                <p><strong>Loại giao dịch:</strong>
                                    @if ($complain->transaction_wallets->type === 'withdraw')
                                        <span>Rút tiền</span>
                                    @endif
                                </p>
                                <p>
                                    <strong>Trạng thái:</strong>
                                    @if ($complain->transaction_wallets->status == 'success')
                                        <span class="badge bg-success">Thành công</span>
                                    @else
                                        <span class="badge bg-danger">Từ chối</span>
                                    @endif
                                </p>
                                <p><strong>Ghi chú:</strong>
                                    {{ $complain->transaction_wallets->note }}
                                </p>
                                <p><strong>Người dùng khiếu nại:</strong>
                                    @if ($complain->transaction_wallets->complain == 0)
                                        <span class="text-muted">Không có</span>
                                    @else
                                        <span class="text-danger">Có</span>
                                    @endif
                                </p>
                            </div>
                        </div>

                    </div>

                    <!-- Cột bên phải: Thông tin thanh toán & trạng thái -->
                    <div class="col-md-6">
                        
                        <div class="card mb-3">
                            <div class="card-header bg-gradient-mix-shade text-white">Thông tin kiểm duyệt</div>
                            <div class="card-body">
                                <p>
                                    <strong>Trạng thái:</strong>
                                    @if ($complain->status == 'resolved')
                                        <span class="badge bg-success">Chấp nhận</span>
                                    @else
                                        <span class="badge bg-danger">Từ chối</span>
                                    @endif
                                </p>
                                <p><strong>Nội dung khiếu nại:</strong>
                                    {{ $complain->description }}
                                </p>
                                <p><strong>Ngày gửi yêu cầu:</strong>
                                    {{ Carbon\Carbon::parse($complain->request_date)->format('d/m/Y H:i:s') }}</p>
                                <p><strong>Admin phản hồi:</strong>
                                    {{ $complain->feedback_by_admin }}
                                </p>
                                <p><strong>Ngày xác nhận:</strong>
                                    {{ Carbon\Carbon::parse($complain->feedback_date)->format('d/m/Y H:i:s') }}</p>
                            </div>
                        </div>

                        <div class="card mb-3">
                            <div class="card-header bg-gradient-mix-shade text-white">Ảnh minh chứng</div>
                            <div class="card-body text-center">
                                @if ($complain->proof_img)
                                    <img src="{{ Storage::url($complain->proof_img) }}"
                                        class="img-fluid rounded zoomable-image" style="max-width: 150px;"
                                        data-bs-toggle="modal" data-bs-target="#imageModal">
                                @else
                                    <p class="text-muted">Không có mã QR</p>
                                @endif
                            </div>
                        </div>

                        <div class="card mb-3">
                            <div class="card-header bg-gradient-mix-shade text-white">Mã QR</div>
                            <div class="card-body text-center">
                                @if ($complain->transaction_wallets->qr_image)
                                    <img src="{{ Storage::url($complain->transaction_wallets->qr_image) }}"
                                        class="img-fluid rounded zoomable-image" style="max-width: 150px;"
                                        data-bs-toggle="modal" data-bs-target="#imageModal">
                                @else
                                    <p class="text-muted">Không có mã QR</p>
                                @endif
                            </div>
                        </div>

                    </div>
                </div>

                <!-- Nút quay lại -->
                <div class="text-center mt-3">
                    <a href="{{ route('admin.censor-complain.history') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Quay lại
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Phóng to ảnh --}}
    <div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content">
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
