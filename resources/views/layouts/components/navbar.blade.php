<nav class="navbar-vertical navbar" style="background-color: white">
    <div class="vh-100" data-simplebar>
        <!-- Brand logo -->
        <a class="navbar-brand d-flex justify-content-center" href="/">
            <img src="{{ env('APP_URL') . '/logo.png' }}" alt="Geeks" />
        </a>
        <!-- Navbar nav -->
        <ul class="navbar-nav flex-column " id="sideNavbar">
            <li class="nav-item">
                <p class="text-dark text-uppercase mb-2 fw-bold fs-6 m-0 ms-3">
                    Quản trị hệ thống
                </p>
            </li>
            <li class="nav-item">
                <a class="nav-link text-dark " href="/">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="w-10 me-2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                    </svg>

                    Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-dark " href="{{ route('admin.dashboard') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="w-10 me-2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z" />
                    </svg>

                    Thống kê
                </a>
            </li>
            <li class="nav-item">
                <p class="text-dark text-uppercase mt-3 fw-bold fs-6 ms-3 m-0 mt-2">
                    Quản lý kiểm duyệt
                </p>
            </li>
            <li class="nav-item">
                <a class="nav-link text-dark" href="{{ route('admin.censor.courses.list') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="w-10 me-2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M11.35 3.836c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m8.9-4.414c.376.023.75.05 1.124.08 1.131.094 1.976 1.057 1.976 2.192V16.5A2.25 2.25 0 0 1 18 18.75h-2.25m-7.5-10.5H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V18.75m-7.5-10.5h6.375c.621 0 1.125.504 1.125 1.125v9.375m-8.25-3 1.5 1.5 3-3.75" />
                    </svg>
                    Kiểm duyệt khoá học
                </a>
                <a class="nav-link text-dark collapsed" href="{{ route('admin.lecturer_registers.index') }}" y>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="w-10 me-2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M17.982 18.725A7.488 7.488 0 0 0 12 15.75a7.488 7.488 0 0 0-5.982 2.975m11.963 0a9 9 0 1 0-11.963 0m11.963 0A8.966 8.966 0 0 1 12 21a8.966 8.966 0 0 1-5.982-2.275M15 9.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                    </svg>
                    Kiểm duyệt người dùng
                </a>
                <a class="nav-link text-dark collapsed" href="{{ route('admin.censor-withdraw.index') }}" y>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="w-10 me-2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M3 7a2 2 0 012-2h14a2 2 0 012 2v10a2 2 0 01-2 2H5a2 2 0 01-2-2V7zm14 2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2a2 2 0 012-2z" />
                    </svg>
                    Kiểm duyệt rút tiền
                </a>
            </li>
            <li class="nav-item">
                <p class="text-dark text-uppercase mt-3 fw-bold fs-6 ms-3 m-0 mt-2">
                    Quản lý nội dung
                </p>
            </li>
            <!-- Nav item -->
            <li class="nav-item">
                <a class="nav-link text-dark  collapsed " href="#" data-bs-toggle="collapse"
                    data-bs-target="#navCourse" aria-expanded="false" aria-controls="navCourse">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="w-10 me-2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9 17.25v1.007a3 3 0 0 1-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0 1 15 18.257V17.25m6-12V15a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 15V5.25m18 0A2.25 2.25 0 0 0 18.75 3H5.25A2.25 2.25 0 0 0 3 5.25m18 0V12a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 12V5.25" />
                    </svg>


                    Khoá học
                </a>
                <div id="navCourse" class="collapse " data-bs-parent="#sideNavbar">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link text-dark " href="{{ route('admin.courses.index') }}">Khoá học đang
                                bán</a>

                        </li>
                    </ul>
                </div>
            </li>
            <li class="nav-item">
                <a class="nav-link text-dark  collapsed " href="#" data-bs-toggle="collapse"
                    data-bs-target="#navachungchi" aria-expanded="false" aria-controls="navachungchi">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="w-10 me-2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342M6.75 15a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Zm0 0v-3.675A55.378 55.378 0 0 1 12 8.443m-7.007 11.55A5.981 5.981 0 0 0 6.75 15.75v-1.5" />
                    </svg>
                    Chứng chỉ
                </a>
                <div id="navachungchi" class="collapse " data-bs-parent="#sideNavbar">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link text-dark " href="">Mẫu chứng chỉ</a>

                        </li>
                    </ul>
                </div>
            </li>
            <li class="nav-item">
                <a class="nav-link text-dark  collapsed " href="#" data-bs-toggle="collapse"
                    data-bs-target="#navProject" aria-expanded="false" aria-controls="navProject">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="w-10 me-2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                    </svg>
                    Danh mục
                </a>
                <div id="navProject" class="collapse " data-bs-parent="#sideNavbar">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link text-dark " href="{{ route('admin.categories.index') }}">Danh sách danh
                                mục</a>

                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-dark " href="{{ route('admin.categories.create') }}">Thêm mới danh
                                mục</a>
                        </li>
                    </ul>
                </div>
            </li>
            <li class="nav-item">
                <a class="nav-link text-dark collapsed" href="#" data-bs-toggle="collapse"
                    data-bs-target="#navBanner" aria-expanded="false" aria-controls="navBanner">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="" viewBox="0 0 576 512" stroke-width="1.5"
                        stroke="currentColor" class="w-10 me-2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M160 80l352 0c8.8 0 16 7.2 16 16l0 224c0 8.8-7.2 16-16 16l-21.2 0L388.1 178.9c-4.4-6.8-12-10.9-20.1-10.9s-15.7 4.1-20.1 10.9l-52.2 79.8-12.4-16.9c-4.5-6.2-11.7-9.8-19.4-9.8s-14.8 3.6-19.4 9.8L175.6 336 160 336c-8.8 0-16-7.2-16-16l0-224c0-8.8 7.2-16 16-16zM96 96l0 224c0 35.3 28.7 64 64 64l352 0c35.3 0 64-28.7 64-64l0-224c0-35.3-28.7-64-64-64L160 32c-35.3 0-64 28.7-64 64zM48 120c0-13.3-10.7-24-24-24S0 106.7 0 120L0 344c0 75.1 60.9 136 136 136l320 0c13.3 0 24-10.7 24-24s-10.7-24-24-24l-320 0c-48.6 0-88-39.4-88-88l0-224zm208 24a32 32 0 1 0 -64 0 32 32 0 1 0 64 0z" />
                    </svg>
                    Banner
                </a>
                <div id="navBanner" class="collapse" data-bs-parent="#sideNavbar">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link text-dark" href="{{ route('admin.banners.index') }}">Danh sách
                                Banner</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-dark" href="{{ route('admin.banners.create') }}">Thêm mới
                                Banner</a>
                        </li>
                    </ul>
                </div>
            </li>

            <li class="nav-item">
                <a class="nav-link text-dark collapsed" href="#" data-bs-toggle="collapse"
                    data-bs-target="#navCardManagement" aria-expanded="false" aria-controls="navCardManagement">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="w-10 me-2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9.568 3H5.25A2.25 2.25 0 0 0 3 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 0 0 5.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 0 0 9.568 3Z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6Z" />
                    </svg>

                    Thẻ
                </a>
                <div id="navCardManagement" class="collapse" data-bs-parent="#sideNavbar">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link text-dark" href="{{ route('admin.tags.index') }}">Danh sách thẻ</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-dark" href="{{ route('admin.tags.create') }}">Thêm mới thẻ</a>
                        </li>
                    </ul>
                </div>
            </li>
            <!-- Nav item -->
            <li class="nav-item">
                <a class="nav-link text-dark  collapsed " href="#" data-bs-toggle="collapse"
                    data-bs-target="#navecommerce" aria-expanded="false" aria-controls="navecommerce">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="w-10 me-2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M6 6.878V6a2.25 2.25 0 0 1 2.25-2.25h7.5A2.25 2.25 0 0 1 18 6v.878m-12 0c.235-.083.487-.128.75-.128h10.5c.263 0 .515.045.75.128m-12 0A2.25 2.25 0 0 0 4.5 9v.878m13.5-3A2.25 2.25 0 0 1 19.5 9v.878m0 0a2.246 2.246 0 0 0-.75-.128H5.25c-.263 0-.515.045-.75.128m15 0A2.25 2.25 0 0 1 21 12v6a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 18v-6c0-.98.626-1.813 1.5-2.122" />
                    </svg>

                    Phiếu giảm giá
                </a>
                <div id="navecommerce" class="collapse " data-bs-parent="#sideNavbar">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link text-dark " href="{{ route('admin.vouchers.index') }}">Danh sách</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-dark " href="{{ route('admin.vouchers.create') }}">Thêm mới</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-dark " href="{{ route('admin.voucher-use.index') }}">Lịch sử</a>
                        </li>
                    </ul>
                </div>
            </li>
            <li class="nav-item">
                <p class="text-dark text-uppercase mt-3 fw-bold fs-6 ms-3 m-0 mt-2">
                    Quản lý tài khoản
                </p>
            </li>
            <!-- Nav item -->
            <li class="nav-item">
                <a class="nav-link text-dark collapsed" href="#" data-bs-toggle="collapse"
                    data-bs-target="#navProfile" aria-expanded="false" aria-controls="navProfile">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="w-10 me-2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M17.982 18.725A7.488 7.488 0 0 0 12 15.75a7.488 7.488 0 0 0-5.982 2.975m11.963 0a9 9 0 1 0-11.963 0m11.963 0A8.966 8.966 0 0 1 12 21a8.966 8.966 0 0 1-5.982-2.275M15 9.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                    </svg>
                    Người dùng
                </a>
                <div id="navProfile" class="collapse" data-bs-parent="#sideNavbar">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link text-dark" href="{{ route('admin.students.index') }}">Học viên</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-dark" href="{{ route('admin.lecturers.index') }}">Giảng viên</a>
                        </li>

                    </ul>
                </div>

            </li>

            <!-- Nav item -->
            <li class="nav-item">
                <div class="nav-divider"></div>
            </li>
    </div>
</nav>
