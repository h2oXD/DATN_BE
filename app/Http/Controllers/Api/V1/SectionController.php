<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Section;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class SectionController extends Controller
{
    public function store(Request $request, $course_id)
    {
        try {
            $course = $request->user()->courses()->find($course_id);
            if (!$course) {
                return response()->json([
                    'message' => 'Không tìm thấy khoá học'
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'title' => 'required',
                'description' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $data = $request->all();

            // Lấy order lớn nhất hiện tại và tăng thêm 1
            $maxOrder = $course->sections()->max('order') ?? 0;
            $data['order'] = $maxOrder + 1;

            // Tạo mới Section
            $section = $course->sections()->create($data);
            return response()->json([
                'section' => $section,
                'message' => 'Tạo mới section thành công',
            ], Response::HTTP_CREATED);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Lỗi server',
                'error' => $th->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(Request $request, $course_id, $section_id)
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

            $data['order'] = $section->order;

            $section->update($data);

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
    public function destroy(Request $request, $course_id, $section_id)
    {
        DB::beginTransaction();
        try {
            $course = $request->user()->courses()->with([
                'sections' => function ($query) use ($section_id) {
                    $query->where('id', $section_id);
                }
            ])->find($course_id);

            if (!$course || !$section = $course->sections->first()) {
                return response()->json(['message' => 'Không tìm thấy tài nguyên'], 404); // Combined check
            }

            // Lấy giá trị order trước khi xóa
            $deletedOrder = $section->order;
            // Xóa section
            $section->delete();

            // Cập nhật lại thứ tự order cho các section còn lại
            Section::where('course_id', $course_id)
                ->where('order', '>', $deletedOrder) // Chỉ cập nhật các section có thứ tự lớn hơn section đã xóa
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
