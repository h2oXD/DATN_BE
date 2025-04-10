@extends('layouts.master')

@section('title')
    Thêm mới người dùng
@endsection

@section('content')
    <div class="card m-3">
        <div class="card-header bg-gradient-mix-shade ">
            <h2 class="m-0 text-white ">Thêm mới người dùng</h2>
        </div>
        <div class="card-body">
            @if (session()->has('success') && !session()->get('success'))
                <div class="alert alert-danger">
                    {{ session()->get('error') }}
                </div>
            @endif

            @if (session()->has('success') && session()->get('success'))
                <div class="alert alert-info">
                    Thao tác thành công!
                </div>
            @endif
            <form method="POST" action="{{ route('admin.users.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="col-lg-4 mb-lg-3 col-12 mb-3">
                        <label for="name" class="form-label">Tên</label>
                        <input placeholder="Nhập tên" type="text" class="form-control" name="name" id="name"
                            value="{{ old('name') }}" />
                        @error('name')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="col-lg-4 mb-lg-3 col-12 mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input placeholder="Nhập Email" type="" class="form-control" name="email" id="email"
                            value="{{ old('email') }}" />
                        @error('email')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="col-lg-4 mb-lg-3 col-12 mb-3">
                        <label for="phone_number" class="form-label">Số điện thoại</label>
                        <input placeholder="Nhập số điện thoại" type="text" class="form-control" name="phone_number"
                            id="phone_number" value="{{ old('phone_number') }}" />
                        @error('phone_number')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="col-lg-4 mb-lg-3 col-12 mb-3">
                        <label for="role" class="form-label">Vai trò</label>
                        <select name="role" class="form-select text-dark">
                            <option value="">--Chọn-vai-trò--</option>
                            <option value="lecturer">Giảng viên</option>
                            <option value="student">Học viên</option>
                        </select>
                        @error('role')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="col-lg-4 mb-lg-3 col-12 mb-3">
                        <label for="password" class="form-label">Mật khẩu</label>
                        <input placeholder="Nhập mật khẩu" type="password" class="form-control" name="password"
                            id="password" />
                        @error('password')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="col-lg-4 mb-lg-3 col-12 mb-3">
                        <label for="password_confirmation" class="form-label">Nhập lại mật khẩu</label>
                        <input type="password" placeholder="Xác nhận mật khẩu" class="form-control"
                            name="password_confirmation" id="password_confirmation" />
                        @error('password_confirmation')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="col-lg-4 mb-lg-3 col-12 mb-3">
                        <label for="profile_picture" class="form-label">Ảnh đại diện</label>
                        <input type="file" class="form-control" name="profile_picture" id="profile_picture" />
                        @error('profile_picture')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">Thêm mới</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
