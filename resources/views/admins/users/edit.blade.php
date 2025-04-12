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

                <div class="mb-3 col-6">
                    <label for="password" class="form-label">Mật khẩu</label>
                    <input type="password" class="form-control" name="password" id="password" />
                </div>

                <div class="mb-3 col-6">
                    <label for="password_confirmation" class="form-label">Nhập lại mật khẩu</label>
                    <input type="password" class="form-control" name="password_confirmation" id="password_confirmation" />
                    @error('password')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="mb-3 col-6">
                    <label for="status" class="form-label">Trạng thái tài khoản</label>
                    <select name="status" class="form-select">
                        <option value="0" {{ $user->status == 0 ? 'selected' : '' }}>Hoạt động</option>
                        <option value="1" {{ $user->status == 1 ? 'selected' : '' }}>Khóa chức năng giảng viên</option>
                        <option value="2" {{ $user->status == 2 ? 'selected' : '' }}>Khóa chức năng giảng viên và học
                            viên</option>
                    </select>
                    @error('status')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="mb-3 col-6">
                    <label for="country" class="form-label">Quốc gia</label>
                    <input type="text" class="form-control" name="country" id="country"
                        value="{{ old('country', $user->country) }}" />
                    @error('country')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="mb-3 col-6">
                    <label for="province" class="form-label">Tỉnh/Thành phố</label>
                    <input type="text" class="form-control" name="province" id="province"
                        value="{{ old('province', $user->province) }}" />
                    @error('province')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="mb-3 col-6">
                    <label for="birth_date" class="form-label">Ngày sinh</label>
                    <input type="date" class="form-control" name="birth_date" id="birth_date"
                        value="{{ old('birth_date', $user->birth_date) }}" />
                    @error('birth_date')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="mb-3 col-6">
                    <label for="gender" class="form-label">Giới tính</label>
                    <select name="gender" class="form-select">
                        <option value="male" {{ $user->gender == 'male' ? 'selected' : '' }}>Nam</option>
                        <option value="female" {{ $user->gender == 'female' ? 'selected' : '' }}>Nữ</option>
                        <option value="other" {{ $user->gender == 'other' ? 'selected' : '' }}>Khác</option>
                    </select>
                    @error('gender')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="mb-3 col-6">
                    <label for="linkedin_url" class="form-label">LinkedIn URL</label>
                    <input type="text" class="form-control" name="linkedin_url" id="linkedin_url"
                        value="{{ old('linkedin_url', $user->linkedin_url) }}" />
                    @error('linkedin_url')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="mb-3 col-6">
                    <label for="website_url" class="form-label">Website URL</label>
                    <input type="text" class="form-control" name="website_url" id="website_url"
                        value="{{ old('website_url', $user->website_url) }}" />
                    @error('website_url')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="mb-3 col-6">
                    <label for="certificate_file" class="form-label">Tệp chứng chỉ</label>
                    <input type="file" class="form-control" name="certificate_file" id="certificate_file" />
                    @error('certificate_file')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="mb-3 col-6">
                    <label for="bank_name" class="form-label">Tên ngân hàng</label>
                    <input type="text" class="form-control" name="bank_name" id="bank_name"
                        value="{{ old('bank_name', $user->bank_name) }}" />
                    @error('bank_name')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="mb-3 col-6">
                    <label for="bank_nameUser" class="form-label">Tên người dùng</label>
                    <input type="text" class="form-control" name="bank_nameUser" id="bank_nameUser"
                        value="{{ old('bank_nameUser', $user->bank_nameUser) }}" />
                    @error('bank_nameUser')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="mb-3 col-6">
                    <label for="bank_number" class="form-label">Số tài khoản ngân hàng</label>
                    <input type="text" class="form-control" name="bank_number" id="bank_number"
                        value="{{ old('bank_number', $user->bank_number) }}" />
                    @error('bank_number')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="mb-3 col-7 ">
                    <label for="profile_picture" class="form-label">Ảnh đại diện</label>
                    <div class="d-flex align-items-center gap-3">
                        <img src="{{ $user->profile_picture ? asset('storage/' . $user->profile_picture) : asset('assets/images/avatar/avatar-3.jpg') }}"
                            alt="avatar" class="avatar-xl rounded-circle border border-4 border-white"
                            style="width: 50px; height: 50px; object-fit: cover;" />
                        <input type="file" class="form-control" name="profile_picture" id="profile_picture" />
                    </div>
                    @error('profile_picture')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="text-end">
                    <a href="{{ url()->previous() }}" class="btn btn-secondary">Quay lại</a>
                    <button type="submit" class="btn btn-primary">Cập nhật</button>
                </div>

            </form>
        </div>
    </div>
@endsection
