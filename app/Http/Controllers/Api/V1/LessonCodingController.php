<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Coding;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Section;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LessonCodingController extends Controller
{
    // Tạo bài tập lập trình mới
    public function createCoding(Request $request, $course_id, $section_id, $lesson_id)
    {
        // Kiểm tra quyền của giảng viên đối với khoá học
        $course = request()->user()->courses()->find($course_id);
        // $course = Course::where('user_id', $user_id)->find($course_id);
        if (!$course) {
            return response()->json([
                'message' => 'Không tìm thấy khoá học'
            ], 404);
        }

        //  Kiểm tra section có tồn tại trong course không
        $section = Section::where('course_id', $course_id)->first($section_id);
        if (!$section) {
            return response()->json([
                'message' => 'Section không tồn tại trong khoá học này!'
            ], 422);
        }

        //  Kiểm tra lesson có tồn tại trong section không
        $lesson = Lesson::where('section_id', $section_id)->first($lesson_id);
        if (!$lesson) {
            return response()->json([
                'message' => 'Lesson không tồn tại trong section này!'
            ], 422);
        }

        // Validate dữ liệu đầu vào
        $validator = Validator::make($request->all(), [
            'language'            => 'required|string|max:255',
            'problem_title'       => 'required|string|max:255',
            'problem_description' => 'nullable|string',
            'starter_code'        => 'required|string',
            'solution_code'       => 'required|string',
            'test_cases'          => 'required|json',
        ]);

        // Nếu dữ liệu không hợp lệ, trả về lỗi 422
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Dữ liệu không hợp lệ',
                'errors'  => $validator->errors()
            ], 422);
        }

        // Lấy dữ liệu đã được xác thực
        $validatedData = $validator->validated();
        $validatedData['lesson_id'] = $lesson_id;

        // Nếu test_cases là mảng, chuyển sang JSON
        if (!empty($validatedData['test_cases']) && is_array($validatedData['test_cases'])) {
            $validatedData['test_cases'] = json_encode($validatedData['test_cases']);
        }

        // Lưu vào database với try-catch để xử lý lỗi
        try {
            $coding = Coding::create($validatedData);

            return response()->json([
                'message' => 'Bài tập lập trình đã được tạo thành công!',
                'data'    => $coding
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Lỗi khi tạo bài tập lập trình!',
                'error'   => $e->getMessage()
            ], 500);
        }
    }




    // Cập nhật bài tập lập trình
    public function updateCoding(Request $request, $course_id, $section_id, $lesson_id, $coding_id)
    {
        // Kiểm tra quyền của giảng viên đối với khoá học
        $course = request()->user()->courses()->find($course_id);
        // $course = Course::where('user_id', $user_id)->find($course_id);
        if (!$course) {
            return response()->json([
                'message' => 'Không tìm thấy khoá học'
            ], 404);
        }

        // Kiểm tra section có tồn tại trong course không
        $section = Section::where('course_id', $course_id)->first($section_id);
        if (!$section) {
            return response()->json([
                'message' => 'Section không tồn tại trong khoá học này!'
            ], 422);
        }

        // Kiểm tra lesson có tồn tại trong section không
        $lesson = Lesson::where('section_id', $section_id)->first($lesson_id);
        if (!$lesson) {
            return response()->json([
                'message' => 'Lesson không tồn tại trong section này!'
            ], 422);
        }

        // Kiểm tra bài tập lập trình có tồn tại không
        $coding = Coding::where('lesson_id', $lesson_id)->first($coding_id);
        if (!$coding) {
            return response()->json([
                'message' => 'Bài tập lập trình không tồn tại hoặc không thuộc bài học này!'
            ], 404);
        }

        // Validate dữ liệu đầu vào
        $validator = Validator::make($request->all(), [
            'language'             => 'required|string|max:255',
            'problem_title'        => 'required|string|max:255',
            'problem_description'  => 'nullable|string',
            'starter_code'         => 'required|string',
            'solution_code'        => 'required|string',
            'test_cases'           => 'required|json',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Dữ liệu không hợp lệ',
                'errors'  => $validator->errors()
            ], 422);
        }

        // Lấy dữ liệu hợp lệ
        $validatedData = $validator->validated();

        // Nếu test_cases là mảng, chuyển sang JSON
        if (!empty($validatedData['test_cases']) && is_array($validatedData['test_cases'])) {
            $validatedData['test_cases'] = json_encode($validatedData['test_cases']);
        }

        // Cập nhật dữ liệu trong database
        try {
            $coding->update($validatedData);
            return response()->json([
                'message' => 'Bài tập lập trình đã được cập nhật thành công!',
                'data'    => $coding->fresh()
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Lỗi khi cập nhật bài tập lập trình!',
                'error'   => $e->getMessage()
            ], 500);
        }
    }


    // Xóa bài tập lập trình
    public function destroyCoding($course_id, $section_id, $lesson_id, $coding_id)
    {
        // Kiểm tra quyền của giảng viên đối với khoá học
        $course = request()->user()->courses()->find($course_id);
        // $course = Course::where('user_id', $user_id)->find($course_id);
        if (!$course) {
            return response()->json([
                'message' => 'Không tìm thấy khoá học'
            ], 404);
        }

        // Kiểm tra section có tồn tại trong course không
        $section = Section::where('course_id', $course_id)->first($section_id);
        if (!$section) {
            return response()->json([
                'message' => 'Section không tồn tại trong khoá học này!'
            ], 422);
        }

        // Kiểm tra lesson có tồn tại trong section không
        $lesson = Lesson::where('section_id', $section_id)->first($lesson_id);
        if (!$lesson) {
            return response()->json([
                'message' => 'Lesson không tồn tại trong section này!'
            ], 422);
        }

        // Kiểm tra bài tập lập trình có tồn tại không
        $coding = Coding::where('lesson_id', $lesson_id)->first($coding_id);
        if (!$coding) {
            return response()->json([
                'message' => 'Bài tập lập trình không tồn tại hoặc không thuộc bài học này!'
            ], 404);
        }

        // Xóa bài tập lập trình
        try {
            $coding->delete();
            return response()->json([
                'message' => 'Bài tập lập trình đã được xoá thành công!'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Lỗi khi xoá bài tập lập trình!',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
}
