<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\Course;
use App\Models\Lecturer;
use App\Models\User;


class StatisticsController extends Controller
{

    public function index()
    {
        $totalRevenue = Transaction::where('status', 'completed')->sum('amount');
        $totalCourses = Course::count();
        $totalUsers = User::count();

        return view('admins.dashboards.statistics', compact('totalRevenue', 'totalCourses', 'totalUsers'));
    }


    public function totalRevenue()
    {
        $total = Transaction::where('status', 'completed')->sum('amount');
        return response()->json(['total_revenue' => $total]);
    }


    public function revenueByCourse()
    {
        $data = Transaction::where('status', 'completed')
            ->join('courses', 'transactions.course_id', '=', 'courses.id')
            ->groupBy('transactions.course_id', 'courses.title')
            ->selectRaw('courses.title as course, SUM(transactions.amount) as revenue')
            ->orderByDesc('revenue')
            ->get();

        return response()->json($data);
    }


    public function revenueByLecturer()
    {
        $data = Transaction::where('status', 'completed')
            ->join('lecturers', 'transactions.lecturer_id', '=', 'lecturers.id')
            ->join('users', 'lecturers.user_id', '=', 'users.id')
            ->groupBy('transactions.lecturer_id', 'users.name')
            ->selectRaw('users.name as lecturer, SUM(transactions.amount) as revenue')
            ->orderByDesc('revenue')
            ->get();

        return response()->json($data);
    }


    public function revenueByTime(Request $request)
    {
        $type = $request->input('type', 'day'); // Mặc định theo ngày

        switch ($type) {
            case 'month':
                $dateFormat = "DATE_FORMAT(transaction_date, '%Y-%m')"; // YYYY-MM
                break;
            case 'year':
                $dateFormat = "YEAR(transaction_date)"; // YYYY
                break;
            default:
                $dateFormat = "DATE(transaction_date)"; // YYYY-MM-DD
        }

        $data = Transaction::where('status', 'completed')
            ->selectRaw("$dateFormat as date, SUM(amount) as revenue")
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        // Nếu không có dữ liệu, trả về mảng rỗng
        if ($data->isEmpty()) {
            return response()->json([]);
        }

        return response()->json($data);
    }


    public function countStats()
    {
        return response()->json([
            'courses' => Course::count(),
            'lecturers' => Lecturer::count(),
            'students' => Transaction::distinct('student_id')->count()
        ]);
    }
}
