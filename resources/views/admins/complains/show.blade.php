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
            <div class="card-body p-4">

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

                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="card mb-4 shadow-sm">
                            <div class="card-header bg-gradient-mix-shade text-white">Thông tin người dùng</div>
                            <div class="card-body">
                                <p><strong>Tên:</strong>
                                    <span>{{ $complain->transaction_wallets->wallet->user->name }}</span>
                                </p>
                                <p><strong>Email:</strong>
                                    <span>{{ $complain->transaction_wallets->wallet->user->email }}</span>
                                </p>
                                <p><strong>Số điện thoại:</strong>
                                    <span>{{ $complain->transaction_wallets->wallet->user->phone_number }}</span>
                                </p>
                            </div>
                        </div>

                        <div class="card mb-4 shadow-sm">
                            <div class="card-header bg-gradient-mix-shade text-white">Thông tin giao dịch</div>
                            <div class="card-body">
                                <p><strong>Ngày gửi yêu cầu:</strong>
                                    <span>{{ Carbon\Carbon::parse($complain->transaction_wallets->transaction_date)->format('d/m/Y H:i:s') }}</span>
                                </p>
                                <p><strong>Ngày xác nhận:</strong>
                                    <span>{{ Carbon\Carbon::parse($complain->transaction_wallets->censor_date)->format('d/m/Y H:i:s') }}</span>
                                </p>
                                <p><strong>Số tiền rút:</strong>
                                    <span>{{ number_format($complain->transaction_wallets->amount) }} VND</span>
                                </p>
                                <p><strong>Số dư ví sau kiểm duyệt:</strong>
                                    <span>{{ number_format($complain->transaction_wallets->balance) }} VND</span>
                                </p>
                            </div>
                        </div>

                        <div class="card mb-4 shadow-sm">
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

                    <div class="col-md-6">
                        <div class="card mb-4 shadow-sm">
                            <div class="card-header bg-gradient-mix-shade text-white">Thông tin thanh toán</div>
                            <div class="card-body">
                                <p><strong>Ngân hàng:</strong> <span>{{ $complain->transaction_wallets->bank_name }}</span>
                                </p>
                                <p><strong>Tên người sở hữu:</strong>
                                    <span>{{ $complain->transaction_wallets->bank_nameUser }}</span>
                                </p>
                                <p><strong>Số tài khoản:</strong>
                                    <span>{{ $complain->transaction_wallets->bank_number }}</span>
                                </p>
                            </div>
                        </div>

                        <div class="card mb-4 shadow-sm">
                            <div class="card-header bg-gradient-mix-shade text-white">Thông tin khiếu nại</div>
                            <div class="card-body">
                                <p><strong>Ngày gửi yêu cầu khiếu nại:</strong>
                                    <span>{{ Carbon\Carbon::parse($complain->request_date)->format('d/m/Y H:i:s') }}</span>
                                </p>
                                <p><strong>Nội dung khiếu nại:</strong> <span>{{ $complain->description }}</span></p>
                                <p><strong>Trạng thái khiếu nại:</strong>
                                    @if ($complain->status === 'pending')
                                        <span class="badge bg-warning text-dark">Đang chờ duyệt</span>
                                    @endif
                                </p>
                            </div>
                        </div>

                        <div class="card mb-4 shadow-sm">
                            <div class="card-header bg-gradient-mix-shade text-white">Ảnh minh chứng</div>
                            <div class="card-body text-center">
                                @if ($complain->proof_img)
                                    <img src="{{ Storage::url($complain->proof_img) }}"
                                        class="img-fluid rounded zoomable-image" style="max-width: 150px;"
                                        data-bs-toggle="modal" data-bs-target="#imageModal">
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-center gap-3 mt-4">
                    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#confirmModal">
                        <i class="fas fa-check"></i> Đồng ý
                    </button>

                    <form action="{{ route('admin.censor-complain.reject', $complain->id) }}" method="POST"
                        style="display:inline-block;" id="update-complain-{{ $complain->id }}">
                        @csrf
                        @method('PUT')
                        <button type="button" class="btn btn-danger" onclick="confirmDelete({{ $complain->id }})">
                            <i class="fas fa-times"></i> Từ chối
                        </button>
                    </form>

                    <a href="{{ route('admin.censor-complain.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Quay lại
                    </a>
                </div>

            </div>
        </div>
    </div>

    {{-- Modal xác nhận với form nhập liệu --}}
    <div class="modal fade" id="confirmModal" tabindex="-1" role="dialog" aria-labelledby="confirmModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmModalLabel">Xác nhận khiếu nại</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="form-errors"></div>
                    <form action="/admin/censor-complains/{{ $complain->id }}/accept" method="POST" class="ajax-form"
                        data-id="{{ $complain->id }}">
                        @csrf
                        <div class="mb-3">
                            <label for="money_refund" class="form-label">Số tiền hoàn trả:</label>
                            <input type="number" class="form-control @error('money_refund') is-invalid @enderror"
                                id="money_refund" name="money_refund" value="{{ old('money_refund') }}">
                            @error('money_refund')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="feedback_by_admin" class="form-label">Phản hồi:</label>
                            <textarea class="form-control @error('feedback_by_admin') is-invalid @enderror" id="feedback_by_admin"
                                name="feedback_by_admin" rows="3">{{ old('feedback_by_admin') }}</textarea>
                            @error('feedback_by_admin')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <button type="button" class="btn btn-primary modal-btn-submit">Xác nhận</button>
                    </form>
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

