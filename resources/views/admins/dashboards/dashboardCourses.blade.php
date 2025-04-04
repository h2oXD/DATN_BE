@extends('layouts.master')

@section('title')
    Dashboard Courses
@endsection

@section('content')
    <section class="container-fluid p-4">
        <div class="row">
            <div class="col-lg-12 col-md-12 col-12">
                <div
                    class="border-bottom pb-3 mb-3 d-flex flex-column flex-lg-row gap-3 justify-content-between align-items-lg-center">
                    <div>
                        <h1 class="mb-0 h2 fw-bold">Thống kê khóa học</h1>
                    </div>
                </div>
            </div>
        </div>

        <div class="row gy-4 my-2 mb-4">
            <div class="col-lg-6">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">Số lượng khóa học theo danh mục</h4>
                    </div>
                    <div class="card-body">
                        <div id="coursesByCategoryChart" class="apex-charts" style="height: 300px;"></div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">Số lượng học viên đăng ký theo khóa học</h4>
                    </div>
                    <div class="card-body">
                        <div id="enrollmentsByCourseChart" class="apex-charts" style="height: 300px;"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row gy-4 my-2 mb-4">
            <div class="col-lg-6">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">Phương thức thanh toán</h4>
                    </div>
                    <div class="card-body">
                        <div id="paymentMethodsChart" class="apex-charts" style="height: 300px;"></div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">Top 5 khóa học được mua nhiều nhất</h4>
                    </div>
                    <div class="card-body">
                        <div id="mostPurchasedCoursesChart" class="apex-charts" style="height: 300px;"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row gy-4 my-2 mb-4">
            <div class="col-lg-6">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">Tỷ lệ khóa học miễn phí và trả phí</h4>
                    </div>
                    <div class="card-body">
                        <div id="freePaidCoursesChart" class="apex-charts" style="height: 300px;"></div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">Tỷ lệ hoàn thành khóa học</h4>
                    </div>
                    <div class="card-body">
                        <div id="completionRateChart" class="apex-charts" style="height: 300px;"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scriptss')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Biểu đồ số lượng khóa học theo danh mục
            const coursesByCategoryChart = new ApexCharts(document.querySelector("#coursesByCategoryChart"), {
                chart: {
                    type: 'pie'
                },
                series: @json($coursesByCategory->pluck('courses_count')),
                labels: @json($coursesByCategory->pluck('name'))
            });

            coursesByCategoryChart.render();

            // Biểu đồ số lượng học viên đăng ký theo khóa học
            const enrollmentsData = @json($enrollmentsByCourse);

            console.log(enrollmentsData);

            const enrollmentsByCourseChart = new ApexCharts(document.querySelector("#enrollmentsByCourseChart"), {
                chart: {
                    type: 'bar'
                },
                series: [{
                    name: 'Số lượng',
                    data: enrollmentsData.map(item => item
                        .enrollments_count)
                }],
                xaxis: {
                    categories: enrollmentsData.map(item => item.title)
                }
            });

            enrollmentsByCourseChart.render();

            // Biểu đồ so sánh số lần mua bằng ví và ngân hàng
            const paymentMethodsChart = new ApexCharts(document.querySelector("#paymentMethodsChart"), {
                chart: {
                    type: 'pie'
                },
                series: @json($paymentMethods->pluck('total')),
                labels: @json($paymentMethods->pluck('payment_method'))
            });
            paymentMethodsChart.render();

            // Biểu đồ hiển thị khóa học được mua nhiều nhất
            const mostPurchasedCoursesChart = new ApexCharts(document.querySelector("#mostPurchasedCoursesChart"), {
                chart: {
                    type: 'bar'
                },
                series: [{
                    name: 'Số lượng',
                    data: @json($mostPurchasedCourses->pluck('enrollments_count'))
                }],
                xaxis: {
                    categories: @json($mostPurchasedCourses->pluck('title'))
                }
            });
            mostPurchasedCoursesChart.render();
            console.log(@json($enrollmentsByCourse));

            // Biểu đồ tỷ lệ khóa học miễn phí và trả phí
            const freePaidCoursesChart = new ApexCharts(document.querySelector("#freePaidCoursesChart"), {
                chart: {
                    type: 'pie'
                },
                series: [@json($freePaidCourses->free_courses), @json($freePaidCourses->paid_courses)],
                labels: ['Miễn phí', 'Trả phí']
            });
            freePaidCoursesChart.render();

            // Biểu đồ tỷ lệ hoàn thành khóa học
            const completionRateChart = new ApexCharts(document.querySelector("#completionRateChart"), {
                chart: {
                    type: 'pie'
                },
                series: [@json($completionRate->completed), @json($completionRate->total - $completionRate->completed)],
                labels: ['Hoàn thành', 'Chưa hoàn thành']
            });
            completionRateChart.render();
        });
    </script>
@endpush
