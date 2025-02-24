{{-- @extends('layouts.master')
@section('title')
    Kiểm duyệt khoá học - {{ $course->title }}
@endsection

@section('content')
    <div class="m-3">
        <div class="card">
            <div class="card-body pb-1">
                <h1>ABC</h1>
                <ul class="nav nav-lb-tab border-bottom-0" id="tab" role="tablist">
                    <li class="nav-item my-1" role="presentation">
                        <a class="nav-link p-0 active" id="courses-tab" data-bs-toggle="pill" href="#courses" role="tab"
                            aria-controls="courses" aria-selected="true">All</a>
                    </li>
                    <li class="nav-item my-1" role="presentation">
                        <a class="nav-link p-0 m-" id="approved-tab" data-bs-toggle="pill" href="#approved" role="tab"
                            aria-controls="approved" aria-selected="false" tabindex="-1">Approved</a>
                    </li>
                    <li class="nav-item my-1" role="presentation">
                        <a class="nav-link p-0 m-" id="pending-tab" data-bs-toggle="pill" href="#pending" role="tab"
                            aria-controls="pending" aria-selected="false" tabindex="-1">Pending</a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="card mt-2" id="tabContent">
            <div class="tab-pane fade show" id="courses" role="tabpanel" aria-labelledby="courses-tab">
                <h1>Course</h1>
            </div>
            <div class="tab-pane fade" id="approved" role="tabpanel" aria-labelledby="approved-tab">
                <h1>approved</h1>
            </div>
            <div class="tab-pane fade" id="pending" role="tabpanel" aria-labelledby="pending-tab">
                <h1>pending</h1>
            </div>
        </div>
    </div>
@endsection --}}

@extends('layouts.master')

@section('title')
    Kiểm duyệt người dùng - {{ $lecturerRegister->user->name }}
@endsection

