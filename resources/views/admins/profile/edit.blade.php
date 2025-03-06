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
                                            class="avatar-xl rounded-circle border border-4 border-white" alt="avatar" />
                                    </div>
                                    <div class="lh-1">
                                        <h4 class="mb-0">{{ auth()->user()->name }}</h4>
                                        <small
                                            class="text-black-50">{{ str_replace('@', '', auth()->user()->email) }}</small>
                                    </div>
                                </div>
                                <div class="">
                                    <a href="{{ route('admin.admins.profile.edit') }}" class="btn btn-light btn-sm">Account
                                        Setting</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mt-0 mt-md-4">
                    <div class="col-lg-3 col-md-4 col-12">
                        <div class="card card-body p-0 border-0">
                            <h5 class="px-4 py-3 m-0">SUBSCRIPTION</h5>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item px-4 py-2"><i class="fe fe-calendar me-2"></i> My Subscriptions
                                </li>
                                <li class="list-group-item px-4 py-2"><i class="fe fe-dollar-sign me-2"></i> Billing Info
                                </li>
                                <li class="list-group-item px-4 py-2"><i class="fe fe-credit-card me-2"></i> Payment</li>
                                <li class="list-group-item px-4 py-2"><i class="fe fe-file-text me-2"></i> Invoice</li>
                                <li class="list-group-item px-4 py-2"><i class="fe fe-help-circle me-2"></i> My Quiz Attempt
                                </li>
                            </ul>
                            <h5 class="px-4 py-3 m-0">ACCOUNT SETTINGS</h5>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item px-4 py-2 active"><i class="fe fe-edit me-2"></i> <a
                                        href="{{ route('admin.admins.profile.edit') }}"
                                        class="text-decoration-none text-dark">Edit Profile</a></li>
                                <li class="list-group-item px-4 py-2"><i class="fe fe-lock me-2"></i> <a href="#"
                                        class="text-decoration-none text-dark">Security</a></li>
                                <li class="list-group-item px-4 py-2"><i class="fe fe-share me-2"></i> <a
                                        href="#"
                                        class="text-decoration-none text-dark">Social Profiles</a></li>
                                <li class="list-group-item px-4 py-2"><i class="fe fe-bell me-2"></i> <a href="#"
                                        class="text-decoration-none text-dark">Notifications</a></li>
                                <li class="list-group-item px-4 py-2"><i class="fe fe-shield me-2"></i> <a href="#"
                                        class="text-decoration-none text-dark">Profile Privacy</a></li>
                                <li class="list-group-item px-4 py-2"><i class="fe fe-trash me-2"></i> <a
                                        href="#"
                                        class="text-decoration-none text-dark">Delete Profile</a></li>
                                <li class="list-group-item px-4 py-2"><i class="fe fe-link me-2"></i> <a href="#"
                                        class="text-decoration-none text-dark">Linked Accounts</a></li>
                                <li class="list-group-item px-4 py-2"><i class="fe fe-log-out me-2"></i> <a
                                        href="{{ route('admin.admin.logout') }}" class="text-decoration-none text-dark"
                                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Sign
                                        Out</a>
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
                                <h3 class="mb-0">Profile Details</h3>
                                <p class="mb-0">You have full control to manage your own account setting.</p>
                            </div>
                            <!-- Card body -->
                            <div class="card-body">
                                <div class="d-lg-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center mb-4 mb-lg-0">
                                        <img src="{{ $user->profile_picture ? asset($user->profile_picture) : asset('assets/images/avatar/avatar-3.jpg') }}"
                                            class="avatar-md rounded-circle" alt="Current avatar" />
                                        <div class="ms-3">
                                            <h4 class="mb-0">Your avatar</h4>
                                            <p class="mb-0">PNG or JPG no bigger than 800px wide and tall.</p>
                                        </div>
                                    </div>
                                    <div>
                                        <a href="#" class="btn btn-outline-secondary btn-sm">Update</a>
                                        <a href="#" class="btn btn-outline-danger btn-sm">Delete</a>
                                    </div>
                                </div>
                                <hr class="my-5" />
                                <div>
                                    <h4 class="mb-0">Personal Details</h4>
                                    <p class="mb-4">Edit your personal information.</p>
                                    <!-- Form -->
                                    <form class="row gx-3 needs-validation" novalidate
                                        action="{{ route('admin.admins.profile.update') }}" method="POST"
                                        enctype="multipart/form-data">
                                        @csrf
                                        @method('PUT')
                                        <!-- Full Name -->
                                        <div class="mb-3 col-12 col-md-6">
                                            <label class="form-label" for="profileEditName">Full Name</label>
                                            <input type="text" id="profileEditName" name="name"
                                                class="form-control" value="{{ old('name', auth()->user()->name) }}"
                                                placeholder="Full Name" required />
                                            <div class="invalid-feedback">Please enter full name.</div>
                                            @error('name')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <!-- Phone -->
                                        <div class="mb-3 col-12 col-md-6">
                                            <label class="form-label" for="profileEditPhone">Phone</label>
                                            <input type="text" id="profileEditPhone" name="phone"
                                                class="form-control"
                                                value="{{ old('phone', auth()->user()->phone_number) }}"
                                                placeholder="Phone" required />
                                            <div class="invalid-feedback">Please enter phone number.</div>
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
                                            <div class="invalid-feedback">Please enter a valid email.</div>
                                            @error('email')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <!-- Password -->
                                        <div class="mb-3 col-12 col-md-6">
                                            <label class="form-label" for="profileEditPassword">Password</label>
                                            <input type="password" id="profileEditPassword" name="password"
                                                class="form-control" placeholder="Password" />
                                            <div class="invalid-feedback">Please enter a password.</div>
                                            @error('password')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <!-- Profile Picture -->
                                        <div class="mb-3 col-12">
                                            <label class="form-label" for="profileEditPicture">Profile Picture</label>
                                            <input type="file" id="profileEditPicture" name="profile_picture"
                                                class="form-control" accept="image/png, image/jpeg" />
                                            <div class="invalid-feedback">Please upload a valid image (PNG or JPG).</div>
                                            @error('profile_picture')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                            <p class="mt-1">
                                                Current image:
                                                <img src="{{ $user->profile_picture ? asset($user->profile_picture) : asset('assets/images/avatar/avatar-3.jpg') }}"
                                                    class="avatar-md rounded-circle" alt="Current avatar" />
                                            </p>
                                        </div>
                                        <div class="col-12">
                                            <!-- Button -->
                                            <button class="btn btn-primary" type="submit">Update Profile</button>
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
