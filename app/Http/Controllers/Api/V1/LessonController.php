<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Section;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LessonController extends Controller
{
    public function createLesson(Request $request, $course_id, $section_id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'title' => 'required',
                'description' => 'nullable|string',
            ]);
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            $user_id = $request->user()->id;
            $course = Course::where('user_id', $user_id)->find($course_id);
            if (!$course) {
                return response()->json([
                    'message' => 'Không tìm thấy khóa học'
                ], 404);
            }
            $section = Section::where('course_id', $course_id)->find($section_id);
            if (!$section) {
                return response()->json([
                    'message' => 'Không tìm thấy section'
                ], 404);
            }

            // Lấy order lớn nhất hiện tại trong section và tăng thêm 1
            $maxOrder = $section->lessons()->max('order') ?? 0;
            $newOrder = $maxOrder + 1;

            // Tạo mới Lesson
            $lesson = $section->lessons()->create([
                'title' => $request->title,
                'description' => $request->description,
                'order' => $newOrder, // Tự động tăng order
                'course_id' => $course_id,
            ]);

            return response()->json([
                'lesson' => $lesson,
                'message' => 'Tạo mới lesson thành công',
            ], 201);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Lỗi server',
                'error' => $th->getMessage(),
            ], 500);
        }
    }
    public function updateLesson(Request $request, $course_id, $section_id, $lesson_id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'title' => 'required',
                'description' => 'nullable|string',
                'order' => 'required',
            ]);
            $user_id = $request->user()->id;
            $course = Course::where('user_id', $user_id)->find($course_id);
            if (!$course) {
                return response()->json([
                    'message' => 'Không tìm thấy khóa học'
                ], 404);
            }
            $section = Section::where('course_id', $course_id)->find($section_id);
            if (!$section) {
                return response()->json([
                    'message' => 'Không tìm thấy section'
                ], 404);
            }
            $lesson = Lesson::where('section_id', $section_id)->find($lesson_id);

            if (!$lesson) {
                return response()->json([
                    'message' => 'Không tìm thấy lesson'
                ], 404);
            }
            $lesson->update($request->all());
            return response()->json([
                'lesson' => $lesson,
                'message' => 'Cập nhật lesson thành công',
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Lỗi server',
                'error' => $th,
            ], 500);
        }
    }
    public function destroyLesson(Request $request, $course_id, $section_id, $lesson_id)
    {
        try {
            $user_id = $request->user()->id;

            // Kiểm tra xem khóa học có thuộc về user không
            $course = Course::where('user_id', $user_id)->find($course_id);
            if (!$course) {
                return response()->json([
                    'message' => 'Không tìm thấy khóa học'
                ], 404);
            }

            // Kiểm tra xem section có tồn tại không
            $section = Section::where('course_id', $course_id)->find($section_id);
            if (!$section) {
                return response()->json([
                    'message' => 'Không tìm thấy section'
                ], 404);
            }

            // Kiểm tra xem lesson có tồn tại không
            $lesson = Lesson::where('section_id', $section_id)->find($lesson_id);
            if (!$lesson) {
                return response()->json([
                    'message' => 'Không tìm thấy lesson'
                ], 404);
            }

            // Lưu order của lesson trước khi xóa
            $deletedOrder = $lesson->order;

            // Xóa lesson
            $lesson->delete();

            // Cập nhật lại thứ tự order cho các lesson còn lại trong section
            Lesson::where('section_id', $section_id)
                ->where('order', '>', $deletedOrder) // Chỉ cập nhật các lesson có thứ tự lớn hơn lesson đã xóa
                ->decrement('order'); // Giảm order đi 1

            return response()->json([
                'message' => 'Xóa lesson thành công và cập nhật lại thứ tự',
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Lỗi server',
                'error' => $th->getMessage(),
            ], 500);
        }
    }
}
