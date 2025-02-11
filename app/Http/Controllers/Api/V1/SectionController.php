<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Section;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SectionController extends Controller
{
    public function createSection(Request $request, $course_id)
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
                    'message' => 'Không tìm thấy khoá học'
                ], 404);
            }

            // Lấy order lớn nhất hiện tại và tăng thêm 1
            $maxOrder = $course->sections()->max('order') ?? 0;
            $newOrder = $maxOrder + 1;

            // Tạo mới Section
            $section = $course->sections()->create([
                'title' => $request->title,
                'description' => $request->description,
                'order' => $newOrder,
            ]);
            return response()->json([
                'section' => $section,
                'message' => 'Tạo mới section thành công',
            ], 201);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Lỗi server',
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    public function updateSection(Request $request, $course_id, $section_id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'title' => 'required',
                'description' => 'nullable|string',
                'order' => 'required'
            ]);
            $user_id = $request->user()->id;
            $course = Course::where('user_id', $user_id)->find($course_id);
            if (!$course) {
                return response()->json([
                    'message' => 'Không tìm thấy khoá học'
                ], 404);
            }
            $section = Section::where('course_id', $course_id)->find($section_id);
            if (!$section) {
                return response()->json([
                    'message' => 'Không tìm thấy section'
                ], 404);
            }
            $section->update($request->all());
            return response()->json([
                'section' => $section,
                'message' => 'Cập nhật section thành công'
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Lỗi server',
                'error' => $th
            ], 500);
        }
    }
    public function destroySection(Request $request, $course_id, $section_id)
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

            // Lấy giá trị order trước khi xóa
            $deletedOrder = $section->order;

            // Xóa section
            $section->delete();

            // Cập nhật lại thứ tự order cho các section còn lại
            Section::where('course_id', $course_id)
                ->where('order', '>', $deletedOrder) // Chỉ cập nhật các section có thứ tự lớn hơn section đã xóa
                ->decrement('order'); // Giảm order đi 1

            return response()->json([
                'message' => 'Xóa section thành công và cập nhật lại thứ tự',
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Lỗi server',
                'error' => $th->getMessage(),
            ], 500);
        }
    }
}
