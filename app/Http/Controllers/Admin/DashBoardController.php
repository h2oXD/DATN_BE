<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
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

        // 1. Tổng doanh thu của giảng viên (toàn bộ thời gian)
        $currentRevenue = Transaction::sum('amount') * 0.7;
        $lastMonthRevenue = Transaction::whereBetween('transaction_date', [$lastMonth->startOfMonth(), $lastMonth->endOfMonth()])
            ->sum('amount') * 0.7;
        $revenueChange = $currentRevenue - $lastMonthRevenue;

        // 2. Tổng số khóa học
        $currentCourses = Course::count();
        $lastMonthCourses = Course::whereBetween('created_at', [$lastMonth->startOfMonth(), $lastMonth->endOfMonth()])->count();
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
            ->orderBy('courses_count', 'desc') // Sắp xếp theo số khóa học giảm dần
            ->take(5) // Lấy 5 giảng viên hàng đầu
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
            'revenuePerDate'
        ));
    }
}
