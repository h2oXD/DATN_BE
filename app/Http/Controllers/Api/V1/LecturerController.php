<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreCourseRequest;
use App\Http\Requests\Api\UpdateCourseRequest;
use App\Models\Coding;
use App\Models\Course;
use App\Models\Document;
use App\Models\Enrollment;
use App\Models\Lecturer;
use App\Models\Lesson;
use App\Models\Quiz;
use App\Models\Section;
use App\Models\Transaction;
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
    /**
     * @OA\Get(
     *     path="/lecturer/statistics",
     *     summary="Lấy thống kê tổng số khóa học, tổng thu nhập, số lượng học viên và doanh thu theo tháng của giảng viên",
     *     tags={"Lecturer"},
     *     security={{"sanctum":{}}}, 
     *     @OA\Response(
     *         response=200,
     *         description="Thống kê thành công",
     *         @OA\JsonContent(
     *             @OA\Property(property="total_courses", type="integer", example=5),
     *             @OA\Property(property="total_students", type="integer", example=500),
     *             @OA\Property(property="total_revenue", type="number", format="float", example=15000000),
     *             @OA\Property(property="monthly_revenue", type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="month", type="string", example="2024-03"),
     *                     @OA\Property(property="total", type="number", format="float", example=5000000)
     *                 )
     *             ),
     *             @OA\Property(property="monthly_students", type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="month", type="string", example="2024-03"),
     *                     @OA\Property(property="total_students", type="integer", example=100)
     *                 )
     *             ),
     *             @OA\Property(property="top_selling_courses", type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="title", type="string", example="Khóa học Laravel"),
     *                     @OA\Property(property="enrollments_count", type="integer", example=300)
     *                 )
     *             ),
     *             @OA\Property(property="courses_&_students", type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=2),
     *                     @OA\Property(property="title", type="string", example="Khóa học PHP"),
     *                     @OA\Property(property="enrollments_count", type="integer", example=200)
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Chưa xác thực",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthorized")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Lỗi hệ thống",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Lỗi hệ thống: [chi tiết lỗi]")
     *         )
     *     )
     * )
     */

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
            ->get();

        $totalStudents = $coursesWithStudents->sum('enrollments_count'); // Tổng số học viên đăng ký tất cả các khóa

        // Format dữ liệu khóa học
        $coursesWithStudents = $coursesWithStudents->map(function ($course) {
            return [
                'id' => $course->id,
                'title' => $course->title,
                'enrollments_count' => $course->enrollments_count,
            ];
        });

        $totalRevenue = 0;

        foreach ($coursesWithStudents as $c) {
            $courseRevenue = Transaction::where('course_id', $c['id'])->sum('amount');
            $totalRevenue += $courseRevenue * 0.7;
        }

        // Doanh thu theo tháng 
        $monthlyRevenue = Transaction::selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, SUM(amount) * 0.7 as total')
            ->whereIn('course_id', Course::where('user_id', $user->id)->pluck('id'))
            ->groupBy('month')
            ->orderBy('month', 'desc')
            ->get();

        // Học viên theo tháng
        $monthlyStudents = Enrollment::selectRaw('DATE_FORMAT(enrolled_at, "%Y-%m") as month, COUNT(id) as total_students')
            ->whereIn('course_id', Course::where('user_id', $user->id)->pluck('id'))
            ->groupBy('month')
            ->orderBy('month', 'desc')
            ->get();

        // Top 3 khóa học bán chạy nhất
        $topCourses = Course::where('user_id', $user->id)
            ->where('status', 'published')
            ->withCount('enrollments')
            ->orderByDesc('enrollments_count')
            ->limit(3)
            ->get()
            ->map(function ($course) {
                return [
                    'id' => $course->id,
                    'title' => $course->title,
                    'enrollments_count' => $course->enrollments_count,
                ];
            });

        return response()->json([
            'total_courses' => $totalCourses, // Tổng số khóa học
            'total_students' => $totalStudents, // Tổng số học viên
            'total_revenue' => $totalRevenue, // Tổng doanh thu
            'monthly_revenue' => $monthlyRevenue, // Doanh thu theo tháng
            'monthly_students' => $monthlyStudents, // Tổng số học viên đăng kí theo tháng
            'top_selling_courses' => $topCourses, // Top 3 khóa học bán chạy
            'courses_&_students' => $coursesWithStudents, // Khóa học và tổng số học viên trong mỗi khóa
        ]);
    }
}
