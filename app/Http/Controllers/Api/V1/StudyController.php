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
            return response()->json(['message' => 'Học viên bắt đầu bài học.'], Response::HTTP_OK);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Lỗi hệ thống: ' . $th->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR); // Thêm $th->getMessage() để debug
        }
    }
    public function completeLesson(Request $request, $user_id, $course_id, $section_id, $lesson_id)
    {
        try {
            if ($request->user()->id!= $user_id) {
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
            $totalLessons = Lesson::whereHas('section', function ($query) use ($course_id) {
                $query->where('course_id', $course_id);
            })->count();

            $completedLessons = Completion::where('user_id', $user_id)
                ->where('course_id', $course_id)
                ->where('status', 'completed')
                ->count();

            $progressPercent = ($completedLessons / $totalLessons) * 100;

            Progress::updateOrCreate(
                [
                    'user_id' => $user_id,
                    'course_id' => $course_id,
                ],
                [
                    'progress_percent' => $progressPercent,
                    'status' => $progressPercent == 100? 'completed': 'in_progress'
                ]
            );

            return response()->json(['message' => 'Hoàn thành bài học thành công.'], Response::HTTP_OK);

        } catch (\Throwable $th) {
            return response()->json(['message' => 'Lỗi hệ thống: '. $th->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}