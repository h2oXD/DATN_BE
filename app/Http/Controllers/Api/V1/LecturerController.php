<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreCourseRequest;
use App\Http\Requests\Api\UpdateCourseRequest;
use App\Models\Coding;
use App\Models\Course;
use App\Models\Document;
use App\Models\Lecturer;
use App\Models\Lesson;
use App\Models\Quiz;
use App\Models\Section;
use App\Models\TransactionWallet;
use App\Models\Video;
use App\Models\Wallet;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LecturerController extends Controller
{
    public function dashboard()
    {
        return response()->json([
            'data' => request()->user()
        ]);
    }
    public function getLecturerInfo()
    {
        return response()->json([
            'message' => 'Lấy dữ liệu thành công',
            'lecturer' => request()->user()
        ], Response::HTTP_CREATED);
    }
    public function statistics(Request $request)
    {
        $user = $request->user();

        $totalCourses = Course::where('user_id', $user->id)
            ->where('status', 'published')
            ->count();

        // Lấy danh sách khóa học của giảng viên kèm theo tổng số học viên 
        $coursesWithStudents = Course::where('user_id', $user->id)
            ->where('status', 'published')
            ->withCount('enrollments')
            ->get()
            ->map(function ($course) {
                return [
                    'id' => $course->id,
                    'title' => $course->title,
                    'enrollments_count' => $course->enrollments_count,
                ];
            });

        $totalRevenue = $user->wallet?->transaction_wallet()
            ->where('type', 'profit')
            ->where('status', 'success')
            ->sum('amount') ?? 0;

        return response()->json([
            'total_courses' => $totalCourses,
            'total_revenue' => $totalRevenue,
            'courses' => $coursesWithStudents,
        ]);
    }

}
