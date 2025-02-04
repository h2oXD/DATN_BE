@extends('layouts.master')

@section('title')
    Chi tiết người dùng
@endsection

@section('content')
    <h1>Chi tiết User</h1>

    <p><strong>ID:</strong> {{ $user->id }}</p>
    <p><strong>Tên:</strong> {{ $user->name }}</p>
    <p><strong>Email:</strong> {{ $user->email }}</p>
    <p><strong>Số điện thoại:</strong> {{ $user->phone_number }}</p>
    <p><strong>Vai trò:</strong> {{ $user->roles->pluck('name')->join(', ') }}</p>
    <p><strong>Ảnh đại diện:</strong></p>
    <img src="{{ Storage::url($user->profile_pictures) }}" width="150">
    <p><strong>Ngày tạo:</strong> {{ $user->created_at->format('d-m-Y H:i') }}</p>
    <p><strong>Ngày cập nhật:</strong> {{ $user->updated_at->format('d-m-Y H:i') }}</p>

    <a href="{{ route('users.index') }}" class="btn btn-secondary">Quay lại</a>
@endsection
