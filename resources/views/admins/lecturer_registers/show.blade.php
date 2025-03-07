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

                    <div class="container py-5">
                        <div class="row">
                            <!-- Thông tin giảng viên -->
                            <div class="col-lg-6">
                                <div class="card shadow-lg border-0">
                                    <div class="card-body text-center">
                                        <!-- Avatar -->
                                        <img src="{{ $lecturerRegister->user->profile_picture ? Storage::url($lecturerRegister->user->profile_picture) : '/default-avatar.png' }}" 
                                             class="rounded-circle border border-3 border-primary shadow-sm mb-3" width="120" height="120">
                                        
                                        <h4 class="fw-bold">{{ $lecturerRegister->user->name }}</h4>
                                        <p class="text-muted">{{ $lecturerRegister->user->bio ?? 'Chưa có thông tin' }}</p>
                                        
                                        <span class="badge bg-primary">
                                            <i class="bi bi-envelope"></i> {{ $lecturerRegister->user->email }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                    
                            <!-- Câu trả lời & Chứng chỉ -->
                            <div class="col-lg-6">
                                <div class="card shadow-sm border-0 mb-3">
                                    <div class="card-header bg-success bg-gradient text-dark text-center">
                                        <i class="bi bi-chat-left-text"></i> Câu trả lời của giảng viên
                                    </div>
                                    <div class="card-body">
                                        <ul class="list-group list-group-flush">
                                            <li class="list-group-item">
                                                <i class="bi bi-check-circle-fill text-success"></i> {{ $lecturerRegister->answer1 }}
                                            </li>
                                            <li class="list-group-item">
                                                <i class="bi bi-check-circle-fill text-primary"></i> {{ $lecturerRegister->answer2 }}
                                            </li>
                                            <li class="list-group-item">
                                                <i class="bi bi-check-circle-fill text-danger"></i> {{ $lecturerRegister->answer3 }}
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                    
                                <div class="card shadow-sm border-0">
                                    <div class="card-header text-dark bg-warning text-center">
                                        <i class="bi bi-award"></i> Chứng chỉ giảng viên
                                    </div>
                                    <div class="card-body text-center">
                                        @if ($lecturerRegister->user->certificate_file)
                                            <img src="{{ Storage::url($lecturerRegister->user->certificate_file) }}" class="img-fluid rounded shadow-sm">
                                        @else
                                            <p class="text-muted">Chưa có chứng chỉ</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    
                        <!-- Lý do từ chối -->
                        @if ($lecturerRegister->admin_rejection_reason)
                            <div class="alert alert-danger mt-4">
                                <i class="bi bi-x-circle"></i> <strong>Lý do từ chối:</strong> {!! $lecturerRegister->admin_rejection_reason !!}
                            </div>
                        @endif
                    
                        <!-- Nút quay lại -->
                        <div class="text-end mt-3">
                            <a href="{{ route('admin.lecturer_registers.index') }}" class="btn btn-lg btn-outline-secondary">
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