@section('content')
    <div class="position-fixed top-0 end-0 p-3" style="z-index: 1050">
        <div id="toastWrapper"></div>
    </div>
    <div class="m-3">
        <div class="card">
            <div class="card-body pb-1 pt-3 px-3">
                <li class="list-group-item bg-transparent pb-2">
                    <div class="row align-items-center">
                        <div class="col">
                            <a href="#">
                                <div class="d-flex align-items-center flex-row gap-3">
                                    @if ($lecturerRegister->user->profile_picture)
                                        <img src="{{ Storage::url($lecturerRegister->user->profile_picture) }}"
                                            alt="" class="avatar-lg rounded">
                                    @else
                                        <img src="/avatar-1.jpg" alt="" class="avatar-lg rounded">
                                    @endif

                                    <div class="text-body">
                                        <p class="mb-0">
                                            <span class="fw-bold mb-0 h5">{{ $lecturerRegister->user->name }}</span>
                                            <span class="ms-1 fs-6">{{ $lecturerRegister->user->email }}</span>
                                        </p>
                                        <span class="fs-6">
                                            <span class="ms-1">Thời gian gửi: {{ $lecturerRegister->updated_at }}</span>
                                        </span>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-auto text-center">
                            @if ($lecturerRegister->status == 'approved')
                                <span class="badge bg-success">Đã duyệt</span>
                            @elseif ($lecturerRegister->status == 'rejected')
                                <span class="badge bg-danger">Từ chối</span>
                            @else
                                <form action="{{ route('admin.lecturer-approvals.approve', $lecturerRegister->id) }}"
                                    method="POST" class="d-inline" id="approveForm">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-primary btn-sm">
                                        Chấp nhận
                                    </button>
                                </form>
                                <button type="button" class="btn btn-outline-danger btn-sm" data-bs-toggle="modal"
                                    data-bs-target="#rejectModal">
                                    Từ chối
                                </button>
                            @endif
                        </div>
                    </div>
                </li>
                <ul class="nav nav-lb-tab border-bottom-0 pb-2 pt-2 border-top" id="tab" role="tablist">
                    <li class="nav-item my-1" role="presentation">
                        <a class="nav-link p-0 active" id="courses-tab" data-bs-toggle="pill" href="#courses" role="tab"
                            aria-controls="courses" aria-selected="true">Thông tin người dùng</a>
                    </li>
                </ul>
            </div>
        </div>

        <div class="card mt-2" id="tabContent">
            <div class="tab-content">
                <!-- Tab All -->
                <div class="tab-pane fade show active" id="courses" role="tabpanel" aria-labelledby="courses-tab">

                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h5 class="text-primary"><i class="bi bi-person-circle me-2"></i> Thông tin giảng viên</h5>
                                <hr>
                                
                                <div class="card bg-white ps-4 py-4 mx-auto" style="max-width: 600px;">
                                    <div class="d-flex flex-column align-items-center">
                                        <p class="fs-5 d-flex align-items-center w-100">
                                            <i class="bi bi-person-fill me-3 fs-4"></i>
                                            <strong class="flex-shrink-0" style="width: 120px;">Tên:</strong>
                                            <span class="flex-grow-1 text-start">{{ $lecturerRegister->user->name }}</span>
                                        </p>
                                        <p class="fs-5 d-flex align-items-center w-100">
                                            <i class="bi bi-envelope-fill me-3 fs-4"></i>
                                            <strong class="flex-shrink-0" style="width: 120px;">Email:</strong>
                                            <span class="flex-grow-1 text-start">{{ $lecturerRegister->user->email }}</span>
                                        </p>
                                        <p class="fs-5 d-flex align-items-center w-100">
                                            <i class="bi bi-card-text me-3 fs-4"></i>
                                            <strong class="flex-shrink-0" style="width: 120px;">Tiểu sử:</strong>
                                            <span class="flex-grow-1 text-start">{{ $lecturerRegister->user->bio ?? 'Chưa cập nhật' }}</span>
                                        </p>
                                        <p class="fs-5 d-flex align-items-center w-100">
                                            <i class="bi bi-calendar2-date-fill me-3 fs-4"></i>
                                            <strong class="flex-shrink-0" style="width: 120px;">Ngày tạo:</strong>
                                            <span class="flex-grow-1 text-start">{{ $lecturerRegister->created_at->format('d-m-Y H:i') }}</span>
                                        </p>
                                        <p class="fs-5 d-flex align-items-center w-100">
                                            <i class="bi bi-calendar2-plus-fill me-3 fs-4"></i>
                                            <strong class="flex-shrink-0" style="width: 120px;">Ngày cập nhật:</strong>
                                            <span class="flex-grow-1 text-start">{{ $lecturerRegister->updated_at->format('d-m-Y H:i') }}</span>
                                        </p>
                                    </div>
                                </div>
                                
                                
                                
                                

                                </p>
                            </div>
                            <div class="col-md-6">
                                <h5 class="text-success"><i class="bi bi-file-earmark-text me-2"></i> Câu trả lời</h5>
                                <hr>
                                <div class="card p-3 bg-light">
                                    <ul class="list-unstyled mb-0">
                                        <li class="mb-2"><i class="bi bi-chat-left-quote text-primary me-2"></i>
                                            {{ $lecturerRegister->answer1 }}</li>
                                        <li class="mb-2"><i class="bi bi-chat-left-quote text-success me-2"></i>
                                            {{ $lecturerRegister->answer2 }}</li>
                                        <li><i class="bi bi-chat-left-quote text-danger me-2"></i>
                                            {{ $lecturerRegister->answer3 }}</li>
                                    </ul>
                                </div>
                                <h5 class="text-warning mt-4"><i class="bi bi-award me-2"></i> Chứng chỉ</h5>
                                <hr>
                                @if ($lecturerRegister->user->certificate_file)
                                    <img src="{{ Storage::url($lecturerRegister->user->certificate_file) }}"
                                        class="img-fluid rounded shadow" width="250px">
                                @else
                                    <p class="text-muted">Chưa có chứng chỉ</p>
                                @endif
                            </div>
                        </div>

                        @if ($lecturerRegister->admin_rejection_reason)
                            <div class="alert alert-danger d-flex ">
                                <strong><i class="bi bi-x-circle"></i> Lý do từ chối:&nbsp;</strong>
                                <span>{!! $lecturerRegister->admin_rejection_reason !!}</span>
                            </div>
                        @endif

                        {{-- <div class="row mb-3">
                            <div class="col-md-3"><strong>Trạng thái:</strong></div>
                            <div class="col-md-9">
                                @if ($lecturerRegister->status === 'pending')
                                    <span class="badge bg-warning">Chờ duyệt</span>
                                @elseif ($lecturerRegister->status === 'approved')
                                    <span class="badge bg-success">Đã duyệt</span>
                                @elseif ($lecturerRegister->status === 'rejected')
                                    <span class="badge bg-danger">Từ chối</span>
                                @endif
                            </div>
                        </div> --}}

                        <div class="text-end">
                            <a href="{{ route('admin.lecturer_registers.index') }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Quay lại
                            </a>
                        </div>
                    </div>

                </div>




            </div>

            <!-- Tab Approved -->

            <!-- Tab kiểm tra  -->

        </div>
    </div>
    </div>

    <div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content shadow-lg rounded-3">
                <!-- Header -->
                <div class="modal-header text-white" style="background: linear-gradient(135deg, #A78BFA, #D8B4FE);">
                    <h5 class="modal-title fw-bold" id="rejectModalLabel">
                        <i class="bi bi-x-circle me-2"></i> Từ chối yêu cầu giảng viên
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>

                <!-- Body -->
                <div class="modal-body bg-light">
                    <form id="rejectForm" action="{{ route('admin.lecturer-approvals.reject', $lecturerRegister->id) }}"
                        method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="rejectionReason" class="form-label fw-semibold">
                                Lý do từ chối <span class="text-danger">*</span>
                            </label>
                            <textarea id="rejectionReason" name="admin_rejection_reason" class="form-control"></textarea>
                        </div>
                        <div class="text-end">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                <i class="bi bi-arrow-left-circle me-1"></i> Hủy
                            </button>
                            <button type="submit" class="btn text-white" style="background: #A78BFA;">
                                <i class="bi bi-send me-1"></i> Gửi từ chối
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.0/tinymce.min.js"></script>
    <script>
        tinymce.init({
            selector: '#rejectionReason',
            height: 250,
            menubar: false,
            plugins: 'lists link code emoticons',
            toolbar: 'undo redo | bold italic underline | alignleft aligncenter alignright | bullist numlist | link emoticons',
            content_style: 'body { font-family: Arial, sans-serif; font-size: 14px; background-color: #f8f9fa; }'
        });
    </script>

    <!-- Script hiển thị thông báo -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            function showToast(message, type = "success") {
                let icon = type === "success" ? "✔️" : "❌";
                let borderColor = type === "success" ? "border-success" : "border-danger";
                let progressColor = type === "success" ? "bg-success" : "bg-danger";

                let toast = document.createElement("div");
                toast.className = `toast align-items-center show border ${borderColor} shadow-lg mb-2`;
                toast.setAttribute("role", "alert");
                toast.setAttribute("aria-live", "assertive");
                toast.setAttribute("aria-atomic", "true");
                toast.style.maxWidth = "350px"; // Giới hạn chiều rộng cho đẹp
                toast.style.overflow = "hidden"; // Tránh bị tràn nội dung

                toast.innerHTML = `
                    <div class="d-flex p-3 bg-white rounded">
                        <div class="me-3 fs-4">${icon}</div>
                        <div class="flex-grow-1">
                            <strong>${message}</strong>
                            <div class="progress mt-2" style="height: 4px;">
                                <div class="progress-bar ${progressColor}" role="progressbar" 
                                     style="width: 100%;" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                        <button type="button" class="btn-close ms-3" data-bs-dismiss="toast" aria-label="Close"></button>
                    </div>
                `;

                document.getElementById("toastWrapper").appendChild(toast);

                // Giảm thanh tiến trình dần trong 3 giây
                setTimeout(() => {
                    toast.querySelector(".progress-bar").style.transition = "width 3s linear";
                    toast.querySelector(".progress-bar").style.width = "0%";
                }, 50);

                // Ẩn toast sau 3 giây
                setTimeout(() => {
                    toast.classList.remove("show");
                    setTimeout(() => toast.remove(), 500);
                }, 3000);
            }

            // Kiểm tra session Laravel để hiển thị toast
            @if (session('success'))
                showToast("{{ session('success') }}", "success");
            @endif

            @if (session('error'))
                showToast("{{ session('error') }}", "error");
            @endif
        });
    </script>
@endsection
