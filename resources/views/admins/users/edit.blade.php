@extends('layouts.master')

@section('title')
    Chỉnh sửa người dùng
@endsection

@section('content')
    <div class="container my-5">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-content-center">
                <h2>Chỉnh sửa người dùng</h2>

            </div>
            <div class="card-body">
                @if (session()->has('success') && !session()->get('success'))
                    <div class="alert alert-danger">
                        {{ session()->get('error') }}
                    </div>
                @endif

                @if (session()->has('success') && session()->get('success'))
                    <div class="alert alert-info">Thao tác thành công!</div>
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

                <form class="row" method="POST" action="{{ route('users.update', $user->id) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="mb-3 col-6">
                        <label for="name" class="form-label">Tên</label>
                        <input type="text" class="form-control" name="name" id="name"
                            value="{{ old('name', $user->name) }}" required />
                    </div>

                    <div class="mb-3 col-6">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" name="email" id="email"
                            value="{{ old('email', $user->email) }}" required />
                    </div>

                    <div class="mb-3 col-6">
                        <label for="phone_number" class="form-label">Số điện thoại</label>
                        <input type="text" class="form-control" name="phone_number" id="phone_number"
                            value="{{ old('phone_number', $user->phone_number) }}" />
                    </div>

                    <div class="mb-3 col-6">
                        <label for="role" class="form-label">Vai trò</label>
                        <select name="role" class="form-control" required>
                            <option value="lecturer" {{ $user->roles->first()->name === 'lecturer' ? 'selected' : '' }}>
                                Giảng viên</option>
                            <option value="student" {{ $user->roles->first()->name === 'student' ? 'selected' : '' }}>Học
                                viên</option>
                        </select>
                    </div>

                    <div class="mb-3 col-6">
                        <label for="password" class="form-label">Mật khẩu</label>
                        <input type="password" class="form-control" name="password" id="password" />
                    </div>

                    <div class="mb-3 col-6">
                        <label for="password_confirmation" class="form-label">Nhập lại mật khẩu</label>
                        <input type="password" class="form-control" name="password_confirmation"
                            id="password_confirmation" />
                    </div>

                    <div class="mb-3 col-6">
                        <label for="profile_picture" class="form-label">Ảnh đại diện</label>
                        <div class="d-flex align-items-center gap-3">
                            <img src="{{ Storage::url($user->profile_picture) }}" class="rounded-circle" width="80"
                                height="80">
                            <input type="file" class="form-control" name="profile_picture" id="profile_picture" />
                        </div>
                    </div>
                    <div class="text-end">
                        <a href="{{ route('users.index') }}" class="btn btn-secondary">Quay lại</a>
                        <button type="submit" class="btn btn-primary">Cập nhật</button>
                    </div>
                </form>

            </div>

        </div>
    </div>
@endsection