@section('style')
    <style>
        .is-invalid {
            border-color: red;
        }

        .invalid-feedback {
            color: red;
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }
    </style>
@endsection

@section('script')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Từ chối khiếu nại
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
                    document.getElementById(`update-complain-${id}`).submit();
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

        // Ajax form
        $(document).ready(function() {
            $('.modal-btn-submit').click(function(e) {
                e.preventDefault();
                var form = $(this).closest('.ajax-form');
                var id = form.data('id'); // Lấy ID từ data-id

                var money_refund = $('#money_refund').val();
                var feedback_by_admin = $('#feedback_by_admin').val();
                console.log(money_refund, feedback_by_admin);

                if (id) {

                    var formData = form.serialize();
                    console.log(formData);

                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        url: `/admin/censor-complains/${id}/accept`, // Sử dụng đường dẫn trực tiếp từ route
                        method: 'POST',
                        data: {
                            money_refund,
                            feedback_by_admin
                        },
                        success: function(response) {
                            if (response.success) {
                                $('#confirmModal').modal('hide');
                                Swal.fire('Thành công', 'Khiếu nại đã được xác nhận.',
                                    'success');
                                // Thêm setTimeout để chuyển trang sau 2 giây
                                setTimeout(function() {
                                    window.location.href = '/admin/censor-complain';
                                }, 1000);
                            } else if (response.error) {
                                $('#form-errors').html('<div class="alert alert-danger">' +
                                    response.error + '</div>');
                            }
                        },
                        error: function(xhr) {
                            if (xhr.responseJSON && xhr.responseJSON.error) {
                                var errors = xhr.responseJSON.error;
                                var errorHtml = '<div class="alert alert-danger">';
                                $.each(errors, function(key, value) {
                                    errorHtml += '<p>' + value[0] + '</p>';
                                });
                                errorHtml += '</div>';
                                $('#form-errors').html(errorHtml);
                            } else {
                                $('#form-errors').html(
                                    '<div class="alert alert-danger">Lỗi server. Vui lòng thử lại.</div>'
                                );
                            }
                        }
                    });
                } else {
                    console.error('ID not found on form.');
                    alert('Lỗi: Không tìm thấy ID khiếu nại.');
                }
            });
        });

    </script>
@endsection
