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
use App\Models\User;
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

    public function show(string $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'message' => 'Không tìm thấy người dùng'
            ], 404);
        }

        // Lấy ID của giảng viên đang đăng nhập
        $instructorId = auth()->id();

        // Lấy thông tin đăng ký khóa học của người dùng trong các khóa học của giảng viên
        $enrollments = Enrollment::where('user_id', $id)
            ->whereIn('course_id', function ($query) use ($instructorId) {
                $query->select('id')
                    ->from('courses')
                    ->where('user_id', $instructorId)
                    ->where('status', 'published'); // Chỉ lấy các khóa học đã xuất bản
            })
            ->with(['course', 'progress']) // Nạp trước thông tin khóa học và tiến độ
            ->get();

        // Chuẩn bị danh sách các khóa học đang học
        $currentCourses = $enrollments
            ->where('status', 'active')
            ->map(function ($enrollment) {
                return [
                    'course_id' => $enrollment->course_id,
                    'title' => $enrollment->course->title,
                    'enrolled_at' => $enrollment->enrolled_at, // Trả về mặc định
                    'progress_percent' => $enrollment->progress ? (float) $enrollment->progress->progress_percent : 0,
                    'progress_status' => $enrollment->progress ? $enrollment->progress->status : 'in_progress',
                ];
            })->values();

        // Chuẩn bị lịch sử khóa học (tất cả các khóa học đã đăng ký)
        $courseHistory = $enrollments
            ->where('status', 'completed')
            ->map(function ($enrollment) {
                return [
                    'course_id' => $enrollment->course_id,
                    'title' => $enrollment->course->title,
                    'status' => $enrollment->status,
                    'enrolled_at' => $enrollment->enrolled_at,
                    'completed_at' => $enrollment->completed_at,
                    'progress_percent' => $enrollment->progress ? (float) $enrollment->progress->progress_percent : 0,
                ];
            })->values();

        return response()->json([
            'message' => 'Hiển thị thông tin người dùng thành công',
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone_number' => $user->phone_number,
                // 'bio' => $user->bio,
                'country' => $user->country,
                'province' => $user->province,
                'birth_date' => $user->birth_date,
                'gender' => $user->gender,
                'linkedin_url' => $user->linkedin_url,
                'website_url' => $user->website_url,
                // 'bank_name' => $user->bank_name,
                // 'bank_nameUser' => $user->bank_nameUser,
                // 'bank_number' => $user->bank_number,
                'profile_picture' => $user->profile_picture
                    ? asset('storage/' . $user->profile_picture)
                    : null,
                'created_at' => $user->created_at, // Trả về mặc định
                'updated_at' => $user->updated_at, // Trả về mặc định
                'current_courses' => $currentCourses, // Các khóa học mà học viên đang học
                'course_history' => $courseHistory, // Tất cả các khóa học mà học viên đã đăng ký
            ]
        ], 200);
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
    public function getAllStudents(Request $request)
    {
        $lecturer = $request->user();
        // Lấy tất cả ID khóa học của giảng viên
        $courseIds = Course::where('user_id', $lecturer->id)->pluck('id');

        // Nếu không có khóa học nào
        if ($courseIds->isEmpty()) {
            return response()->json([
                'message' => 'Giảng viên chưa có khóa học nào.',
            ], Response::HTTP_NOT_FOUND);
        }

        // Lấy danh sách học viên đăng ký học các khóa học 
        $studentIds = Enrollment::whereIn('course_id', $courseIds)
            ->distinct()
            ->pluck('user_id');

        if ($studentIds->isEmpty()) {
            return response()->json([
                'message' => 'Chưa có học viên nào đăng ký các khóa học của bạn.',
            ], Response::HTTP_OK);
        }

        // Lấy thông tin học viên từ bảng users
        $students = User::whereIn('id', $studentIds)
            ->get(['id', 'name', 'email', 'phone_number', 'profile_picture']);

        return response()->json([
            'lecturer_id' => $lecturer->id,
            'lecturer_name' => $lecturer->name,
            'total_students' => $students->count(),
            'students' => $students,
        ]);
    }
}
