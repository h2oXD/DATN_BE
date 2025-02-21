<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Completion;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\Progress;
use App\Models\Section;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class StudyController extends Controller
{
    /**
     * @OA\Get(
     * path="/api/users/{user_id}/courses/{course_id}",
     * tags={"Study"},
     * summary="Lấy thông tin khóa học của người dùng",
     * description="Lấy thông tin chi tiết của khóa học mà người dùng đã đăng ký, bao gồm tiến độ và nội dung khóa học.",
     * security={{"sanctum":{}}},
     * @OA\Parameter(
     * name="user_id",
     * in="path",
     * description="ID của người dùng",
     * required=true,
     * @OA\Schema(type="integer", example=1)
     * ),
     * @OA\Parameter(
     * name="course_id",
     * in="path",
     * description="ID của khóa học",
     * required=true,
     * @OA\Schema(type="integer", example=1)
     * ),
     * @OA\Response(
     * response=200,
     * description="Thành công. Trả về thông tin khóa học và tiến độ.",
     * @OA\JsonContent(
     * @OA\Property(property="course", type="object", ref="#/components/schemas/Course"),
     * @OA\Property(property="progress_percent", type="integer", example=50)
     * )
     * ),
     * @OA\Response(
     * response=400,
     * description="Yêu cầu không hợp lệ.",
     * @OA\JsonContent(
     * @OA\Property(property="message", type="string", example="Khóa học này đã bị hủy. Hoặc Trạng thái khóa học không hợp lệ.")
     * )
     * ),
     * @OA\Response(
     * response=401,
     * description="Unauthorized - Chưa đăng nhập hoặc token không hợp lệ.",
     * @OA\JsonContent(
     * @OA\Property(property="message", type="string", example="Unauthenticated.")
     * )
     * ),
     * @OA\Response(
     * response=403,
     * description="Không có quyền truy cập.",
     * @OA\JsonContent(
     * @OA\Property(property="message", type="string", example="Unauthorized.")
     * )
     * ),
     * @OA\Response(
     * response=404,
     * description="Không tìm thấy khóa học hoặc người dùng chưa đăng ký khóa học.",
     * @OA\JsonContent(
     * @OA\Property(property="message", type="string", example="Bạn chưa đăng ký khóa học này. Hoặc Khóa học này chưa được đẩy lên.")
     * )
     * ),
     * @OA\Response(
     * response=500,
     * description="Lỗi máy chủ nội bộ.",
     * @OA\JsonContent(
     * @OA\Property(property="message", type="string", example="Lỗi hệ thống.")
     * )
     * )
     * )
     *
     * @OA\Schema(
     * schema="Course",
     * title="Course Model",
     * description="Thông tin chi tiết của khóa học.",
     * @OA\Property(property="id", type="integer", example=1),
     * @OA\Property(property="title", type="string", example="Khóa học Laravel"),
     * @OA\Property(property="description", type="string", example="Mô tả khóa học Laravel"),
     * @OA\Property(property="status", type="string", example="published"),
     * @OA\Property(property="sections", type="array", @OA\Items(ref="#/components/schemas/Section")),
     * )
     *
     * @OA\Schema(
     * schema="Section",
     * title="Section Model",
     * description="Thông tin chi tiết của section.",
     * @OA\Property(property="id", type="integer", example=1),
     * @OA\Property(property="title", type="string", example="Giới thiệu"),
     * @OA\Property(property="lessons", type="array", @OA\Items(ref="#/components/schemas/Lesson")),
     * )
     *
     * @OA\Schema(
     * schema="Lesson",
     * title="Lesson Model",
     * description="Thông tin chi tiết của lesson.",
     * @OA\Property(property="id", type="integer", example=1),
     * @OA\Property(property="title", type="string", example="Bài 1"),
     * @OA\Property(property="documents", type="array", @OA\Items(ref="#/components/schemas/Document")),
     * @OA\Property(property="codings", type="array", @OA\Items(ref="#/components/schemas/Coding")),
     * @OA\Property(property="videos", type="array", @OA\Items(ref="#/components/schemas/Video")),
     * @OA\Property(property="quizzes", type="array", @OA\Items(ref="#/components/schemas/Quiz")),
     * )
     *
     * @OA\Schema(
     * schema="Document",
     * title="Document Model",
     * description="Thông tin chi tiết của document.",
     * @OA\Property(property="id", type="integer", example=1),
     * @OA\Property(property="title", type="string", example="Tài liệu 1"),
     * )
     *
     * @OA\Schema(
     * schema="Coding",
     * title="Coding Model",
     * description="Thông tin chi tiết của coding.",
     * @OA\Property(property="id", type="integer", example=1),
     * @OA\Property(property="title", type="string", example="Coding 1"),
     * )
     *
     * @OA\Schema(
     * schema="Video",
     * title="Video Model",
     * description="Thông tin chi tiết của video.",
     * @OA\Property(property="id", type="integer", example=1),
     * @OA\Property(property="title", type="string", example="Video 1"),
     * )
     *
     * @OA\Schema(
     * schema="Quiz",
     * title="Quiz Model",
     * description="Thông tin chi tiết của quiz.",
     * @OA\Property(property="id", type="integer", example=1),
     * @OA\Property(property="title", type="string", example="Quiz 1"),
     * @OA\Property(property="questions", type="array", @OA\Items(ref="#/components/schemas/Question")),
     * )
     *
     * @OA\Schema(
     * schema="Question",
     * title="Question Model",
     * description="Thông tin chi tiết của question.",
     * @OA\Property(property="id", type="integer", example=1),
     * @OA\Property(property="title", type="string", example="Câu hỏi 1"),
     * )
     */
    public function getCourseInfo(Request $request, $user_id, $course_id)
    {
        try {
            if ($request->user()->id != $user_id) {
                return response()->json(['message' => 'Unauthorized.'], Response::HTTP_FORBIDDEN);
            }

            $enrollment = Enrollment::where('user_id', $user_id)
                ->where('course_id', $course_id)
                ->first();

            if (!$enrollment) {
                return response()->json(['message' => 'Bạn chưa đăng ký khóa học này.'], Response::HTTP_NOT_FOUND);
            }

            $progress = Progress::where('user_id', $user_id)
                ->where('course_id', $course_id)
                ->first();

            $progressPercent = $progress ? $progress->progress_percent : 0;

            if ($enrollment->status === 'active') {
                $course = Course::with([
                    'sections.lessons.documents',
                    'sections.lessons.codings',
                    'sections.lessons.videos',
                    'sections.lessons.quizzes.questions',
                ])
                    ->where('status', 'published')
                    ->findOrFail($course_id);

                return response()->json([
                    'course' => $course,
                    'progress_percent' => $progressPercent,
                ], Response::HTTP_OK);

            } elseif ($enrollment->status === 'canceled') {
                return response()->json(['message' => 'Khóa học này đã bị hủy.'], Response::HTTP_BAD_REQUEST);

            } elseif ($enrollment->status === 'completed') {
                $course = Course::where('status', 'published')->find($course_id);
                if (!$course) {
                    return response()->json(['message' => 'Khóa học này chưa được đẩy lên.'], Response::HTTP_NOT_FOUND);
                }
                return response()->json(['message' => 'Khóa học này đã hoàn thành.'], Response::HTTP_OK);

            } else {
                return response()->json(['message' => 'Trạng thái khóa học không hợp lệ.'], Response::HTTP_BAD_REQUEST);
            }

        } catch (\Throwable $th) {

            return response()->json(['message' => 'Lỗi hệ thống.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    /**
     * @OA\Post(
     * path="/api/users/{user_id}/courses/{course_id}/sections/{section_id}/lessons/{lesson_id}/start",
     * tags={"Study"},
     * summary="Bắt đầu học một bài học",
     * description="API cho phép người dùng bắt đầu học một bài học trong một khóa học cụ thể.",
     * security={{"sanctum":{}}},
     * @OA\Parameter(
     * name="user_id",
     * in="path",
     * description="ID của người dùng",
     * required=true,
     * @OA\Schema(type="integer", example=1)
     * ),
     * @OA\Parameter(
     * name="course_id",
     * in="path",
     * description="ID của khóa học",
     * required=true,
     * @OA\Schema(type="integer", example=1)
     * ),
     * @OA\Parameter(
     * name="section_id",
     * in="path",
     * description="ID của section",
     * required=true,
     * @OA\Schema(type="integer", example=1)
     * ),
     * @OA\Parameter(
     * name="lesson_id",
     * in="path",
     * description="ID của lesson",
     * required=true,
     * @OA\Schema(type="integer", example=1)
     * ),
     * @OA\Response(
     * response=200,
     * description="Bắt đầu bài học thành công.",
     * @OA\JsonContent(
     * @OA\Property(property="message", type="string", example="Tiến độ bài học được khởi tạo.")
     * )
     * ),
     * @OA\Response(
     * response=400,
     * description="Bài học đã hoàn thành.",
     * @OA\JsonContent(
     * @OA\Property(property="message", type="string", example="Bài học đã hoàn thành.")
     * )
     * ),
     * @OA\Response(
     * response=401,
     * description="Unauthorized - Chưa đăng nhập hoặc token không hợp lệ.",
     * @OA\JsonContent(
     * @OA\Property(property="message", type="string", example="Unauthenticated.")
     * )
     * ),
     * @OA\Response(
     * response=403,
     * description="Không có quyền truy cập.",
     * @OA\JsonContent(
     * @OA\Property(property="message", type="string", example="Unauthorized.")
     * )
     * ),
     * @OA\Response(
     * response=404,
     * description="Không tìm thấy khóa học, section hoặc lesson.",
     * @OA\JsonContent(
     * @OA\Property(property="message", type="string", example="Bạn chưa đăng ký khóa học này. Hoặc Section không tồn tại hoặc không thuộc khóa học này. Hoặc Lesson không tồn tại hoặc không thuộc section này.")
     * )
     * ),
     * @OA\Response(
     * response=500,
     * description="Lỗi máy chủ nội bộ.",
     * @OA\JsonContent(
     * @OA\Property(property="message", type="string", example="Lỗi hệ thống: [Exception message]")
     * )
     * )
     * )
     */
    public function startLesson(Request $request, $user_id, $course_id, $section_id, $lesson_id)
    {
        try {
            if ($request->user()->id != $user_id) {
                return response()->json(['message' => 'Unauthorized.'], Response::HTTP_FORBIDDEN);
            }
            $enrollment = Enrollment::where('user_id', $user_id)
                ->where('course_id', $course_id)
                ->first();

            if (!$enrollment) {
                return response()->json(['message' => 'Bạn chưa đăng ký khóa học này.'], Response::HTTP_NOT_FOUND);
            }
            $section = Section::where('course_id', $course_id)
                ->where('id', $section_id)
                ->first();
            if (!$section) {
                return response()->json(['message' => 'Section không tồn tại hoặc không thuộc khóa học này.'], Response::HTTP_NOT_FOUND);
            }
            $lesson = Lesson::where('section_id', $section_id)
                ->where('id', $lesson_id)
                ->first();
            if (!$lesson) {
                return response()->json(['message' => 'Lesson không tồn tại hoặc không thuộc section này.'], Response::HTTP_NOT_FOUND);
            }
            $completion = Completion::firstOrNew([
                'user_id' => $user_id,
                'course_id' => $course_id,
                'lesson_id' => $lesson_id,
            ]);

            if ($completion->exists && $completion->status === 'completed') {
                return response()->json(['message' => 'Bài học đã hoàn thành.'], Response::HTTP_OK);
            }

            $completion->status = 'in_progress';
            $completion->save();
            return response()->json(['message' => 'Tiến độ bài học được khởi tạo.'], Response::HTTP_OK);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Lỗi hệ thống: ' . $th->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR); // Thêm $th->getMessage() để debug
        }
    }
    /**
     * @OA\Post(
     * path="/api/users/{user_id}/courses/{course_id}/sections/{section_id}/lessons/{lesson_id}/complete",
     * tags={"Study"},
     * summary="Hoàn thành một bài học",
     * description="API cho phép người dùng hoàn thành một bài học trong một khóa học cụ thể.",
     * security={{"sanctum":{}}},
     * @OA\Parameter(
     * name="user_id",
     * in="path",
     * description="ID của người dùng",
     * required=true,
     * @OA\Schema(type="integer", example=1)
     * ),
     * @OA\Parameter(
     * name="course_id",
     * in="path",
     * description="ID của khóa học",
     * required=true,
     * @OA\Schema(type="integer", example=1)
     * ),
     * @OA\Parameter(
     * name="section_id",
     * in="path",
     * description="ID của section",
     * required=true,
     * @OA\Schema(type="integer", example=1)
     * ),
     * @OA\Parameter(
     * name="lesson_id",
     * in="path",
     * description="ID của lesson",
     * required=true,
     * @OA\Schema(type="integer", example=1)
     * ),
     * @OA\Response(
     * response=200,
     * description="Hoàn thành bài học thành công.",
     * @OA\JsonContent(
     * @OA\Property(property="message", type="string", example="Học viên hoàn thành bài học.")
     * )
     * ),
     * @OA\Response(
     * response=401,
     * description="Unauthorized - Chưa đăng nhập hoặc token không hợp lệ.",
     * @OA\JsonContent(
     * @OA\Property(property="message", type="string", example="Unauthenticated.")
     * )
     * ),
     * @OA\Response(
     * response=403,
     * description="Không có quyền truy cập.",
     * @OA\JsonContent(
     * @OA\Property(property="message", type="string", example="Unauthorized.")
     * )
     * ),
     * @OA\Response(
     * response=404,
     * description="Không tìm thấy khóa học, section, lesson hoặc completion.",
     * @OA\JsonContent(
     * @OA\Property(property="message", type="string", example="Bạn chưa đăng ký khóa học này. Hoặc Section không tồn tại hoặc không thuộc khóa học này. Hoặc Lesson không tồn tại hoặc không thuộc section này.")
     * )
     * ),
     * @OA\Response(
     * response=500,
     * description="Lỗi máy chủ nội bộ.",
     * @OA\JsonContent(
     * @OA\Property(property="message", type="string", example="Lỗi hệ thống: [Exception message]")
     * )
     * )
     * )
     */
    public function completeLesson(Request $request, $user_id, $course_id, $section_id, $lesson_id)
    {
        try {
            if ($request->user()->id != $user_id) {
                return response()->json(['message' => 'Unauthorized.'], Response::HTTP_FORBIDDEN);
            }

            // Kiểm tra đăng ký khóa học
            $enrollment = Enrollment::where('user_id', $user_id)
                ->where('course_id', $course_id)
                ->first();

            if (!$enrollment) {
                return response()->json(['message' => 'Bạn chưa đăng ký khóa học này.'], Response::HTTP_NOT_FOUND);
            }

            // Kiểm tra section có thuộc course không
            $section = Section::where('course_id', $course_id)
                ->where('id', $section_id)
                ->first();

            if (!$section) {
                return response()->json(['message' => 'Section không tồn tại hoặc không thuộc khóa học này.'], Response::HTTP_NOT_FOUND);
            }

            // Kiểm tra lesson có thuộc section không
            $lesson = Lesson::where('section_id', $section_id)
                ->where('id', $lesson_id)
                ->first();

            if (!$lesson) {
                return response()->json(['message' => 'Lesson không tồn tại hoặc không thuộc section này.'], Response::HTTP_NOT_FOUND);
            }

            // Cập nhật trạng thái hoàn thành bài học
            $completion = Completion::where('user_id', $user_id)
                ->where('course_id', $course_id)
                ->where('lesson_id', $lesson_id)
                ->firstOrFail();

            if ($completion->status === 'completed') {
                return response()->json(['message' => 'Bài học đã hoàn thành.'], Response::HTTP_OK);
            }

            $completion->status = 'completed';
            $completion->completed_at = now();
            $completion->save();

            // Tính toán và cập nhật % hoàn thành khóa học

            // Lần 1: Lấy total_lessons để tính toán ban đầu
            $totalLessons = Section::where('course_id', $course_id)->sum('total_lessons');

            $completedLessons = Completion::where('user_id', $user_id)
                ->where('course_id', $course_id)
                ->where('status', 'completed')
                ->count();

            // Tính toán progressPercent lần 1
            if ($totalLessons > 0) {
                $progressPercent = ($completedLessons / $totalLessons) * 100;
            } else {
                $progressPercent = 0;
            }

            // Lần 2: Kiểm tra lại total_lessons sau khi cập nhật trạng thái hoàn thành
            $totalLessons = Section::where('course_id', $course_id)->sum('total_lessons');

            // Tính toán lại progressPercent (nếu cần)
            if ($totalLessons > 0) {
                $progressPercent = ($completedLessons / $totalLessons) * 100;
            } else {
                $progressPercent = 0;
            }

            Progress::updateOrCreate(
                [
                    'user_id' => $user_id,
                    'course_id' => $course_id,
                ],
                [
                    'progress_percent' => $progressPercent,
                    'status' => $progressPercent == 100 ? 'completed' : 'in_progress'
                ]
            );

            return response()->json(['message' => 'Học viên hoàn thành bài học.'], Response::HTTP_OK);

        } catch (\Throwable $th) {
            return response()->json(['message' => 'Lỗi hệ thống: ' . $th->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}