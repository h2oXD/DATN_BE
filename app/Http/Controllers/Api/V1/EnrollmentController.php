<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Models\Lesson;
use Illuminate\Http\Request;

class EnrollmentController extends Controller
{
    /**
     * @OA\Get(
     *     path="/user/courses",
     *     summary="Lấy danh sách khoá học đã đăng ký của người dùng kèm tiến độ",
     *     description="API này trả về danh sách các khoá học mà người dùng đã đăng ký, bao gồm thông tin khoá học và tiến độ học tập.",
     *     tags={"User Courses"},
     *     security={{ "bearerAuth":{} }},
     *     @OA\Response(
     *         response=200,
     *         description="Danh sách khoá học của người dùng",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="user_id", type="integer", example=10),
     *                     @OA\Property(property="course_id", type="integer", example=5),
     *                     @OA\Property(property="status", type="string", example="active"),
     *                     @OA\Property(property="course", type="object",
     *                         @OA\Property(property="id", type="integer", example=5),
     *                         @OA\Property(property="title", type="string", example="Khoá học Laravel từ cơ bản đến nâng cao"),
     *                         @OA\Property(property="thumbnail", type="string", example="https://example.com/thumbnail.jpg"),
     *                         @OA\Property(property="description", type="string", example="Khoá học Laravel chi tiết."),
     *                         @OA\Property(property="price_regular", type="number", example=199.99),
     *                         @OA\Property(property="price_sale", type="number", example=99.99),
     *                         @OA\Property(property="status", type="string", example="published")
     *                     ),
     *                     @OA\Property(property="progress", type="object",
     *                         @OA\Property(property="completed_lessons", type="integer", example=15),
     *                         @OA\Property(property="total_lessons", type="integer", example=30),
     *                         @OA\Property(property="progress_percentage", type="number", example=50.0)
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized - Người dùng chưa đăng nhập hoặc token không hợp lệ",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthorized")
     *         )
     *     )
     * )
     */

    public function getUserCoursesWithProgress(Request $request)
    {
        $userId = $request->user()->id; // Lấy user_id từ request

        // Lấy danh sách các khóa học mà user đã đăng ký với 3 trạng thái
        $enrollments = Enrollment::where('user_id', $userId)
            ->whereIn('status', ['active', 'canceled', 'completed'])
            ->with(['course', 'progress'])
            ->get()
            ->unique('course_id'); // Chỉ lấy 1 bản ghi duy nhất cho mỗi khóa học

        return response()->json([
            'status' => 'success',
            'data' => $enrollments->values() // Reset lại key để tránh lỗi index
        ]);
    }

    public function showUserEnrollmentCourse($courses_id)
    {
        $enrollment = request()->user()->enrollments()->where('course_id', $courses_id)->first();
        if (!$enrollment) {
            return response()->json([
                'status' => 403,
                'message' => 'Người dùng chưa đăng ký khoá học này'
            ], 403);
        }

        $course = $enrollment->course()->with(
            'sections',
            'sections.lessons',
            'sections.lessons.videos',
            'sections.lessons.documents',
            'sections.lessons.codings',
            'sections.lessons.quizzes',
            )->first();

        return response()->json([
            'data' => $course
        ]);
    }

    public function showLesson($lesson_id)
    {
        $lesson = Lesson::with('videos','documents','quizzes','codings')->find($lesson_id);
        return response()->json([
            'data' => $lesson
        ],200);
    }
}
