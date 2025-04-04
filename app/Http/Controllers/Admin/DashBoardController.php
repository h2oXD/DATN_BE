<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Review;
use App\Models\Submission;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashBoardController extends Controller
{
    public function index()
    {
        if (Auth::check()) {
            return redirect()->route('admin.dashboard');
        }
        return redirect()->route('admin.login');
    }

    public function filterRevenue(Request $request)
    {
        $query = Transaction::query()
            ->where('status', 'success'); // Lọc giao dịch hoàn tất

        // Lọc ngày
        if ($request->filled('date')) {
            $query->whereDate('transaction_date', $request->date);
        }

        // Lọc tháng
        if ($request->filled('month')) {
            $query->whereMonth('transaction_date', $request->month);
        }

        // Lọc năm
        if ($request->filled('year')) {
            $query->whereYear('transaction_date', $request->year);
        }

        $transactions = $query->selectRaw('DATE(transaction_date) as date, SUM(amount * 0.7) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return response()->json([
            'labels' => $transactions->pluck('date'),
            'series' => $transactions->pluck('total')
        ]);
    }

    public function dashboard()
    {
        $now = Carbon::now();
        $lastMonth = $now->copy()->subMonth();

        $currentRevenue = Transaction::sum('amount');
        $lastMonthRevenue = Transaction::whereBetween('transaction_date', [$lastMonth->startOfMonth(), $lastMonth->endOfMonth()])
            ->sum('amount');
        $revenueChange = $currentRevenue - $lastMonthRevenue;

        //doanh thu theo năm
        $year = now()->year;

        $revenuePerMonth = Transaction::selectRaw('MONTH(transaction_date) as month, SUM(amount) as total_revenue')
            ->whereYear('transaction_date', $year)
            ->groupBy(DB::raw('MONTH(transaction_date)'))
            ->orderBy(DB::raw('MONTH(transaction_date)'))
            ->get()
            ->map(function ($item) {
                return [
                    'month' => 'Tháng ' . $item->month,
                    'total_revenue' => round($item->total_revenue),
                    'total_profit' => round($item->total_revenue * 0.3)
                ];
            });

        // Lấy dữ liệu cho biểu đồ
        $chartMonthLabels = $revenuePerMonth->pluck('month');
        $chartMonthSeries = $revenuePerMonth->pluck('total_revenue');
        $chartProfitSeries = $revenuePerMonth->pluck('total_profit');

        // 2. Tổng số khóa học
        $currentCourses = Course::where('status', 'published')->count();

        $lastMonthCourses = Course::where('status', 'published')
            ->whereBetween('created_at', [$lastMonth->startOfMonth(), $lastMonth->endOfMonth()])
            ->count();

        $coursesChange = $currentCourses - $lastMonthCourses;

        // 3. Tổng số học viên
        $currentStudents = Enrollment::distinct('user_id')->count('user_id');
        $lastMonthStudents = Enrollment::whereBetween('created_at', [$lastMonth->startOfMonth(), $lastMonth->endOfMonth()])
            ->distinct('user_id')->count('user_id');
        $studentsChange = $currentStudents - $lastMonthStudents;

        // 4. Tổng số giảng viên
        $currentInstructors = User::whereHas('courses')->count();
        $lastMonthInstructors = User::whereHas('courses', function ($query) use ($lastMonth) {
            $query->whereBetween('created_at', [$lastMonth->startOfMonth(), $lastMonth->endOfMonth()]);
        })->count();
        $instructorsChange = $currentInstructors - $lastMonthInstructors;

        // 5. Số khóa học đang chờ duyệt
        $pendingCourses = Course::where('status', 'pending')->count();

        // Lấy danh sách giảng viên phổ biến (Popular Instructors)
        $popularInstructors = User::whereHas('courses') // Chỉ lấy users có khóa học
            ->withCount('courses') // Đếm số khóa học
            ->with(['courses' => function ($query) {
                $query->withCount('enrollments') // Đếm số học viên cho mỗi khóa học
                    ->withCount('reviews');    // Đếm số đánh giá cho mỗi khóa học
            }])
            ->orderBy('courses_count', 'desc')
            ->take(5)
            ->get()
            ->map(function ($instructor) {
                // Tính tổng số học viên và đánh giá từ các khóa học
                $instructor->students_count = $instructor->courses->sum('enrollments_count');
                $instructor->reviews_count = $instructor->courses->sum('reviews_count');
                return $instructor;
            });

        // Lấy danh sách khóa học gần đây (Recent Courses)
        $recentCourses = Course::select('id', 'title', 'thumbnail', 'user_id', 'created_at')
            ->with('user:id,name,profile_picture')
            ->where('status', 'published')
            ->latest('created_at')
            ->take(4)
            ->get();


        // Dữ liệu biểu đồ: Tổng doanh thu của tất cả giảng viên
        $revenuePerDate = Transaction::selectRaw('DATE(transaction_date) as date, SUM(amount) * 0.7 as total_revenue')
            ->groupBy(DB::raw('DATE(transaction_date)'))
            ->orderBy('date', 'ASC')
            ->get()
            ->map(function ($item) {
                return [
                    'date' => $item->date,
                    'total_revenue' => round($item->total_revenue)
                ];
            });

        // Mảng ngày tháng và doanh thu
        $chartLabels = $revenuePerDate->pluck('date');
        $chartSeries = $revenuePerDate->pluck('total_revenue');

        // Tổng số đơn hàng (transactions)
        $totalTransactions = Transaction::count();

        // Tổng số lượt đánh giá
        $totalReviews = Review::count();

        // Tổng số học viên đăng ký khoá học
        $totalEnrollments = Enrollment::count();

        // Tổng số bài nộp quiz
        $totalSubmissions = Submission::count();

        $currentProfit = $currentRevenue * 0.3;
        $lastMonthProfit = $lastMonthRevenue * 0.3;
        $profitChange = $currentProfit - $lastMonthProfit;

        $coursesData = Course::where('status', 'published')
            ->selectRaw('DATE(created_at) as date, COUNT(*) as total_courses')
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date', 'ASC')
            ->get()
            ->map(function ($item) {
                return [
                    'date' => $item->date,
                    'total_courses' => $item->total_courses
                ];
            });

        $studentsData = Enrollment::selectRaw('DATE(enrolled_at) as date, COUNT(DISTINCT user_id) as total_students')
            ->groupBy(DB::raw('DATE(enrolled_at)'))
            ->orderBy('date', 'ASC')
            ->get()
            ->map(function ($item) {
                return [
                    'date' => Carbon::parse($item->date)->toDateString(), // Định dạng lại ngày
                    'total_students' => (int) $item->total_students
                ];
            });

        $ordersData = Transaction::selectRaw('DATE(transaction_date) as date, COUNT(id) as total_orders')
            ->groupBy(DB::raw('DATE(transaction_date)'))
            ->orderBy('date', 'ASC')
            ->get()
            ->map(function ($item) {
                return [
                    'date' => $item->date,
                    'total_orders' => $item->total_orders
                ];
            });

        $lecturersData = User::join('user_role', 'users.id', '=', 'user_role.user_id')
            ->where('user_role.role_id', 2)
            ->selectRaw('DATE(users.created_at) as date, COUNT(users.id) as total_lecturers')
            ->groupBy(DB::raw('DATE(users.created_at)'))
            ->orderBy('date', 'ASC')
            ->get()
            ->map(function ($item) {
                return [
                    'date' => $item->date,
                    'total_lecturers' => $item->total_lecturers
                ];
            });

        return view('admins.dashboards.dash-board', compact(
            'currentRevenue',
            'revenueChange',
            'currentCourses',
            'coursesChange',
            'currentStudents',
            'studentsChange',
            'currentInstructors',
            'instructorsChange',
            'pendingCourses',
            'popularInstructors',
            'recentCourses',
            'chartLabels',
            'chartSeries',
            'totalTransactions',
            'totalReviews',
            'totalEnrollments',
            'totalSubmissions',
            'revenuePerDate',
            'coursesData',
            'studentsData',
            'ordersData',
            'lecturersData',
            'currentProfit',
            'profitChange',
            'revenuePerMonth',
            'chartMonthSeries',
            'chartMonthLabels',
            'chartProfitSeries',
        ));
    }

    public function dashboardAnalytics()
    {
        $now = Carbon::now();
        $lastMonth = $now->copy()->subMonth();

        // Doanh thu
        $currentRevenue = Transaction::sum('amount') * 0.7;
        $lastMonthRevenue = Transaction::whereBetween('transaction_date', [$lastMonth->startOfMonth(), $lastMonth->endOfMonth()])
            ->sum('amount') * 0.7;
        $revenueChange = $currentRevenue - $lastMonthRevenue;

        // Tính lợi nhuận (30% tổng doanh thu)
        $currentProfit = $currentRevenue * 0.3;
        $lastMonthProfit = $lastMonthRevenue * 0.3;
        $profitChange = $currentProfit - $lastMonthProfit;

        //doanh thu theo năm
        $year = now()->year;

        $revenuePerMonth = Transaction::selectRaw('MONTH(transaction_date) as month, SUM(amount) as total_revenue')
            ->whereYear('transaction_date', $year)
            ->groupBy(DB::raw('MONTH(transaction_date)'))
            ->orderBy(DB::raw('MONTH(transaction_date)'))
            ->get()
            ->map(function ($item) {
                return [
                    'month' => 'Tháng ' . $item->month,
                    'total_revenue' => round($item->total_revenue),
                    'total_profit' => round($item->total_revenue * 0.3)
                ];
            });

        // Lấy dữ liệu cho biểu đồ
        $chartMonthLabels = $revenuePerMonth->pluck('month');
        $chartMonthSeries = $revenuePerMonth->pluck('total_revenue');
        $chartProfitSeries = $revenuePerMonth->pluck('total_profit');

        // Dữ liệu biểu đồ doanh thu theo ngày
        $revenuePerDate = Transaction::selectRaw('DATE(transaction_date) as date, SUM(amount) * 0.7 as total_revenue')
            ->groupBy(DB::raw('DATE(transaction_date)'))
            ->orderBy('date', 'ASC')
            ->get()
            ->map(function ($item) {
                return [
                    'date' => $item->date,
                    'total_revenue' => round($item->total_revenue),
                    'total_profit' => round($item->total_revenue * 0.3)
                ];
            });

        // Dữ liệu biểu đồ lợi nhuận
        $profitPerDate = $revenuePerDate->pluck('total_profit');
        $chartLabels = $revenuePerDate->pluck('date');
        $chartSeries = $revenuePerDate->pluck('total_revenue');
        $chartSeriesProfit = $profitPerDate;

        // Doanh thu theo tháng
        $monthlyRevenue = Transaction::selectRaw('MONTH(transaction_date) as month, SUM(amount) * 0.7 as total_revenue')
            ->groupBy(DB::raw('MONTH(transaction_date)'))
            ->orderBy('month', 'ASC')
            ->get()
            ->map(function ($item) {
                return [
                    'month' => $item->month,
                    'total_revenue' => round($item->total_revenue)
                ];
            });

        // Dữ liệu biểu đồ doanh thu giữa các tháng
        $monthlyLabels = $monthlyRevenue->pluck('month');
        $monthlySeries = $monthlyRevenue->pluck('total_revenue');

        // Doanh thu theo giảng viên
        $revenueByLecturer = Transaction::join('courses', 'transactions.course_id', '=', 'courses.id')
            ->join('users', 'courses.user_id', '=', 'users.id') // Lấy tên giảng viên
            ->join('user_role', 'courses.user_id', '=', 'user_role.user_id')
            ->join('roles', 'user_role.role_id', '=', 'roles.id')
            ->where('roles.name', 'lecturer') // Lọc giảng viên
            ->selectRaw('users.name as lecturer_name, SUM(transactions.amount) * 0.7 as total_revenue')
            ->groupBy('users.name')
            ->orderBy('total_revenue', 'DESC')
            ->take(5) // Lấy top 5 giảng viên
            ->get()
            ->map(function ($item) {
                return [
                    'lecturer_name' => $item->lecturer_name,
                    'total_revenue' => round($item->total_revenue)
                ];
            });

        $instructorLabels = $revenueByLecturer->pluck('lecturer_name');
        $instructorSeries = $revenueByLecturer->pluck('total_revenue');

        $currentRevenue = Transaction::sum('amount');
        $lastMonthRevenue = Transaction::whereBetween('transaction_date', [$lastMonth->startOfMonth(), $lastMonth->endOfMonth()])
            ->sum('amount');
        $revenueChange = $currentRevenue - $lastMonthRevenue;

        $currentProfit = $currentRevenue * 0.3;
        $lastMonthProfit = $lastMonthRevenue * 0.3;
        $profitChange = $currentProfit - $lastMonthProfit;

        // Doanh thu theo danh mục khóa học
        $revenueByCategory = Transaction::join('courses', 'transactions.course_id', '=', 'courses.id')
            ->join('categories', 'courses.category_id', '=', 'categories.id')
            ->selectRaw('categories.name as category_name, SUM(transactions.amount) * 0.7 as total_revenue')
            ->groupBy('courses.category_id', 'categories.name')
            ->orderBy('total_revenue', 'DESC')
            ->get()
            ->map(function ($item) {
                return [
                    'category_name' => $item->category_name,
                    'total_revenue' => round($item->total_revenue)
                ];
            });

        // Dữ liệu biểu đồ doanh thu theo danh mục khóa học
        $categoryLabels = $revenueByCategory->pluck('category_name');
        $categorySeries = $revenueByCategory->pluck('total_revenue');

        return view('admins.dashboards.DashboardAnalytics', compact(
            'currentRevenue',
            'revenueChange',
            'chartLabels',
            'chartSeries',
            'chartSeriesProfit',
            'profitChange',
            'currentProfit',
            'monthlyLabels',
            'monthlySeries',
            'instructorLabels',
            'instructorSeries',
            'categoryLabels',
            'categorySeries',
            'chartMonthSeries',
            'chartMonthLabels',
            'chartProfitSeries',
            'revenuePerMonth'
        ));
    }
    public function dashboardCourses()
    {
        // Biểu đồ số lượng khóa học theo danh mục
        $coursesByCategory = Category::withCount('courses')->get();

        // Biểu đồ số lượng học viên đăng ký theo khóa học
        $enrollmentsByCourse = DB::table('enrollments')
            ->join('courses', 'courses.id', '=', 'enrollments.course_id')
            ->select('courses.id', 'courses.title', DB::raw('COUNT(enrollments.id) as enrollments_count'))
            ->where('enrollments.status', 'active')
            ->groupBy('courses.id', 'courses.title')
            ->orderBy('enrollments_count', 'desc')
            ->get();

        // Biểu đồ so sánh số lần mua bằng ví và ngân hàng
        $paymentMethods = Transaction::select('payment_method', DB::raw('COUNT(*) as total'))
            ->where('status', 'success')
            ->groupBy('payment_method')
            ->get();

        // Biểu đồ hiển thị top 5 khóa học được mua nhiều nhất
        $mostPurchasedCourses = DB::table('courses')
            ->leftJoin('enrollments', function ($join) {
                $join->on('courses.id', '=', 'enrollments.course_id')
                    ->whereIn('enrollments.status', ['active', 'completed']);
            })
            ->where('courses.status', 'published')
            ->select('courses.id', 'courses.title', DB::raw('count(enrollments.id) as enrollments_count'))
            ->groupBy('courses.id', 'courses.title')
            ->orderByDesc('enrollments_count')
            ->take(5)
            ->get();

        // Biểu đồ tỷ lệ khóa học miễn phí và trả phí
        $freePaidCourses = Course::select(
            DB::raw("COUNT(CASE WHEN is_free = 1 THEN 1 END) as free_courses"),
            DB::raw("COUNT(CASE WHEN is_free = 0 THEN 1 END) as paid_courses")
        )
            ->first();

        // Biểu đồ tỷ lệ hoàn thành khóa học
        $completionRate = Enrollment::select(
            DB::raw("COUNT(CASE WHEN status = 'completed' THEN 1 END) as completed"),
            DB::raw("COUNT(*) as total")
        )
            ->first();

        // Biểu đồ doanh thu theo khóa học
        $revenueByCourse = Transaction::where('status', 'success')
            ->select('course_id', DB::raw('SUM(amount) as total_revenue'))
            ->groupBy('course_id')
            ->with('course')
            ->get();

        return view('admins.dashboards.DashboardCourses', compact(
            'coursesByCategory',
            'enrollmentsByCourse',
            'paymentMethods',
            'mostPurchasedCourses',
            'freePaidCourses',
            'completionRate',
            'revenueByCourse'
        ));
    }
}
