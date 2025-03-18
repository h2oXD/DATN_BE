@extends('layouts.master')

@section('content')
    <!-- Page Content -->
    <main>
        <section class="pt-5 pb-5">
            <div class="container">
                <!-- Content -->
                <div class="row align-items-center">
                    <div class="col-xl-12 col-lg-12 col-md-12 col-12">
                        <div class="rounded-top"
                            style="background: linear-gradient(45deg, #6f42c1, #8a4cf0); height: 150px; border-radius: 10px 10px 0 0;">
                        </div>
                        <div class="card px-4 pt-2 pb-4 shadow-sm rounded-top-0 rounded-bottom-0 rounded-bottom-md-2">
                            <div class="d-flex align-items-end justify-content-between">
                                <div class="d-flex align-items-center">
                                    <div class="me-2 position-relative d-flex justify-content-end align-items-end mt-n5">
                                        <img src="{{ $user->profile_picture ? asset($user->profile_picture) : asset('assets/images/avatar/avatar-3.jpg') }}"
                                            class="avatar-xl rounded-circle border border-4 border-white"
                                            style="width: 100px; height: 100px; object-fit: cover;" alt="avatar" />
                                    </div>
                                    <div class="lh-1">
                                        <h4 class="mb-0">{{ auth()->user()->name }}</h4>
                                        <small
                                            class="text-black-50">{{ str_replace('@', '', auth()->user()->email) }}</small>
                                    </div>
                                </div>
                                <div class="">
                                    <a href="{{ route('admin.profiles.edit') }}" class="btn btn-light btn-sm">Cài đặt tài
                                        khoản</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mt-0 mt-md-4">
                    <div class="col-lg-3 col-md-4 col-12">
                        <div class="card card-body p-0 border-0">
                            <h5 class="px-4 py-3 m-0">GÓI ĐĂNG KÝ</h5>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item px-4 py-2"><i class="fe fe-calendar me-2"></i> Gói của tôi</li>
                                <li class="list-group-item px-4 py-2"><i class="fe fe-dollar-sign me-2"></i> Thông tin thanh
                                    toán</li>
                                <li class="list-group-item px-4 py-2"><i class="fe fe-credit-card me-2"></i> Thanh toán</li>
                                <li class="list-group-item px-4 py-2"><i class="fe fe-file-text me-2"></i> Hóa đơn</li>
                                <li class="list-group-item px-4 py-2"><i class="fe fe-help-circle me-2"></i> Bài kiểm tra
                                    của tôi</li>
                            </ul>
                            <h5 class="px-4 py-3 m-0">THIẾT LẬP TÀI KHOẢN</h5>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item px-4 py-2 active"><i class="fe fe-edit me-2"></i> <a
                                        href="{{ route('admin.profiles.edit') }}"
                                        class="text-decoration-none text-dark">Chỉnh sửa hồ sơ</a></li>
                                <li class="list-group-item px-4 py-2"><i class="fe fe-log-out me-2"></i> <a
                                        href="{{ route('admin.admin.logout') }}" class="text-decoration-none text-dark"
                                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Đăng
                                        xuất</a>
                                    <form id="logout-form" action="{{ route('admin.admin.logout') }}" method="POST"
                                        class="d-none">
                                        @csrf
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-lg-9 col-md-8 col-12">
                        <div class="card">
                            <!-- Card header -->
                            <div class="card-header">
                                <h3 class="mb-0">Chi tiết hồ sơ</h3>
                                <p class="mb-0">Bạn có toàn quyền quản lý thiết lập tài khoản của mình.</p>
                            </div>
                            <!-- Card body -->
                            <div class="card-body">
                                <div class="d-lg-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center mb-4 mb-lg-0">
                                        <img src="{{ $user->profile_picture ? asset($user->profile_picture) : asset('assets/images/avatar/avatar-3.jpg') }}"
                                            class="avatar-md rounded-circle"
                                            style="width: 100px; height: 100px; object-fit: cover;" alt="Current avatar" />
                                        <div class="ms-3">
                                            <h4 class="mb-0">Ảnh đại diện của bạn</h4>
                                            <p class="mb-0">PNG hoặc JPG, không lớn hơn 800px chiều rộng và chiều cao.</p>
                                        </div>
                                    </div>
                                </div>
                                <hr class="my-5" />
                                <div>
                                    <h4 class="mb-0">Thông tin cá nhân</h4>
                                    <p class="mb-4">Chỉnh sửa thông tin cá nhân của bạn.</p>
                                    <!-- Form -->
                                    <form class="row gx-3 needs-validation" novalidate
                                        action="{{ route('admin.profiles.update') }}" method="POST"
                                        enctype="multipart/form-data">
                                        @csrf
                                        @method('PUT')
                                        <!-- Full Name -->
                                        <div class="mb-3 col-12 col-md-6">
                                            <label class="form-label" for="profileEditName">Họ và tên</label>
                                            <input type="text" id="profileEditName" name="name" class="form-control"
                                                value="{{ old('name', auth()->user()->name) }}" placeholder="Họ và tên"
                                                required />
                                            <div class="invalid-feedback">Vui lòng nhập họ và tên.</div>
                                            @error('name')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <!-- Phone -->
                                        <div class="mb-3 col-12 col-md-6">
                                            <label class="form-label" for="profileEditPhone">Số điện thoại</label>
                                            <input type="text" id="profileEditPhone" name="phone" class="form-control"
                                                value="{{ old('phone', auth()->user()->phone_number) }}"
                                                placeholder="Số điện thoại" required />
                                            <div class="invalid-feedback">Vui lòng nhập số điện thoại.</div>
                                            @error('phone')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <!-- Email -->
                                        <div class="mb-3 col-12 col-md-6">
                                            <label class="form-label" for="profileEditEmail">Email</label>
                                            <input type="email" id="profileEditEmail" name="email"
                                                class="form-control" value="{{ old('email', auth()->user()->email) }}"
                                                placeholder="Email" required />
                                            <div class="invalid-feedback">Vui lòng nhập địa chỉ email hợp lệ.</div>
                                            @error('email')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <!-- Profile Picture -->
                                        <div class="mb-3 col-12">
                                            <label class="form-label" for="profileEditPicture">Ảnh đại diện</label>
                                            <input type="file" id="profileEditPicture" name="profile_picture"
                                                class="form-control" accept="image/png, image/jpeg" />
                                            <div class="invalid-feedback">Vui lòng tải lên hình ảnh hợp lệ (PNG hoặc JPG).
                                            </div>
                                            @error('profile_picture')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                            <p class="mt-1">
                                                Ảnh hiện tại:
                                                <img src="{{ $user->profile_picture ? asset($user->profile_picture) : asset('assets/images/avatar/avatar-3.jpg') }}"
                                                    class="avatar-md rounded-circle"
                                                    style="width: 100px; height: 100px; object-fit: cover;"
                                                    alt="Current avatar" />
                                            </p>
                                        </div>
                                        <div class="col-12">
                                            <!-- Button -->
                                            <button class="btn btn-primary" type="submit">Cập nhật hồ sơ</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
@endsection
