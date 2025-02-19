<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Progress;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class StudyController extends Controller
{
    public function getCourseInfo(Request $request, $userId, $courseId)
    {
        if ($request->user()->id != $userId) {
            return response()->json(['message' => 'Unauthorized.'], Response::HTTP_FORBIDDEN);
        }

        $enrollment = Enrollment::where('user_id', $userId)
            ->where('course_id', $courseId)
            ->first();

        if (!$enrollment) {
            return response()->json(['message' => 'Bạn chưa đăng ký khóa học này.'], Response::HTTP_NOT_FOUND);
        }

        $progress = Progress::where('user_id', $userId)
            ->where('course_id', $courseId)
            ->first();

        $progressPercent = $progress ? $progress->progress_percent : 0;

        switch ($enrollment->status) {
            case 'active':
                $course = Course::with([
                    'sections.lessons.documents',
                    'sections.lessons.codings',
                    'sections.lessons.videos',
                    'sections.lessons.quizzes.questions',
                ])
                    ->where('status', 'published')
                    ->findOrFail($courseId);

                return response()->json([
                    'course' => $course,
                    'progress_percent' => $progressPercent,
                ], Response::HTTP_OK);

            case 'canceled':
                return response()->json(['message' => 'Khóa học này đã bị hủy.'], Response::HTTP_BAD_REQUEST);

            case 'completed':
                $course = Course::where('status', 'published')->find($courseId);
                if (!$course) {
                    return response()->json(['message' => 'Khóa học này chưa được đẩy lên.'], Response::HTTP_NOT_FOUND);
                }
                return response()->json(['message' => 'Khóa học này đã hoàn thành.'], Response::HTTP_OK);

            default:
                return response()->json(['message' => 'Trạng thái khóa học không hợp lệ.'], Response::HTTP_BAD_REQUEST);
        }
    }
}