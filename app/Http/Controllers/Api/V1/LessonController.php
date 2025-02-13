<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Section;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class LessonController extends Controller
{
    public function store(Request $request, $course_id, $section_id)
    {
        try {
            $course = $request->user()->courses()->with([
                'sections' => function ($query) use ($section_id) {
                    $query->where('id', $section_id);
                }
            ])->find($course_id);

            if (!$course || !$section = $course->sections->first()) {
                return response()->json(['message' => 'Không tìm thấy tài nguyên'], 404); // Combined check
            }

            $validator = Validator::make($request->all(), [
                'title' => 'required',
                'description' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $data = $request->all();

            // Lấy order lớn nhất hiện tại trong section và tăng thêm 1
            $maxOrder = $section->lessons()->max('order') ?? 0;
            $data['order'] = $maxOrder + 1;
            // Tạo mới Lesson
            $lesson = $section->lessons()->create($data);

            return response()->json([
                'lesson' => $lesson,
                'message' => 'Tạo mới lesson thành công',
            ], Response::HTTP_CREATED);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Lỗi server',
                'error' => $th->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    public function update(Request $request, $course_id, $section_id, $lesson_id)
    {
        try {
            $course = $request->user()->courses()->with([
                'sections' => function ($query) use ($section_id) {
                    $query->where('id', $section_id);
                },
                'sections.lessons' => function ($query) use ($lesson_id) {
                    $query->where('id', $lesson_id);
                }
            ])->find($course_id);

            if (!$course || !$course->sections->first() || !$lesson = $course->sections->first()->lessons->first()) {
                return response()->json(['message' => 'Không tìm thấy tài nguyên'], Response::HTTP_NOT_FOUND); // Combined check
            }

            $validator = Validator::make($request->all(), [
                'title' => 'required',
                'description' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $data = $request->all();

            $data['order'] = $lesson->order;

            $lesson->update($data);

            return response()->json([
                'lesson' => $lesson,
                'message' => 'Cập nhật lesson thành công',
            ], Response::HTTP_OK);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Lỗi server',
                'error' => $th,
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    public function destroy(Request $request, $course_id, $section_id, $lesson_id)
    {
        DB::beginTransaction();
        try {
            $course = $request->user()->courses()->with([
                'sections' => function ($query) use ($section_id) {
                    $query->where('id', $section_id);
                },
                'sections.lessons' => function ($query) use ($lesson_id) {
                    $query->where('id', $lesson_id);
                }
            ])->find($course_id);

            if (!$course || !$course->sections->first() || !$lesson = $course->sections->first()->lessons->first()) {
                return response()->json(['message' => 'Không tìm thấy tài nguyên'], Response::HTTP_NOT_FOUND); // Combined check
            }

            // Lưu order của lesson trước khi xóa
            $deletedOrder = $lesson->order;

            // Xóa lesson
            $lesson->delete();

            // Cập nhật lại thứ tự order cho các lesson còn lại trong section
            Lesson::where('section_id', $section_id)
                ->where('order', '>', $deletedOrder) // Chỉ cập nhật các lesson có thứ tự lớn hơn lesson đã xóa
                ->decrement('order'); // Giảm order đi 1

            DB::commit();
            return response()->noContent();
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'message' => 'Lỗi server',
                'error' => $th->getMessage(),
            ], 500);
        }
    }
}
