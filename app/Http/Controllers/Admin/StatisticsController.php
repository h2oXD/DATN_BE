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
        return view('admins.dashboards.statistics', [
            'totalRevenue' => Transaction::sum('amount'), 
            'totalCourses' => Course::count(),
            'totalUsers'   => User::count(),
        ]);
    }

    public function totalRevenue()
    {
        return response()->json([
            'total_revenue' => Transaction::sum('amount') 
        ]);
    }

    public function revenueByCourse()
    {
        $data = Transaction::with('course:id,title')
            ->get()
            ->groupBy('course.title')
            ->map(fn($transactions) => [
                'course'  => optional($transactions->first()->course)->title, // Kiểm tra null tránh lỗi
                'revenue' => $transactions->sum('amount'),
            ])
            ->values();

        return response()->json($data);
    }

    public function revenueByLecturer()
    {
        $data = Transaction::with(['lecturer.user:id,name'])
            ->get()
            ->groupBy('lecturer.user.name')
            ->map(fn($transactions) => [
                'lecturer' => optional($transactions->first()->lecturer->user)->name, // Kiểm tra null
                'revenue'  => $transactions->sum('amount'),
            ])
            ->values();

        return response()->json($data);
    }

    public function revenueByTime(Request $request)
    {
        $type = $request->input('type', 'day');

        $dateFormats = [
            'day'   => "DATE(transaction_date)",
            'month' => "DATE_FORMAT(transaction_date, '%Y-%m')",
            'year'  => "YEAR(transaction_date)"
        ];

        $format = $dateFormats[$type] ?? $dateFormats['day'];

        $data = Transaction::selectRaw("$format as date, SUM(amount) as revenue")
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        return response()->json($data->isEmpty() ? [] : $data);
    }


    public function countStats()
    {
        // return response()->json([
        //     'courses' => Course::count(),
        //     'lecturers' => Lecturer::count(),
        //     'students' => Transaction::distinct('student_id')->count()
        // ]);
    }
}
