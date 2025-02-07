@extends('layouts.master')

@section('content')
    <div class="container my-5">
        <div class="card">
            <div class="card-header">
                <h2>Thống kê tổng quan</h2>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="card bg-primary text-white">
                            <div class="card-body">
                                <h5>Tổng doanh thu</h5>
                                <h3>{{ number_format($totalRevenue, 0, ',', '.') }} VNĐ</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-success text-white">
                            <div class="card-body">
                                <h5>Tổng số khóa học</h5>
                                <h3>{{ $totalCourses }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-warning text-white">
                            <div class="card-body">
                                <h5>Tổng số người dùng</h5>
                                <h3>{{ $totalUsers }}</h3>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="my-2" id="noDataMessage" style="display:none;">Không có dữ liệu doanh thu</div>
                <canvas class="apex-charts" id="revenueChart" width="400" height="200"></canvas>
            </div>

            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
            <script>
                fetch('/admin/statistics/revenue-by-time')
                    .then(response => response.json())
                    .then(data => {
                        let labels = data.length ? data.map(d => d.date) : ['N/A'];
                        let revenues = data.length ? data.map(d => d.revenue) : [0];

                        // Vẽ biểu đồ với dữ liệu (nếu có) hoặc biểu đồ trống
                        new Chart(document.getElementById("revenueChart"), {
                            type: 'line',
                            data: {
                                labels: labels,
                                datasets: [{
                                    label: "Doanh thu theo ngày",
                                    data: revenues,
                                    borderColor: 'blue',
                                    fill: false
                                }]
                            },
                            options: {
                                responsive: true,
                                scales: {
                                    x: {
                                        type: 'category',
                                        labels: labels
                                    },
                                    y: {
                                        beginAtZero: true
                                    }
                                }
                            }
                        });
                    })
                    .catch(error => console.error('Error:', error));
            </script>
        </div>
    </div>
@endsection
