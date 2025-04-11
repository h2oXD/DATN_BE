@extends('layouts.master')

@section('title')
    Chỉnh sửa người dùng
@endsection

@section('content')
    <div class="card m-3">
        <div class="card-header bg-gradient-mix-shade d-flex justify-content-between align-content-center">
            <h2 class="text-white ">Chỉnh sửa người dùng</h2>
        </div>
        <div class="card-body">
            @if (session()->has('success') && session()->get('success'))
                <div class="alert alert-info">Thao tác thành công!</div>
            @endif

            <form class="row" method="POST" action="{{ route('admin.users.update', $user->id) }}"
                enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="mb-3 col-6">
                    <label for="name" class="form-label">Tên</label>
                    <input type="text" class="form-control" name="name" id="name"
                        value="{{ old('name', $user->name) }}" />
                    @error('name')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="mb-3 col-6">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" name="email" id="email"
                        value="{{ old('email', $user->email) }}" />
                    @error('email')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="mb-3 col-6">
                    <label for="phone_number" class="form-label">Số điện thoại</label>
                    <input type="text" class="form-control" name="phone_number" id="phone_number"
                        value="{{ old('phone_number', $user->phone_number) }}" />
                    @error('phone_number')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                @if ($user->roles->count() === 1 && $user->roles->first()->name === 'student')
                    <div class="mb-3 col-6">
                        <label for="role" class="form-label">Vai trò</label>
                        <select name="role" class="form-control">
                            <option value="student" selected>Học viên</option>
                            <option value="lecturer">Giảng viên</option>
                        </select>
                    </div>
                @endif

                <div class="mb-3 col-6">
                    <label for="password" class="form-label">Mật khẩu</label>
                    <input type="password" class="form-control" name="password" id="password" />
                </div>

                <div class="mb-3 col-6">
                    <label for="password_confirmation" class="form-label">Nhập lại mật khẩu</label>
                    <input type="password" class="form-control" name="password_confirmation" id="password_confirmation" />
                </div>

                <div class="mb-3 col-6">
                    <label for="status" class="form-label">Trạng thái tài khoản</label>
                    <select name="status" class="form-select">
                        <option value="0" {{ $user->status == 0 ? 'selected' : '' }}>Hoạt động</option>
                        <option value="1" {{ $user->status == 1 ? 'selected' : '' }}>Khóa chức năng giảng viên</option>
                        <option value="2" {{ $user->status == 2 ? 'selected' : '' }}>Khóa chức năng giảng viên và học
                            viên</option>
                    </select>
                </div>

                <div class="mb-3 col-7 ">
                    <label for="profile_picture" class="form-label">Ảnh đại diện</label>
                    <div class="d-flex align-items-center gap-3">
                        <img src="{{ $user->profile_picture ? asset('storage/' . $user->profile_picture) : asset('assets/images/avatar/avatar-3.jpg') }}"
                            alt="avatar" class="avatar-xl rounded-circle border border-4 border-white"
                            style="width: 50px; height: 50px; object-fit: cover;" />
                        <input type="file" class="form-control" name="profile_picture" id="profile_picture" />
                    </div>
                </div>

                <div class="text-end">
                    <a href="{{ url()->previous() }}" class="btn btn-secondary">Quay lại</a>
                    <button type="submit" class="btn btn-primary">Cập nhật</button>
                </div>

            </form>
        </div>
    </div>
@endsection
