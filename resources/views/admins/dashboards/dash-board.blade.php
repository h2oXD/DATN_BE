@extends('layouts.master')

@section('title')
    Dashboard-analytics
@endsection

@section('content')
    <section class="container-fluid p-4">
        <div class="row">
            <div class="col-lg-12 col-md-12 col-12">
                <div
                    class="border-bottom pb-3 mb-3 d-flex flex-column flex-lg-row gap-3 justify-content-between align-items-lg-center">
                    <div>
                        <h1 class="mb-0 h2 fw-bold">Dashboard-analytics</h1>
                    </div>
                    <div class="d-flex gap-3">
                        <div class="input-group">
                            <input class="form-control flatpickr" type="text" placeholder="Select Date"
                                aria-describedby="basic-addon2" />
                            <span class="input-group-text" id="basic-addon2"><i class="fe fe-calendar"></i></span>
                        </div>
                        <a href="#" class="btn btn-primary">Setting</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="row gy-4 mb-4">
            <!-- Tổng doanh thu của giảng viên -->
            <div class="col-xl-3 col-lg-6 col-md-12 col-12">
                <div class="card">
                    <div class="card-body d-flex flex-column gap-3">
                        <div class="d-flex align-items-center justify-content-between lh-1">
                            <div>
                                <span class="fs-6 text-uppercase fw-semibold ls-md">Doanh thu của giảng viên</span>
                            </div>
                            <div>
                                <span class="fe fe-shopping-bag fs-3 text-primary"></span>
                            </div>
                        </div>
                        <div class="d-flex flex-column gap-1 text-center">
                            <h2 class="fw-bold mb-0">{{ number_format($currentRevenue) }}VND</h2>
                            <div class="d-flex flex-row justify-content-center gap-2 align-items-center">
                                <span class="text-success fw-semibold d-flex align-items-center">
                                    <i class="fe fe-trending-up me-1"></i>
                                    +{{ number_format($revenueChange) }}VND
                                </span>
                                <span class="fw-medium">Doanh thu</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Tổng số khóa học -->
            <div class="col-xl-3 col-lg-6 col-md-12 col-12">
                <div class="card">
                    <div class="card-body d-flex flex-column gap-3">
                        <div class="d-flex align-items-center justify-content-between lh-1">
                            <div>
                                <span class="fs-6 text-uppercase fw-semibold ls-md">Tổng số khóa học</span>
                            </div>
                            <div>
                                <span class="fe fe-book-open fs-3 text-primary"></span>
                            </div>
                        </div>
                        <div class="d-flex flex-column gap-1 text-center">
                            <h2 class="fw-bold mb-0">{{ $currentCourses }}</h2>
                            <div class="d-flex flex-row justify-content-center gap-2 align-items-center">
                                <span
                                    class="text-danger fw-semibold">{{ $coursesChange >= 0 ? '+' : '' }}{{ $coursesChange }}</span>
                                <span class="fw-medium">Khóa học</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Tổng số học viên -->
            <div class="col-xl-3 col-lg-6 col-md-12 col-12">
                <div class="card">
                    <div class="card-body d-flex flex-column gap-3">
                        <div class="d-flex align-items-center justify-content-between lh-1">
                            <div>
                                <span class="fs-6 text-uppercase fw-semibold ls-md">Tổng số học viên</span>
                            </div>
                            <div>
                                <span class="fe fe-users fs-3 text-primary"></span>
                            </div>
                        </div>
                        <div class="d-flex flex-column gap-1 text-center">
                            <h2 class="fw-bold mb-0">{{ number_format($currentStudents) }}</h2>
                            <div class="d-flex flex-row justify-content-center gap-2 align-items-center">
                                <span class="{{ $studentsChange >= 0 ? 'text-success' : 'text-danger' }} fw-semibold">
                                    <i
                                        class="fe {{ $studentsChange >= 0 ? 'fe-trending-up' : 'fe-trending-down' }} me-1"></i>
                                    {{ $studentsChange >= 0 ? '+' : '' }}{{ number_format($studentsChange) }}
                                </span>
                                <span class="fw-medium">Học viên</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Tổng số giảng viên -->
            <div class="col-xl-3 col-lg-6 col-md-12 col-12">
                <div class="card">
                    <div class="card-body d-flex flex-column gap-3">
                        <div class="d-flex align-items-center justify-content-between lh-1">
                            <div>
                                <span class="fs-6 text-uppercase fw-semibold ls-md">Tổng số giảng viên</span>
                            </div>
                            <div>
                                <span class="fe fe-user-check fs-3 text-primary"></span>
                            </div>
                        </div>
                        <div class="d-flex flex-column gap-1 text-center">
                            <h2 class="fw-bold mb-0">{{ number_format($currentInstructors) }}</h2>
                            <div class="d-flex flex-row justify-content-center gap-2 align-items-center">
                                <span class="{{ $instructorsChange >= 0 ? 'text-success' : 'text-danger' }} fw-semibold">
                                    <i
                                        class="fe {{ $instructorsChange >= 0 ? 'fe-trending-up' : 'fe-trending-down' }} me-1"></i>
                                    {{ $instructorsChange >= 0 ? '+' : '' }}{{ number_format($instructorsChange) }}
                                </span>
                                <span class="fw-medium">Giảng viên</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            {{-- tổng đơn hàng --}}
            <div class="col-xl-3 col-lg-6 col-md-12 col-12">
                <div class="card">
                    <div class="card-body d-flex flex-column gap-3 py-4">
                        <!-- Tiêu đề và Icon -->
                        <div class="d-flex align-items-center justify-content-between lh-1">
                            <div>
                                <span class="fs-6 text-uppercase fw-semibold ls-md">Tổng đơn hàng</span>
                            </div>
                            <div>
                                <span class="icon-shape bg-light-warning text-warning rounded-circle fs-3">
                                    <i class="fe fe-shopping-cart"></i>
                                </span>
                            </div>
                        </div>

                        <!-- Nội dung chính -->
                        <div class="d-flex flex-column gap-1 text-center">
                            <h2 class="fw-bold mb-0">{{ number_format($totalTransactions) }}</h2>
                            <div class="d-flex flex-row justify-content-center gap-2 align-items-center">
                                <span class="fw-medium">Đơn hàng</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Tổng Đánh Giá -->
            <div class="col-xl-3 col-lg-6 col-md-12 col-12">
                <div class="card">
                    <div class="card-body d-flex flex-column gap-3">
                        <!-- Tiêu đề và Icon -->
                        <div class="d-flex align-items-center justify-content-between lh-1">
                            <div>
                                <span class="fs-6 text-uppercase fw-semibold ls-md">Tổng đánh giá</span>
                            </div>
                            <div>
                                <span class="fe fe-star fs-3 text-warning"></span>
                            </div>
                        </div>

                        <!-- Số liệu -->
                        <div class="d-flex flex-column gap-1 text-center">
                            <h2 class="fw-bold mb-0">{{ number_format($totalReviews) }}</h2>
                            <div class="d-flex flex-row justify-content-center gap-2 align-items-center">
                                <span class="fw-medium">Đánh giá</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Tổng Lượt Đăng Ký -->
            <div class="col-xl-3 col-lg-6 col-md-12 col-12">
                <div class="card">
                    <div class="card-body d-flex flex-column gap-3">
                        <!-- Tiêu đề và Icon -->
                        <div class="d-flex align-items-center justify-content-between lh-1">
                            <div>
                                <span class="fs-6 text-uppercase fw-semibold ls-md">Tổng lượt đăng ký</span>
                            </div>
                            <div>
                                <span class="fe fe-users fs-3 text-info"></span>
                            </div>
                        </div>

                        <!-- Số liệu -->
                        <div class="d-flex flex-column gap-1 text-center">
                            <h2 class="fw-bold mb-0">{{ number_format($totalEnrollments) }}</h2>
                            <div class="d-flex flex-row justify-content-center gap-2 align-items-center">
                                <span class="fw-medium">Lượt đăng ký</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Tổng Bài Nộp Quiz -->
            <div class="col-xl-3 col-lg-6 col-md-12 col-12">
                <div class="card">
                    <div class="card-body d-flex flex-column gap-3">
                        <!-- Tiêu đề và Icon -->
                        <div class="d-flex align-items-center justify-content-between lh-1">
                            <div>
                                <span class="fs-6 text-uppercase fw-semibold ls-md">Tổng bài nộp quiz</span>
                            </div>
                            <div>
                                <span class="fe fe-edit-3 fs-3 text-success"></span>
                            </div>
                        </div>

                        <!-- Số liệu -->
                        <div class="d-flex flex-column gap-1 text-center">
                            <h2 class="fw-bold mb-0">{{ number_format($totalSubmissions) }}</h2>
                            <div class="d-flex flex-row justify-content-center gap-2 align-items-center">
                                <span class="fw-medium">Bài nộp</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row gy-4 my-2 mb-4">
            <!-- Thống kê tổng doanh thu của giảng viên -->
            <div class="">
                <div class="card h-100">
                    <div
                        class="card-header align-items-center card-header-height d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-0">Thống kê doanh thu của giảng viên</h4>
                        </div>
                        <div>
                            <div class="dropdown dropstart">
                                <a class="btn-icon btn btn-ghost btn-sm rounded-circle" href="#" role="button"
                                    id="courseDropdown1" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fe fe-more-vertical"></i>
                                </a>
                                <div class="dropdown-menu" aria-labelledby="courseDropdown1">
                                    <span class="dropdown-header">Settings</span>
                                    <a class="dropdown-item" href="#">
                                        <i class="fe fe-external-link dropdown-item-icon"></i>
                                        Export
                                    </a>
                                    <a class="dropdown-item" href="#">
                                        <i class="fe fe-mail dropdown-item-icon"></i>
                                        Email Report
                                    </a>
                                    <a class="dropdown-item" href="#">
                                        <i class="fe fe-download dropdown-item-icon"></i>
                                        Download
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end align-items-center mb-3 mx-6 my-2">
                        <form id="revenueFilterForm" class="d-flex align-items-center gap-2">
                            <div class="form-group mb-0">
                                <input type="date" name="date" class="form-control"
                                    value="{{ request('date') }}">
                            </div>
                            <!-- Nút tìm kiếm -->
                            <button type="button" id="searchButton" class="btn btn-success d-flex align-items-center">
                                <i class="fas fa-search me-1"></i> Tìm kiếm
                            </button>
                        </form>
                    </div>
                    <div class="card-body">
                        <div id="courseRevenueChart" class="apex-charts" style="height: 300px;"></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- top giảng viên --}}
        <div class="my-1 row gy-4">
            <div class="col-xl-4 col-lg-12 col-md-12 col-12">
                <!-- Card -->
                <div class="card h-100">
                    <!-- Card header -->
                    <div class="card-header d-flex align-items-center justify-content-between card-header-height">
                        <h4 class="mb-0">giảng viên hot</h4>
                        <a href="#" class="btn btn-outline-secondary btn-sm">View all</a>
                    </div>
                    <!-- Card body -->
                    <div class="card-body">
                        <!-- List group -->
                        <ul class="list-group list-group-flush">
                            @forelse ($popularInstructors as $index => $instructor)
                                <li class="list-group-item px-0 {{ $index === 0 ? 'pt-0' : '' }}">
                                    <div class="row">
                                        <div class="col-auto">
                                            <div class="avatar avatar-md avatar-indicators avatar-online">
                                                <img alt="avatar"
                                                    src="{{ $instructor->profile_picture ? asset('storage/' . $instructor->profile_picture) : asset('assets/images/avatar/avatar-default.jpg') }}"
                                                    class="rounded-circle" />
                                            </div>
                                        </div>
                                        <div class="col ms-n3">
                                            <h4 class="mb-0 h5">{{ $instructor->name }}</h4>
                                            <span class="me-2 fs-6">
                                                <span
                                                    class="text-dark me-1 fw-semibold">{{ $instructor->courses_count }}</span>
                                                Khóa học
                                            </span>
                                            <span class="me-2 fs-6">
                                                <span
                                                    class="text-dark me-1 fw-semibold">{{ number_format($instructor->students_count) }}</span>
                                                Học viên
                                            </span>
                                            <span class="fs-6">
                                                <span
                                                    class="text-dark me-1 fw-semibold">{{ number_format($instructor->reviews_count) }}</span>
                                                Đánh giá
                                            </span>
                                        </div>
                                        <div class="col-auto">
                                            <span class="dropdown dropstart">
                                                <a class="btn-icon btn btn-ghost btn-sm rounded-circle" href="#"
                                                    role="button" id="courseDropdown{{ $index + 7 }}"
                                                    data-bs-toggle="dropdown" data-bs-offset="-20,20"
                                                    aria-expanded="false">
                                                    <i class="fe fe-more-vertical"></i>
                                                </a>
                                                <span class="dropdown-menu"
                                                    aria-labelledby="courseDropdown{{ $index + 7 }}">
                                                    <span class="dropdown-header">Settings</span>
                                                    <a class="dropdown-item" href="#">
                                                        <i class="fe fe-edit dropdown-item-icon"></i>
                                                        Edit
                                                    </a>
                                                    <a class="dropdown-item" href="#">
                                                        <i class="fe fe-trash dropdown-item-icon"></i>
                                                        Remove
                                                    </a>
                                                </span>
                                            </span>
                                        </div>
                                    </div>
                                </li>
                            @empty
                                <li class="list-group-item px-0 pt-0">
                                    <p class="text-center">Không có giảng viên nào.</p>
                                </li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-xl-4 col-lg-12 col-md-12 col-12">
                <!-- Card -->
                <div class="card h-100">
                    <!-- Card header -->
                    <div class="card-header d-flex align-items-center justify-content-between card-header-height">
                        <h4 class="mb-0">Khóa học gần đây</h4>
                        <a href="#" class="btn btn-outline-secondary btn-sm">View all</a>
                    </div>
                    <!-- Card body -->
                    <div class="card-body">
                        <!-- List group flush -->
                        <ul class="list-group list-group-flush">
                            @forelse ($recentCourses as $index => $course)
                                <li class="list-group-item px-0 {{ $index === 0 ? 'pt-0' : '' }}">
                                    <div class="row flex-column flex-md-row gap-3 gap-md-0">
                                        <!-- Col -->
                                        <div class="col-md-3 col-12">
                                            <a href="#">
                                                <img src="{{ $course->thumbnail ? asset('storage/' . $course->thumbnail) : asset('assets/images/course/course-default.jpg') }}"
                                                    alt="{{ $course->title }}" class="img-fluid rounded" />
                                            </a>
                                        </div>
                                        <!-- Col -->
                                        <div class="col-md-8 col-10">
                                            <div class="d-flex flex-column gap-2">
                                                <a href="#">
                                                    <h5 class="text-primary-hover mb-0">
                                                        {{ Str::limit($course->title, 40, '...') }}</h5>
                                                </a>
                                                <div class="d-flex align-items-center gap-2">
                                                    <img alt="avatar"
                                                        src="{{ $course->user->profile_picture ? asset('storage/' . $course->user->profile_picture) : asset('assets/images/avatar/avatar-default.jpg') }}"
                                                        class="avatar-md rounded-circle" />
                                                    <span class="fs-6">{{ $course->user->name }}</span>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Col auto -->
                                        <div class="col-1 col-auto d-flex justify-content-center">
                                            <span class="dropdown dropstart">
                                                <a class="btn-icon btn btn-ghost btn-sm rounded-circle" href="#"
                                                    role="button" id="courseDropdown{{ $index + 3 }}"
                                                    data-bs-toggle="dropdown" data-bs-offset="-20,20"
                                                    aria-expanded="false">
                                                    <i class="fe fe-more-vertical"></i>
                                                </a>
                                                <span class="dropdown-menu"
                                                    aria-labelledby="courseDropdown{{ $index + 3 }}">
                                                    <span class="dropdown-header">Settings</span>
                                                    <a class="dropdown-item" href="#">
                                                        <i class="fe fe-edit dropdown-item-icon"></i>
                                                        Edit
                                                    </a>
                                                    <a class="dropdown-item" href="#">
                                                        <i class="fe fe-trash dropdown-item-icon"></i>
                                                        Remove
                                                    </a>
                                                </span>
                                            </span>
                                        </div>
                                    </div>
                                </li>
                            @empty
                                <li class="list-group-item px-0 pt-0">
                                    <p class="text-center">Không có khóa học nào.</p>
                                </li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    {{-- biểu đồ doanh thu của giảng viên --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const revenueChart = new ApexCharts(document.querySelector("#courseRevenueChart"), {
                chart: {
                    height: 400,
                    type: 'bar'
                },
                series: [{
                    name: 'Tổng doanh thu',
                    data: @json($chartSeries)
                }],
                xaxis: {
                    title: {
                        text: 'Ngày giao dịch'
                    },
                    categories: @json($chartLabels)
                }
            });

            revenueChart.render();

            // Sự kiện click tìm kiếm
            $('#searchButton').on('click', function(e) {
                e.preventDefault();

                const selectedDate = $('input[name="date"]').val();
                const selectedMonth = $('select[name="month"]').val();
                const selectedYear = $('select[name="year"]').val();

                if (!selectedDate && !selectedMonth && !selectedYear) {
                    alert('Vui lòng chọn ít nhất một điều kiện tìm kiếm!');
                    return;
                }

                $.ajax({
                    url: '{{ route('admin.dashboard.revenue.filter') }}',
                    method: 'GET',
                    data: {
                        date: selectedDate,
                        month: selectedMonth,
                        year: selectedYear
                    },
                    success: function(response) {
                        console.log('Dữ liệu trả về:', response);

                        if (response.labels.length === 0) {
                            alert('Không tìm thấy kết quả phù hợp!');
                        }

                        // Cập nhật dữ liệu vào biểu đồ
                        revenueChart.updateOptions({
                            xaxis: {
                                categories: response.labels
                            },
                            series: [{
                                name: 'Tổng doanh thu',
                                data: response.series
                            }]
                        });
                    },
                    error: function(err) {
                        console.error('Lỗi:', err);
                        alert('Đã xảy ra lỗi khi tìm kiếm!');
                    }
                });
            });
        });
    </script>
@endpush
