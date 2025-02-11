<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Coding;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LessonCodingController extends Controller
{
    // Tạo bài tập lập trình mới
    public function createCoding(Request $request, $course_id)
    {

        $validator = Validator::make($request->all(), [
            'lesson_id'          => 'required|exists:lessons,id',
            'language'           => 'required|string',
            'problem_title'      => 'required|string',
            'problem_description' => 'required|string',
            'starter_code'       => 'nullable|string',
            'solution_code'      => 'nullable|string',
            'test_cases'         => 'nullable|json',
        ]);

        // Nếu lỗi, trả về thông báo
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Dữ liệu không hợp lệ',
                'errors'  => $validator->errors()
            ], 422);
        }

        // Lưu vào database
        $coding = Coding::create($validator->validated());

        return response()->json([
            'message' => 'Bài tập lập trình đã được tạo thành công!',
            'data'    => $coding
        ], 201);
    }

    // Cập nhật bài tập lập trình
    public function updateCoding(Request $request, $course_id, $section_id, $lesson_id, $coding_id)
    {
        // dd($request->all());
        // Tìm bài tập lập trình thuộc đúng lesson_id
        $coding = Coding::where('lesson_id', $lesson_id)
            ->where('id', $coding_id)
            ->first();

        // Nếu không tìm thấy, trả về lỗi
        if (!$coding) {
            return response()->json([
                'message' => 'Bài tập lập trình không tồn tại hoặc không thuộc bài học này!'
            ], 404);
        }

        // Sử dụng Validator::make() để kiểm tra dữ liệu đầu vào
        $validator = Validator::make($request->all(), [
            'language'           => 'sometimes|string',
            'problem_title'      => 'sometimes|string',
            'problem_description' => 'sometimes|string',
            'starter_code'       => 'nullable|string',
            'solution_code'      => 'nullable|string',
            'test_cases'         => 'nullable|json',
        ]);

        // Nếu dữ liệu không hợp lệ, trả về lỗi 422
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Dữ liệu không hợp lệ',
                'errors'  => $validator->errors()
            ], 422);
        }

        // Cập nhật dữ liệu
        $coding->update($validator->validated());

        return response()->json([
            'message' => 'Bài tập lập trình đã được cập nhật thành công!',
            'data'    => $coding->fresh() // Lấy dữ liệu mới nhất từ DB
        ]);
    }

    // Xóa bài tập lập trình
    public function destroyCoding($course_id, $section_id, $lesson_id, $coding_id)
    {
        $coding = Coding::where('lesson_id', $lesson_id)->where('id', $coding_id)->first();

        // Nếu không tìm thấy, báo lỗi
        if (!$coding) {
            return response()->json([
                'message' => 'Không tìm thấy bài tập lập trình để xóa!'
            ], 404);
        }

        // Xóa dữ liệu
        $coding->delete();

        return response()->json([
            'message' => 'Bài tập lập trình đã được xóa thành công!'
        ]);
    }
}
