@extends('layouts.master')

@section('title')
    Chi tiết yêu cầu đăng ký giảng viên
@endsection

@section('content')
    <div class="card m-3">
        <div class="card-header d-flex justify-content-between align-content-center">
            <h2>Chi tiết yêu cầu đăng ký giảng viên</h2>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-3"><strong>ID:</strong></div>
                <div class="col-md-9">{{ $lecturerRegister->id }}</div>
            </div>
            <div class="row mb-3">
                <div class="col-md-3"><strong>Tên:</strong></div>
                <div class="col-md-9">{{ $lecturerRegister->user->name }}</div>
            </div>
            <div class="row mb-3">
                <div class="col-md-3"><strong>Email:</strong></div>
                <div class="col-md-9">{{ $lecturerRegister->user->email }}</div>
            </div>
            <div class="row mb-3">
                <div class="col-md-3"><strong>Câu trả lời:</strong></div>
                <div class="col-md-9">{{ $lecturerRegister->lecturer_answers }}</div>
            </div>
            <div class="row mb-3">
                <div class="col-md-3"><strong>Tiểu sử:</strong></div>
                <div class="col-md-9">{{ $lecturerRegister->user->bio }}</div>
            </div>
            <div class="row mb-3">
                <div class="col-md-3"><strong>Chứng chỉ:</strong></div>
                <div class="col-md-9">
                    @if ($lecturerRegister->user->certificate_file)
                        <img src="{{ Storage::url($lecturerRegister->user->certificate_file) }}" width="200px" height="100px">
                    @else
                        <p>Chưa có chứng chỉ</p>
                    @endif
                </div>
            </div>
            @if ($lecturerRegister->admin_rejection_reason)
                <div class="row mb-3">
                    <div class="col-md-3"><strong>Lý do từ chối:</strong></div>
                    <div class="col-md-9">{{ $lecturerRegister->admin_rejection_reason }}</div>
                </div>
            @endif
            <div class="row mb-3">
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
            </div>
            <div class="row mb-3">
                <div class="col-md-3"><strong>Ngày tạo:</strong></div>
                <div class="col-md-9">{{ $lecturerRegister->created_at->format('d-m-Y H:i') }}</div>
            </div>
            <div class="row mb-3">
                <div class="col-md-3"><strong>Ngày cập nhật:</strong></div>
                <div class="col-md-9">{{ $lecturerRegister->updated_at->format('d-m-Y H:i') }}</div>
            </div>
            <div class="text-end">
                <a href="{{ route('admin.lecturer_registers.index') }}" class="btn btn-secondary">Quay lại</a>
            </div>
        </div>
    </div>
@endsection
