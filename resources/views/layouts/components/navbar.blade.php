<nav class="navbar-vertical navbar">
    <div class="vh-100" data-simplebar>
        <!-- Brand logo -->
        <a class="navbar-brand d-flex justify-content-center" href="/">
            <img src="{{ env('APP_URL') . '/logo.png' }}" alt="Geeks" />
        </a>
        <!-- Navbar nav -->
        <ul class="navbar-nav flex-column " id="sideNavbar">
            <li class="nav-item">
                <a class="nav-link text-light " href="/">
                    <i class="text-light fe fe-home me-3 fs-4"></i>
                    Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-light " href="{{ url('/admin/dashboards/statistics') }}">
                    <i class="fe fe-trending-up mr-2 me-3 fs-4"></i>
                    Thống kê
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-light" href="{{ route(name: 'courses.index') }}">
                    <i class="text-light fe fe-book me-3 fs-4"></i>
                    Kiểm duyệt khoá học
                </a>
            </li>
            <!-- Nav item -->
            <li class="nav-item">
                <a class="nav-link text-light collapsed" href="#" data-bs-toggle="collapse"
                    data-bs-target="#navProfile" aria-expanded="false" aria-controls="navProfile">
                    <i class="text-light fe fe-user me-3 fs-4"></i>
                    Quản lí người dùng
                </a>
                <div id="navProfile" class="collapse" data-bs-parent="#sideNavbar">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link text-light" href="{{ route('users.index') }}">Danh sách người
                                dùng</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-light" href="{{ route('users.create') }}">Thêm mới người
                                dùng</a>
                        </li>
                    </ul>
                </div>
            </li>
            <!-- Nav item -->
            <li class="nav-item">
                <a class="nav-link text-light  collapsed " href="#" data-bs-toggle="collapse"
                    data-bs-target="#navProject" aria-expanded="false" aria-controls="navProject">
                    <i class="text-light fe fe-file me-3 fs-4"></i>
                    Quản lí danh mục
                </a>
                <div id="navProject" class="collapse " data-bs-parent="#sideNavbar">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link text-light " href="{{ route('admin.categories.index') }}">Danh sách danh
                                mục</a>

                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-light " href="{{ route('admin.categories.create') }}">Thêm mới danh
                                mục</a>
                        </li>
                    </ul>
                </div>
            </li>
            <li class="nav-item">
                <a class="nav-link text-light collapsed" href="#" data-bs-toggle="collapse"
                    data-bs-target="#navCardManagement" aria-expanded="false" aria-controls="navCardManagement">
                    <i class=" text-light fe fe-tag me-3 fs-4"></i>
                    Quản lí thẻ
                </a>
                <div id="navCardManagement" class="collapse" data-bs-parent="#sideNavbar">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link text-light" href="{{ route('tags.index') }}">Danh sách thẻ</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-light" href="{{ route('tags.create') }}">Thêm mới thẻ</a>
                        </li>
                    </ul>
                </div>
            </li>
            <!-- Nav item -->
            <li class="nav-item">
                <a class="nav-link text-light  collapsed " href="#" data-bs-toggle="collapse"
                    data-bs-target="#navecommerce" aria-expanded="false" aria-controls="navecommerce">
                    <i class="text-light fe fe-shopping-bag me-3 fs-4"></i>
                    Phiếu giảm giá
                </a>
                <div id="navecommerce" class="collapse " data-bs-parent="#sideNavbar">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link text-light " href="{{ route('vouchers.index') }}">Danh sách</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-light " href="{{ route('vouchers.create') }}">Thêm mới</a>
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
