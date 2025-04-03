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
                    <!-- Cột bên trái -->
                    <div class="col-md-6">
                        <div class="card mb-3">
                            <div class="card-header bg-gradient-mix-shade text-white">Thông tin người dùng</div>
                            <div class="card-body">
                                <p><strong>Tên:</strong> {{ $item->user->name }}</p>
                                <p><strong>Email:</strong> {{ $item->user->email }}</p>
                                <p><strong>Số điện thoại:</strong> {{ $item->user->phone_number }}</p>
                            </div>
                        </div>

                        <div class="card mb-3">
                            <div class="card-header bg-gradient-mix-shade text-white">Thông tin giao dịch</div>
                            <div class="card-body">
                                <p><strong>Số tiền thanh toán:</strong> {{ number_format($item->amount) }} VND</p>
                                <p><strong>Phương thức thanh toán:</strong> 
                                    @if ($item->payment_method === 'wallet')
                                        Ví điện tử
                                    @elseif ($item->payment_method === 'bank_transfer')
                                        Chuyển khoản ngân hàng
                                    @elseif ($item->payment_method === 'credit_card')
                                        Thẻ tín dụng
                                    @elseif ($item->payment_method === 'paypal')
                                        Paypal
                                    @endif
                                </p>
                                <strong>Trạng thái:</strong>
                                    @if ($item->status === 'success')
                                        <p class="badge bg-success">Thành công</p>
                                    @elseif ($item->status === 'pending')
                                        <p class="badge bg-warning">Đang giao dịch</p>
                                    @elseif ($item->status === 'failed')
                                        <p class="badge bg-danger">Thất bại</p>
                                    @endif
                                <p><strong>Ngày giao dịch:</strong> {{ Carbon\Carbon::parse($item->transaction_date)->format('d/m/Y H:i:s') }}</p>
                            </div>
                        </div>

                    </div>

                    <!-- Cột bên phải -->
                    <div class="col-md-6">

                        <div class="card mb-3">
                            <div class="card-header bg-gradient-mix-shade text-white">Thông tin khóa học</div>
                            <div class="card-body">
                                <strong>Ảnh khóa học:</strong>
                                    @if ($item->course->thumbnail)
                                        <img src="{{ Storage::url($item->course->thumbnail) }}"
                                            class="img-fluid rounded zoomable-image" style="max-width: 70px;"
                                            data-bs-toggle="modal" data-bs-target="#imageModal">
                                    @else
                                        <p class="text-muted">Không có ảnh</p>
                                    @endif
                                <p class="mt-3"><strong>Tên khóa học:</strong> {{ $item->course->title }}</p>
                                <p><strong>Danh mục:</strong> {{ $item->course->category->name }}</p>
                                <p><strong>Giá gốc:</strong> <del>{{ number_format($item->course->price_regular) }} VND</del></p>
                                <p><strong>Giá bán:</strong> {{ number_format($item->course->price_sale) }} VND</p>
                                <p><strong>Mô tả:</strong> {{ $item->course->description }}</p>
                                <p><strong>Ngôn ngữ:</strong> {{ $item->course->language }}</p>
                                <p><strong>Trình độ:</strong> {{ $item->course->level }}</p>
                                <strong>Trạng thái:</strong>
                                    @if ($item->course->status === 'published')
                                        <p class="badge bg-success">Xuất bản</p>
                                    @elseif ($item->course->status === 'pending')
                                        <p class="badge bg-warning">Đang chờ duyệt</p>
                                    @elseif ($item->course->status === 'draft')
                                        <p class="badge bg-danger">Bản nháp</p>
                                    @endif
                            </div>
                        </div>

                    </div>
                </div>

                <!-- Nút quay lại -->
                <div class="text-center mt-3">
                    <a href="{{ route('admin.transaction-courses.index') }}" class="btn btn-secondary">
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
