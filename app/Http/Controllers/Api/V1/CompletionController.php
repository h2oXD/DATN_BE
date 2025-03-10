<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Completion;
use App\Models\Lesson;
use Illuminate\Http\Request;

class CompletionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }
    public function showUserCourseProgress($course_id)
    {
        $user_id = request()->user()->id;

        // Tổng số bài học trong khoá học
        $totalLessons = Lesson::whereHas('section', function ($query) use ($course_id) {
            $query->where('course_id', $course_id);
        })->count();

        // Số bài học đã hoàn thành của học viên trong khoá học
        $completedLessons = Completion::where('user_id', $user_id)
            ->where('course_id', $course_id)
            ->where('status', 'completed')
            ->count();

        return response()->json([
            'total_lessons' => $totalLessons,
            'completed_lessons' => $completedLessons,
            'progress' => $totalLessons > 0 ? round(($completedLessons / $totalLessons) * 100, 2) . '%' : '0%',
        ]);
    }
    public function getLatestCourseInProgress($course_id)
    {
        $user_id = request()->user()->id;

        // Lấy khóa học có bài học đang học gần nhất
        $latestCourse = Completion::where('user_id', $user_id)
            ->where('course_id', $course_id)
            ->where('status', 'in_progress')
            ->orderBy('id', 'desc')
            ->first();

        if ($latestCourse) {
            return response()->json([
                'lesson_id' => $latestCourse->lesson_id,
                'status' => 'in_progress',
            ]);
        }

        // Nếu không có bài học đang học, lấy khóa học cuối cùng đã hoàn thành
        $completedCourse = Completion::where('user_id', $user_id)
            ->where('status', 'completed')
            ->where('course_id', $course_id)
            ->first();

        if ($completedCourse) {
            // Lấy bài học cuối cùng của khóa học
            $lastLesson = Lesson::whereHas('section', function ($query) use ($completedCourse) {
                $query->where('course_id', $completedCourse->course_id);
            })->orderBy('order', 'desc')->first();

            return response()->json([
                'course_id' => $completedCourse->course_id,
                'lesson_id' => $lastLesson->id ?? null,
                'status' => 'completed',
            ]);
        }

        return response()->json([
            'message' => 'Không có khóa học nào đang học hoặc đã hoàn thành.'
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
