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
                        <h1 class="mb-0 h2 fw-bold">Thống kê doanh thu</h1>
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
        {{-- biểu đồ thống kê doanh thu giảng viên  --}}
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
                                <input type="date" name="date" class="form-control" value="{{ request('date') }}">
                            </div>
                            <!-- Nút tìm kiếm -->
                            <button type="button" id="searchButton" class="btn btn-success d-flex align-items-center">
                                <i class="fas fa-search me-1"></i> Tìm kiếm
                            </button>
                        </form>
                        <div id="searchStatus" class="alert alert-info" style="display: none;"></div>
                    </div>
                    <div class="card-body">
                        <div id="courseRevenueChart" class="apex-charts" style="height: 300px;"></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Biểu đồ lợi nhuận theo ngày --}}
        <div class="row gy-4 my-2 mb-4">
            <div class="col-lg-6">
                <div class="card h-100">
                    <div
                        class="card-header align-items-center card-header-height d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-0">Biểu đồ lợi nhuận theo ngày</h4>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="profitChart" class="apex-charts" style="height: 300px;"></div>
                    </div>
                </div>
            </div>

            {{-- Biểu đồ so sánh doanh thu giữa các tháng --}}
            <div class="col-lg-6">
                <div class="card h-100">
                    <div
                        class="card-header align-items-center card-header-height d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-0">So sánh doanh thu giữa các tháng</h4>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="monthlyRevenueChart" class="apex-charts" style="height: 300px;"></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Biểu đồ tỷ trọng doanh thu giữa các giảng viên --}}
        {{-- <div class="row gy-4 my-2 mb-4">
            <div class="col-lg-6">
                <div class="card h-100">
                    <div
                        class="card-header align-items-center card-header-height d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-0">Tỷ trọng doanh thu top 5 giảng viên</h4>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="instructorRevenueChart" class="apex-charts" style="height: 300px;"></div>
                    </div>
                </div>
            </div> --}}

            {{-- Biểu đồ doanh thu theo danh mục khóa học --}}
            {{-- <div class="col-lg-6">
                <div class="card h-100">
                    <div
                        class="card-header align-items-center card-header-height d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-0">Doanh thu theo danh mục khóa học</h4>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="categoryRevenueChart" class="apex-charts" style="height: 300px;"></div>
                    </div>
                </div>
            </div> --}}
        </div>

    </section>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Biểu đồ lợi nhuận theo ngày
            const profitChart = new ApexCharts(document.querySelector("#profitChart"), {
                chart: {
                    height: 400,
                    type: 'bar'
                },
                series: [{
                    name: 'Tổng lợi nhuận',
                    data: @json($chartSeriesProfit)
                }],
                xaxis: {
                    categories: @json($chartLabels)
                },
                dataLabels: {
                    enabled: true,
                    formatter: function(val) {
                        return new Intl.NumberFormat('vi-VN').format(val) + " VND";
                    }
                },
                tooltip: {
                    y: {
                        formatter: function(val) {
                            return new Intl.NumberFormat('vi-VN').format(val) + " VND";
                        }
                    }
                }
            });

            // Biểu đồ so sánh doanh thu giữa các tháng
            const monthlyRevenueChart = new ApexCharts(document.querySelector("#monthlyRevenueChart"), {
                chart: {
                    height: 400,
                    type: 'bar'
                },
                series: [{
                    name: 'Doanh thu',
                    data: @json($monthlySeries)
                }],
                xaxis: {
                    categories: @json($monthlyLabels)
                },
                dataLabels: {
                    enabled: true,
                    formatter: function(val) {
                        return new Intl.NumberFormat('vi-VN').format(val) + " VND";
                    }
                },
                tooltip: {
                    y: {
                        formatter: function(val) {
                            return new Intl.NumberFormat('vi-VN').format(val) + " VND";
                        }
                    }
                }
            });

            // Biểu đồ tỷ trọng doanh thu giữa các giảng viên
            const instructorRevenueChart = new ApexCharts(document.querySelector("#instructorRevenueChart"), {
                chart: {
                    height: 400,
                    type: 'pie'
                },
                series: @json($instructorSeries),
                labels: @json($instructorLabels),
                tooltip: {
                    y: {
                        formatter: function(value) {
                            return value.toLocaleString('vi-VN') +
                                ' VND';
                        }
                    }
                }
            });

            // Biểu đồ doanh thu theo danh mục khóa học
            const categoryRevenueChart = new ApexCharts(document.querySelector("#categoryRevenueChart"), {
                chart: {
                    height: 400,
                    type: 'pie'
                },
                series: @json($categorySeries),
                labels: @json($categoryLabels),
                tooltip: {
                    y: {
                        formatter: function(value) {
                            return value.toLocaleString('vi-VN') +
                                ' VND';
                        }
                    }
                }
            });

            profitChart.render();
            monthlyRevenueChart.render();
            instructorRevenueChart.render();
            categoryRevenueChart.render();

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
                },
                dataLabels: {
                    enabled: true,
                    formatter: function(val) {
                        return new Intl.NumberFormat('vi-VN').format(val) + " VND";
                    }
                },
                tooltip: {
                    y: {
                        formatter: function(val) {
                            return new Intl.NumberFormat('vi-VN').format(val) + " VND";
                        }
                    }
                }
            });

            revenueChart.render();

            // Sự kiện click tìm kiếm
            $('#searchButton').on('click', function(e) {
                e.preventDefault();

                const selectedDate = $('input[name="date"]').val();
                const selectedMonth = $('select[name="month"]').val();
                const selectedYear = $('select[name="year"]').val();

                // Kiểm tra nếu không chọn điều kiện nào
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

                        // Kiểm tra xem có dữ liệu trả về không
                        if (response.labels.length === 0) {
                            alert('Không tìm thấy kết quả phù hợp!');
                            // Có thể hiển thị một thông báo khác trong UI nếu cần
                            $('#searchStatus').text('Không tìm thấy kết quả phù hợp.');
                        } else {
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

                            // Hiển thị thông báo tìm kiếm thành công
                            $('#searchStatus').text('Tìm kiếm thành công!');
                        }
                    },
                    error: function(err) {
                        console.error('Lỗi:', err);
                        alert('Đã xảy ra lỗi khi tìm kiếm!');
                        $('#searchStatus').text('Đã xảy ra lỗi khi tìm kiếm!');
                    }
                });
            });

            // Kiểm tra và lấy dữ liệu từ Blade template
            if (typeof chartMonthLabels === 'undefined' || typeof chartMonthSeries === 'undefined') {
                console.error("chartMonthLabels or chartMonthSeries is not defined");
                return;
            }

            // Đảm bảo dữ liệu labels có dạng ["Tháng 1", "Tháng 2", ...]
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
        var chartLabels = @json($chartLabels ?? []);
        var chartSeries = @json($chartSeries ?? []);
        var chartSeriesProfit = @json($chartSeriesProfit ?? []);
        var monthlyLabels = @json($monthlyLabels ?? []);
        var monthlySeries = @json($monthlySeries ?? []);
        var instructorLabels = @json($instructorLabels ?? []);
        var instructorSeries = @json($instructorSeries ?? []);
        var categoryLabels = @json($categoryLabels ?? []);
        var categorySeries = @json($categorySeries ?? []);

        console.log("chartLabels:", chartLabels.length > 0 ? chartLabels : "Không có dữ liệu!");
        console.log("chartSeries:", chartSeries.length > 0 ? chartSeries : "Không có dữ liệu!");
        console.log("chartSeriesProfit:", chartSeriesProfit.length > 0 ? chartSeriesProfit : "Không có dữ liệu!");
        console.log("monthlyLabels:", monthlyLabels.length > 0 ? monthlyLabels : "Không có dữ liệu!");
        console.log("monthlySeries:", monthlySeries.length > 0 ? monthlySeries : "Không có dữ liệu!");
        console.log("instructorLabels:", instructorLabels.length > 0 ? instructorLabels : "Không có dữ liệu!");
        console.log("instructorSeries:", instructorSeries.length > 0 ? instructorSeries : "Không có dữ liệu!");
        console.log("categoryLabels:", categoryLabels.length > 0 ? categoryLabels : "Không có dữ liệu!");
        console.log("categorySeries:", categorySeries.length > 0 ? categorySeries : "Không có dữ liệu!");
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
