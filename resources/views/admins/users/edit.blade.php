@extends('layouts.master')

@section('title')
    Chỉnh sửa người dùng
@endsection

@section('content')
    <h1>Chỉnh sửa người dùng</h1>

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

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="container">
        <form method="POST" action="{{ route('users.update', $user->id) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="mb-3 row">
                <label for="name" class="col-4 col-form-label">Tên</label>
                <div class="col-8">
                    <input type="text" class="form-control" name="name" id="name"
                        value="{{ old('name', $user->name) }}" required />
                </div>
            </div>

            <div class="mb-3 row">
                <label for="email" class="col-4 col-form-label">Email</label>
                <div class="col-8">
                    <input type="email" class="form-control" name="email" id="email"
                        value="{{ old('email', $user->email) }}" required />
                </div>
            </div>

            <div class="mb-3 row">
                <label for="phone_number" class="col-4 col-form-label">Số điện thoại</label>
                <div class="col-8">
                    <input type="text" class="form-control" name="phone_number" id="phone_number"
                        value="{{ old('phone_number', $user->phone_number) }}" />
                </div>
            </div>

            <div class="mb-3 row">
                <label for="role" class="col-4 col-form-label">Vai trò</label>
                <div class="col-8">
                    <select name="role" class="form-control" required>
                        <option value="lecturer" {{ $user->roles->first()->name === 'lecturer' ? 'selected' : '' }}>Giảng
                            viên</option>
                        <option value="student" {{ $user->roles->first()->name === 'student' ? 'selected' : '' }}>Học viên
                        </option>
                    </select>
                </div>
            </div>

            <div class="mb-3 row">
                <label for="profile_picture" class="col-4 col-form-label">Ảnh đại diện</label>
                <div class="col-8">
                    <img src="{{ Storage::url($user->profile_picture) }}" width="100">
                    <input type="file" class="form-control" name="profile_picture" id="profile_picture" />
                </div>
            </div>

            <div class="mb-3 row">
                <label for="password" class="col-4 col-form-label">Mật khẩu</label>
                <div class="col-8">
                    <input type="password" class="form-control" name="password" id="password" />
                </div>
            </div>

            <div class="mb-3 row">
                <label for="password_confirmation" class="col-4 col-form-label">Nhập lại mật khẩu</label>
                <div class="col-8">
                    <input type="password" class="form-control" name="password_confirmation" id="password_confirmation" />
                </div>
            </div>

            <div class="mb-3 row">
                <div class="offset-sm-4 col-sm-8">
                    <button type="submit" class="btn btn-primary">Cập nhật</button>
                </div>
            </div>
        </form>
    </div>
@endsection
