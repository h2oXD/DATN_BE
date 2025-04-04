<div class="card">
    <div class="card-body p-3">
        <li class="list-group-item bg-transparent">
            <div class="row align-items-center">
                <div class="col">
                    <a href="#">
                        <div class="d-flex align-items-center flex-row gap-3">
                            <div class="text-body d-flex">
                                <p class="mb-0">
                                    <img src="/avatar-1.jpg" alt="" class="avatar-md rounded-circle">
                                <div class="d-flex flex-column ms-2">
                                    <span class="fw-bold mb-0 h5">{{ $course->user->name }}</span>
                                    <span class="fs-6">
                                        <span class="">Thời gian gửi: {{ $course->submited_at }}</span>
                                    </span>
                                </div>
                                </p>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-auto text-center d-flex gap-2">
                    <form id="approve-form" action="{{ route('admin.courses.approve', $course->id) }}" method="POST">
                        @csrf
                        <button  type="button" id="chapnhan" class="btn btn-outline-primary btn-sm">Chấp nhận</button>
                    </form>

                    <div>
                        <button class="btn btn-outline-danger btn-sm" data-bs-toggle="modal"
                            data-bs-target=".gd-example-modal-lg">Từ chối</button>
                    </div>
                </div>
            </div>
        </li>
    </div>
</div>
<div class="modal fade gd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4>Lý do từ chối khoá học này</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                </button>
            </div>
            <div class="modal-body px-2 py-2">
                <div class="px-3">
                    <p class="mb-1">1. Không phù hợp với nhu cầu hoặc mục tiêu</p>
                    <p class="mb-1">2. Học phí quá cao</p>
                    <p class="mb-1">3. Không đủ thời gian tham gia</p>
                    <p class="mb-1">4. Chất lượng khóa học không đảm bảo</p>
                    <p class="mb-1">5. Thiếu các yếu tố hỗ trợ học tập</p>
                </div>
                <form method="post" action="{{ route('admin.courses.reject', $course->id) }}"
                    class="d-flex flex-column mx-2">
                    <div class="">
                        @csrf
                        <label class="fw-bold" for="">Lý do</label>

                        <textarea placeholder="Nhập lý do từ chối" name="reason" id="reason" class="form-control" cols="30"
                            rows="5"></textarea>
                    </div>
                    <div class="mt-2 d-flex justify-content-end gap-2 me-1">
                        <button data-bs-dismiss="modal" type="button" class="btn btn-sm btn-danger">Huỷ</button>
                        <button class="btn btn-sm btn-primary" id="submitApproval" data-course-id="{{ $course->id }}"
                            data-action="reject">Gửi</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.getElementById("chapnhan").addEventListener("click", function() {
        Swal.fire({
            title: "Bạn có chắc chắn?",
            text: "Khóa học này sẽ được phê duyệt!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Đồng ý",
            cancelButtonText: "Hủy"
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById("approve-form").submit();
            }
        });
    });
</script>
