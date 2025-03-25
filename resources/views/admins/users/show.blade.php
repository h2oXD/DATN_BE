@extends('layouts.master')

@section('title')
    Chi tiết người dùng
@endsection

@section('content')
    <div class="card m-3">
        <div class="card-header d-flex justify-content-between align-content-center">
            <h2>Chi tiết người dùng</h2>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-3"><strong>ID:</strong></div>
                <div class="col-md-9">{{ $user->id }}</div>
            </div>
            <div class="row mb-3">
                <div class="col-md-3"><strong>Tên:</strong></div>
                <div class="col-md-9">{{ $user->name }}</div>
            </div>
            <div class="row mb-3">
                <div class="col-md-3"><strong>Email:</strong></div>
                <div class="col-md-9">{{ $user->email }}</div>
            </div>
            <div class="row mb-3">
                <div class="col-md-3"><strong>Số điện thoại:</strong></div>
                <div class="col-md-9">{{ $user->phone_number }}</div>
            </div>
            <div class="row mb-3">
                <div class="col-md-3"><strong>Vai trò:</strong></div>
                <div class="col-md-9">
                    @if ($user->roles->contains('name', 'lecturer'))
                        <span class="badge bg-warning">Giảng viên</span>
                    @elseif ($user->roles->contains('name', 'student') && !$user->roles->contains('name', 'lecturer'))
                        <span class="badge bg-success">Học viên</span>
                    @endif
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-3"><strong>Ảnh đại diện:</strong></div>
                <div class="col-md-9">
                    <img src="{{ $user->profile_picture ? asset($user->profile_picture) : asset('assets/images/avatar/avatar-3.jpg') }}"" alt="avatar"
                    class="avatar-xl rounded-circle border border-4 border-white"
                    style="width: 100px; height: 100px; object-fit: cover;" alt="avatar" />
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-3"><strong>Ngày tạo:</strong></div>
                <div class="col-md-9">{{ $user->created_at->format('d-m-Y H:i') }}</div>
            </div>
            <div class="row mb-3">
                <div class="col-md-3"><strong>Ngày cập nhật:</strong></div>
                <div class="col-md-9">{{ $user->updated_at->format('d-m-Y H:i') }}</div>
            </div>
            <div class="text-end">
                <a href="{{ route($user->roles->contains('name', 'lecturer') ? 'admin.lecturers.index' : 'admin.students.index') }}" class="btn btn-secondary">Quay lại</a>
            </div>
        </div>
    </div>
@endsection
