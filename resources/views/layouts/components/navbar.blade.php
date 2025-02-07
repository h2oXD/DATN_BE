<nav class="navbar-vertical navbar">
    <div class="vh-100" data-simplebar>
        <!-- Brand logo -->
        <a class="navbar-brand d-flex justify-content-center" href="/">
            <img src="{{ env('APP_URL') . 'logo.png' }}" alt="Geeks" />
        </a>
        <!-- Navbar nav -->
        <ul class="navbar-nav flex-column " id="sideNavbar">
            <li class="nav-item">
                <a class="nav-link text-light " href="/">
                    <i class="nav-icon text-light fe fe-home me-2"></i>
                    Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-light " href="{{ url('/admin/dashboards/statistics') }}">
                    <i class="fe fe-trending-up mr-2 me-2"></i>
                    Thống kê 
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-light  collapsed " href="#" data-bs-toggle="collapse"
                    data-bs-target="#navCourses" aria-expanded="false" aria-controls="navCourses">
                    <i class="nav-icon text-light fe fe-book me-2"></i>
                    Khoá học
                </a>
                <div id="navCourses" class="collapse " data-bs-parent="#sideNavbar">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link text-light " href="admin-course-overview.html">All Courses</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-light " href="admin-course-category.html">Courses Category</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-light " href="admin-course-category-single.html">Category
                                Single</a>
                        </li>
                    </ul>
                </div>
            </li>
            <!-- Nav item -->
            <li class="nav-item">
                <a class="nav-link text-light collapsed" href="#" data-bs-toggle="collapse"
                    data-bs-target="#navProfile" aria-expanded="false" aria-controls="navProfile">
                    <i class="nav-icon text-light fe fe-user me-2"></i>
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
                    <i class="nav-icon text-light fe fe-file me-2"></i>
                    Quản lí danh mục
                </a>
                <div id="navProject" class="collapse " data-bs-parent="#sideNavbar">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link text-light " href="{{ route('categories.index') }}">Danh sách danh
                                mục</a>

                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-light " href="{{ route('categories.create') }}">Thêm mới danh
                                mục</a>
                        </li>
                    </ul>
                </div>
            </li>
            <li class="nav-item">
                <a class="nav-link text-light collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#navCardManagement" aria-expanded="false" aria-controls="navCardManagement">
                    <i class=" nav-icon text-light fe fe-tag me-2"></i>
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
                    <i class="nav-icon text-light fe fe-shopping-bag me-2"></i>
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
