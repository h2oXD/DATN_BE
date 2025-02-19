@extends('layouts.master')

@section('title')
    Danh sách yêu cầu đăng ký giảng viên
@endsection

@section('content')
    <div class="card m-3">
        <div class="card-header d-flex justify-content-between align-content-center">
            <h2 class="m-0">Danh sách yêu cầu đăng ký giảng viên</h2>
        </div>
        
        <div class="card-body p-0">
            @if (session()->has('success') &&!session()->get('success'))
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

            <div class="table-responsive">
                <table class="table mb-3 text-nowrap table-hover table-centered">
                    <thead class="table-light">
                        <tr>
                            <th scope="col">Tên</th>
                            <th scope="col">Email</th>
                            <th scope="col">Trạng thái</th>
                            <th scope="col">Ngày yêu cầu</th>
                            <th scope="col"></th> 
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($lecturerRegisters as $lecturerRegister)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center flex-row gap-2">
                                        <div class="position-relative">
                                            <img src="{{ Storage::url($lecturerRegister->user->profile_picture) }}" alt="avatar" class="rounded-circle avatar-md">
                                            <a href="#" class="position-absolute mt-5 ms-n4">
                                                <span class="status bg-success"></span> </a>
                                        </div>
                                        <h5 class="mb-0">{{ $lecturerRegister->user->name }}</h5>
                                    </div>
                                </td>
                                <td>{{ $lecturerRegister->user->email }}</td>
                                <td>
                                    @if ($lecturerRegister->status === 'pending')
                                        <span class="badge bg-warning">Chờ duyệt</span>
                                    @elseif ($lecturerRegister->status === 'approved')
                                        <span class="badge bg-success">Đã duyệt</span>
                                    @elseif ($lecturerRegister->status === 'rejected')
                                        <span class="badge bg-danger">Từ chối</span>
                                    @endif
                                </td>
                                <td>{{ $lecturerRegister->created_at }}</td>
                                <td>
                                    <span class="dropdown dropstart">
                                        <a class="btn-icon btn btn-ghost btn-sm rounded-circle" href="#" role="button" data-bs-toggle="dropdown" data-bs-offset="-20,20" aria-expanded="false">
                                            <i class="fe fe-more-vertical"></i>
                                        </a>
                                        <span class="dropdown-menu">
                                            <span class="dropdown-header">Hành động</span>
                                            <a href="{{ route('admin.lecturer_registers.show', $lecturerRegister->id) }}" class="dropdown-item">
                                                <svg class="w-10 me-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0.639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                                </svg>Xem
                                            </a>
                                            @if ($lecturerRegister->status === 'pending')
                                                <a class="dropdown-item" href="">
                                                    <svg class="w-10 me-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0z" />
                                                    </svg>
                                                    Duyệt
                                                </a>
                                                <a href="javascript:void(0)" class="dropdown-item" onclick="confirmReject({{ $lecturerRegister->id }})">
                                                    <svg class="w-10 me-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0z" />
                                                    </svg>
                                                    Từ chối
                                                </a>
                                            @endif 
                                        </span>
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

            </div>
        </div>
    </div>

    <div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="rejectModalLabel">Lý do từ chối</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="rejectForm" action="" method="POST"> 
                        @csrf
                        @method('POST') 
                        <div class="form-group">
                            <label for="reason">Lý do:</label>
                            <textarea name="reason" id="reason" class="form-control" required></textarea>
                            @error('reason')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <button type="submit" class="btn btn-primary mt-3">Gửi</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function confirmReject(id) {
            Swal.fire({
                title: "Bạn có chắc chắn muốn từ chối?",
                text: "Hành động này không thể hoàn tác!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "Từ chối",
                cancelButtonText: "Hủy"
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('rejectForm').action = ``;

                    const rejectModal = new bootstrap.Modal(document.getElementById('rejectModal'));
                    rejectModal.show();
                }
            });
        }
    </script>
@endsection