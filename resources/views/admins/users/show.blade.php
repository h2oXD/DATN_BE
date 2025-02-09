@extends('layouts.master')

@section('title')
    Chi tiết người dùng
@endsection

@section('content')
    <div class="card">
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
                    @foreach ($user->roles->toArray() as $item)
                        @if ($item['name'] == 'lecturer')
                            <span class="badge bg-warning">Giảng viên</span>
                        @elseif($item['name'] == 'student')
                            <span class="badge bg-success">Học viên</span>
                        @endif
                    @endforeach
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-3"><strong>Ảnh đại diện:</strong></div>
                <div class="col-md-9">
                    <img src="{{ Storage::url($user->profile_picture) }}" class="rounded-circle" width="100"
                        height="100">
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
                <a href="{{ route('users.index') }}" class="btn btn-secondary">Quay lại</a>
            </div>
        </div>
    </div>
@endsection
