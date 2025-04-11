@extends('layouts.master')

@section('title')
    Dashboard
@endsection

@section('content')
    <section class="container-fluid p-4">
        <div class="row">
            <div class="col-lg-12 col-md-12 col-12">
                <div
                    class="border-bottom pb-3 mb-3 d-flex flex-column flex-lg-row gap-3 justify-content-between align-items-lg-center">
                    <div>
                        <h1 class="mb-0 h2 fw-bold">Thống kê tổng quát</h1>
                    </div>
                </div>
            </div>
        </div>
        <div class="row gy-4 mb-4">
            <div class="col-xl-3 col-lg-6 col-md-12 col-12">
                <div class="card">
                    <div class="card-body d-flex flex-column gap-3">
                        <div class="d-flex align-items-center justify-content-between lh-1">
                            <div>
                                <span class="fs-6 text-uppercase fw-semibold ls-md">Doanh thu</span>
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
            <div class="col-xl-3 col-lg-6 col-md-12 col-12">
                <div class="card">
                    <div class="card-body d-flex flex-column gap-3">
                        <div class="d-flex align-items-center justify-content-between lh-1">
                            <div>
                                <span class="fs-6 text-uppercase fw-semibold ls-md">Lợi nhuận</span>
                            </div>
                            <div>
                                <span class="fe fe-shopping-bag fs-3 text-primary"></span>
                            </div>
                        </div>
                        <div class="d-flex flex-column gap-1 text-center">
                            <h2 class="fw-bold mb-0">{{ number_format($currentProfit) }}VND</h2>
                            <div class="d-flex flex-row justify-content-center gap-2 align-items-center">
                                <span class="text-success fw-semibold d-flex align-items-center">
                                    <i class="fe fe-trending-up me-1"></i>
                                    +{{ number_format($profitChange) }}VND
                                </span>
                                <span class="fw-medium">Lợi nhuận</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row gy-4 my-2 mb-4">
            <div class="">
                <div class="card h-100">
                    <div
                        class="card-header align-items-center card-header-height d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-0">Thống kê doanh thu theo năm</h4>
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
                                <input type="date" name="date" class="form-control" value="{{ request('date') }}">
                            </div>
                            <!-- Nút tìm kiếm -->
                            <button type="button" id="searchButton" class="btn btn-success d-flex align-items-center">
                                <i class="fas fa-search me-1"></i> Tìm kiếm
                            </button>
                        </form>
                    </div>
                    <div class="card-body">
                        <div id="revenuePerMonthChart" class="apex-charts" style="height: 300px;"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row gy-4 mb-4">
            <div class="col-xl-6 col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Số lượng giảng viên đăng ký</h5>
                        <div id="lecturersChart"></div>
                    </div>
                </div>
            </div>
            <div class="col-xl-6 col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Số lượng khóa học</h5>
                        <div id="coursesChart"></div>
                    </div>
                </div>
            </div>
            <div class="col-xl-6 col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Số lượng học viên</h5>
                        <div id="studentsChart"></div>
                    </div>
                </div>
            </div>
            <div class="col-xl-6 col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Tổng số khóa học đã được mua</h5>
                        <div id="ordersChart"></div>
                    </div>
                </div>
            </div>
        </div>
        {{-- top giảng viên --}}
        <div class="my-1 row gy-4 my-1">
            <!-- HOT INSTRUCTORS -->
            <div class="col-xl-4 col-lg-12 col-md-12 col-12">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-header d-flex align-items-center justify-content-between bg-white">
                        <h4 class="mb-0 fw-bold text-primary">Giảng viên nổi bật</h4>
                    </div>
                    <div class="card-body py-3">
                        <ul class="list-group list-group-flush">
                            @forelse ($popularInstructors as $index => $instructor)
                                <li class="list-group-item px-0 py-3 border-0 rounded hover-bg-light">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="position-relative">
                                                <img alt="avatar"
                                                    src="{{ asset('storage/' . $instructor->profile_picture) }}"
                                                    onerror="this.onerror=null;this.src='{{ asset('assets/images/avatar/avatar-default.jpg') }}';"
                                                    class="avatar-xl rounded-circle border border-4 border-white"
                                                    style="width: 50px; height: 50px; object-fit: cover;"
                                                    alt="avatar" />
                                                <span
                                                    class="position-absolute bottom-0 end-0 p-1 bg-success rounded-circle border border-white"
                                                    style="width:12px; height:12px;"></span>
                                            </div>
                                            <div>
                                                <h5 class="mb-1 fw-semibold text-dark">{{ $instructor->name }}</h5>
                                                <div class="text-muted small">
                                                    {{ $instructor->courses_count }} khóa học •
                                                    {{ number_format($instructor->students_count) }} học viên
                                                </div>
                                                <div class="text-muted small">
                                                    {{ number_format($instructor->reviews_count) }} đánh giá</div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            @empty
                                <li class="list-group-item text-center text-muted">Không có giảng viên nào.</li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>
            <!-- RECENT COURSES -->
            <div class="col-xl-4 col-lg-12 col-md-12 col-12">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-header d-flex align-items-center justify-content-between bg-white">
                        <h4 class="mb-0 fw-bold text-primary">Khóa học gần đây</h4>
                    </div>
                    <div class="card-body py-3">
                        <ul class="list-group list-group-flush">
                            @forelse ($recentCourses as $index => $course)
                                <li class="list-group-item px-0 py-3 border-0 rounded hover-bg-light">
                                    <div
                                        class="d-flex align-items-center justify-content-between flex-wrap flex-md-nowrap gap-3">
                                        <a href="#" class="d-block flex-shrink-0">
                                            <img src="{{ $course->thumbnail ? asset('storage/' . $course->thumbnail) : asset('assets/images/course/course-default.jpg') }}"
                                                alt="{{ $course->title }}" class="rounded shadow-sm border border-1"
                                                style="width: 80px; height: 60px; object-fit: cover;" />
                                        </a>
                                        <div class="flex-grow-1">
                                            <a href="#">
                                                <h5 class="mb-1 text-dark fw-semibold">
                                                    {{ Str::limit($course->title, 40, '...') }}</h5>
                                            </a>
                                            <div class="d-flex align-items-center gap-2">
                                                <img alt="avatar"
                                                    src="{{ asset('storage/' . $course->user->profile_picture) }}"
                                                    class="rounded-circle"
                                                    style="width: 35px; height: 35px; object-fit: cover;" />
                                                <span class="small text-muted">{{ $course->user->name }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            @empty
                                <li class="list-group-item text-center text-muted">Không có khóa học nào.</li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('script')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            if (typeof coursesData === 'undefined') {
                console.error("coursesData is not defined");
                return;
            }

            var options = {
                series: [{
                    name: "Khóa học",
                    data: coursesData.map(item => item.total_courses)
                }],
                chart: {
                    type: 'bar'
                },
                xaxis: {
                    categories: coursesData.map(item => item.date)
                }
            };

            var chart = new ApexCharts(document.querySelector("#coursesChart"), options);
            chart.render();

            var options = {
                series: [{
                    name: "Số học viên",
                    data: studentsData.map(item => item.total_students)
                }],
                chart: {
                    type: 'bar'
                },
                xaxis: {
                    categories: studentsData.map(item => item.date)
                }
            };

            var chart = new ApexCharts(document.querySelector("#studentsChart"), options);
            chart.render();

            var options = {
                series: [{
                    name: "Số đơn hàng",
                    data: ordersData.map(item => item.total_orders)
                }],
                chart: {
                    type: 'bar'
                },
                xaxis: {
                    categories: ordersData.map(item => item.date)
                }
            };

            var chart = new ApexCharts(document.querySelector("#ordersChart"), options);
            chart.render();

            var options = {
                chart: {
                    type: 'bar',
                    height: 350
                },
                series: [{
                    name: 'Số lượng giảng viên',
                    data: lecturersData.map(item => item.total_lecturers)
                }],
                xaxis: {
                    categories: lecturersData.map(item => item.date),
                    title: {
                        text: 'Ngày'
                    }
                },
            };

            var chart = new ApexCharts(document.querySelector("#lecturersChart"), options);
            chart.render();

            if (typeof chartMonthLabels === 'undefined' || typeof chartMonthSeries === 'undefined') {
                console.error("chartMonthLabels or chartMonthSeries is not defined");
                return;
            }

            const formattedMonthLabels = chartMonthLabels;

            // Tính lợi nhuận (30% của doanh thu)
            const chartProfitSeries = chartMonthSeries.map(revenue => revenue * 0.3);

            console.log("Revenue per Month Data:", chartMonthSeries);
            console.log("Profit per Month Data:", chartProfitSeries);

            // Biểu đồ doanh thu và lợi nhuận theo tháng
            var revenuePerMonthChartOptions = {
                chart: {
                    type: 'bar',
                    height: 350
                },
                series: [{
                    name: 'Doanh thu',
                    data: chartMonthSeries || []
                }, {
                    name: 'Lợi nhuận',
                    data: chartProfitSeries || []
                }],
                xaxis: {
                    categories: formattedMonthLabels,
                },
                yaxis: {
                    labels: {
                        formatter: function(value) {
                            return new Intl.NumberFormat('vi-VN').format(value);
                        }
                    }
                },
                tooltip: {
                    y: {
                        formatter: function(val) {
                            return new Intl.NumberFormat('vi-VN').format(val) + ' VND';
                        }
                    }
                },
                colors: ['#00b894', '#e74c3c']
            };

            // Khởi tạo biểu đồ
            var revenuePerMonthChart = new ApexCharts(document.querySelector("#revenuePerMonthChart"),
                revenuePerMonthChartOptions);
            revenuePerMonthChart.render();
            
        });
    </script>
    <script>
        var coursesData = {!! $coursesData ?? '[]' !!};
        var studentsData = {!! $studentsData ?? '[]' !!};
        var ordersData = {!! $ordersData ?? '[]' !!};
        var lecturersData = {!! $lecturersData ?? '[]' !!};

        console.log("coursesData:", coursesData);
        console.log("studentsData:", studentsData);
        console.log("ordersData:", ordersData);
        console.log("lecturersData:", lecturersData);
    </script>
    <script>
        var chartMonthLabels = @json($chartMonthLabels);
        var chartMonthSeries = @json($chartMonthSeries);
        var chartProfitSeries = @json($chartProfitSeries);

        console.log("chartMonthLabels:", chartMonthLabels);
        console.log("chartMonthSeries:", chartMonthSeries);
        console.log("chartProfitSeries:", chartProfitSeries);
    </script>
@endpush
