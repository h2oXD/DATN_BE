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
                        <input placeholder="Nhập Email" type="email" class="form-control" name="email" id="email"
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
                        @error('password.confirmation')
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

                    <!-- Bio -->
                    <div class="col-lg-4 mb-lg-3 col-12 mb-3">
                        <label for="bio" class="form-label">Tiểu sử</label>
                        <textarea placeholder="Nhập tiểu sử" class="form-control" name="bio" id="bio">{{ old('bio') }}</textarea>
                        @error('bio')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Status -->
                    {{-- <div class="col-lg-4 mb-lg-3 col-12 mb-3">
                        <label for="status" class="form-label">Trạng thái</label>
                        <select name="status" class="form-select text-dark">
                            <option value="0" {{ old('status') == 0 ? 'selected' : '' }}>Hoạt động</option>
                            <option value="1" {{ old('status') == 1 ? 'selected' : '' }}>Khóa giảng viên</option>
                            <option value="2" {{ old('status') == 2 ? 'selected' : '' }}>Khóa cả giảng viên và học
                                viên</option>
                        </select>
                        @error('status')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div> --}}

                    <!-- Country -->
                    <div class="col-lg-4 mb-lg-3 col-12 mb-3">
                        <label for="country" class="form-label">Quốc gia</label>
                        <input placeholder="Nhập quốc gia" type="text" class="form-control" name="country"
                            id="country" value="{{ old('country') }}" />
                        @error('country')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Province -->
                    <div class="col-lg-4 mb-lg-3 col-12 mb-3">
                        <label for="province" class="form-label">Tỉnh/Thành phố</label>
                        <input placeholder="Nhập tỉnh thành" type="text" class="form-control" name="province"
                            id="province" value="{{ old('province') }}" />
                        @error('province')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Birth Date -->
                    <div class="col-lg-4 mb-lg-3 col-12 mb-3">
                        <label for="birth_date" class="form-label">Ngày sinh</label>
                        <input placeholder="Chọn ngày sinh" type="date" class="form-control" name="birth_date"
                            id="birth_date" value="{{ old('birth_date') }}" />
                        @error('birth_date')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Gender -->
                    <div class="col-lg-4 mb-lg-3 col-12 mb-3">
                        <label for="gender" class="form-label">Giới tính</label>
                        <select name="gender" class="form-select text-dark">
                            <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Nam</option>
                            <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Nữ</option>
                            <option value="other" {{ old('gender') == 'other' ? 'selected' : '' }}>Khác</option>
                        </select>
                        @error('gender')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- LinkedIn URL -->
                    <div class="col-lg-4 mb-lg-3 col-12 mb-3">
                        <label for="linkedin_url" class="form-label">LinkedIn URL</label>
                        <input placeholder="Nhập LinkedIn URL" type="url" class="form-control" name="linkedin_url"
                            id="linkedin_url" value="{{ old('linkedin_url') }}" />
                        @error('linkedin_url')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Website URL -->
                    <div class="col-lg-4 mb-lg-3 col-12 mb-3">
                        <label for="website_url" class="form-label">Website URL</label>
                        <input placeholder="Nhập Website URL" type="url" class="form-control" name="website_url"
                            id="website_url" value="{{ old('website_url') }}" />
                        @error('website_url')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Certificate File -->
                    <div class="col-lg-4 mb-lg-3 col-12 mb-3">
                        <label for="certificate_file" class="form-label">Tệp chứng chỉ</label>
                        <input type="file" class="form-control" name="certificate_file" id="certificate_file" />
                        @error('certificate_file')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Bank Info -->
                    <div class="col-lg-4 mb-lg-3 col-12 mb-3">
                        <label for="bank_name" class="form-label">Tên ngân hàng</label>
                        <input placeholder="Nhập tên ngân hàng" type="text" class="form-control" name="bank_name"
                            id="bank_name" value="{{ old('bank_name') }}" />
                        @error('bank_name')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-lg-4 mb-lg-3 col-12 mb-3">
                        <label for="bank_nameUser" class="form-label">Tên người dùng</label>
                        <input placeholder="Nhập tên người dùng ngân hàng" type="text" class="form-control"
                            name="bank_nameUser" id="bank_nameUser" value="{{ old('bank_nameUser') }}" />
                        @error('bank_nameUser')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-lg-4 mb-lg-3 col-12 mb-3">
                        <label for="bank_number" class="form-label">Số tài khoản ngân hàng</label>
                        <input placeholder="Nhập số tài khoản ngân hàng" type="text" class="form-control"
                            name="bank_number" id="bank_number" value="{{ old('bank_number') }}" />
                        @error('bank_number')
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
