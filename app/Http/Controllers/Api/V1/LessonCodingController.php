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

    // Lấy danh sách bài tập lập trình
    public function index(Request $request, $course_id, $section_id, $lesson_id)
    {
        $course = $request->user()->courses()->with([
            'sections' => function ($query) use ($section_id) {
                $query->where('id', $section_id);
            },
            'sections.lessons' => function ($query) use ($lesson_id) {
                $query->where('id', $lesson_id);
            },
            'sections.lessons.codings'
        ])->find($course_id);

        if (!$course || !$course->sections->first() || !$lesson = $course->sections->first()->lessons->first()) {
            return response()->json(['message' => 'Không tìm thấy tài nguyên'], 404);
        }

        return response()->json($lesson->codings);
    }

    // Tạo bài tập lập trình mới
    public function store(Request $request, $course_id, $section_id, $lesson_id)
    {
        $course = $request->user()->courses()->with([
            'sections' => function ($query) use ($section_id) {
                $query->where('id', $section_id);
            },
            'sections.lessons' => function ($query) use ($lesson_id) {
                $query->where('id', $lesson_id);
            }
        ])->find($course_id);

        if (!$course || !$course->sections->first() || !$lesson = $course->sections->first()->lessons->first()) {
            return response()->json(['message' => 'Không tìm thấy tài nguyên'], 404); // Combined check
        }

        // Validate dữ liệu đầu vào
        $validator = Validator::make($request->all(), [
            'language' => 'required|string|max:255',
            'problem_title' => 'required|string|max:255',
            'problem_description' => 'nullable|string',
            'starter_code' => 'required|string',
            'solution_code' => 'required|string',
            'test_cases' => 'required|json',
        ]);

        // Nếu dữ liệu không hợp lệ, trả về lỗi 422
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $validator->errors()
            ], 422);
        }

        // Lấy dữ liệu đã được xác thực
        $data = $validator->validated();

        // Nếu test_cases là mảng, chuyển sang JSON
        if (!empty($data['test_cases']) && is_array($data['test_cases'])) {
            $data['test_cases'] = json_encode($data['test_cases']);
        }

        // Lưu vào database với try-catch để xử lý lỗi
        try {
            $coding = $lesson->codings()->create($data);

            return response()->json([
                'message' => 'Bài tập lập trình đã được tạo thành công!',
                'data' => $coding
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Lỗi khi tạo bài tập lập trình!',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Cập nhật bài tập lập trình
    public function update(Request $request, $course_id, $section_id, $lesson_id, $coding_id)
    {
        $course = $request->user()->courses()->with([
            'sections' => function ($query) use ($section_id) {
                $query->where('id', $section_id);
            },
            'sections.lessons' => function ($query) use ($lesson_id) {
                $query->where('id', $lesson_id);
            },
            'sections.lessons.codings' => function ($query) use ($coding_id) {
                $query->where('id', $coding_id);
            }
        ])->find($course_id);

        if (
            !$course ||
            !$course->sections->first() ||
            !$course->sections->first()->lessons->first() ||
            !$coding = $course->sections->first()->lessons->first()->codings->first()
        ) {
            return response()->json(['message' => 'Không tìm thấy tài nguyên'], 404); // Combined check
        }

        // Validate dữ liệu đầu vào
        $validator = Validator::make($request->all(), [
            'language' => 'required|string|max:255',
            'problem_title' => 'required|string|max:255',
            'problem_description' => 'nullable|string',
            'starter_code' => 'required|string',
            'solution_code' => 'required|string',
            'test_cases' => 'required|json',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $validator->errors()
            ], 422);
        }

        // Lấy dữ liệu hợp lệ
        $data = $validator->validated();

        // Nếu test_cases là mảng, chuyển sang JSON
        if (!empty($data['test_cases']) && is_array($data['test_cases'])) {
            $data['test_cases'] = json_encode($data['test_cases']);
        }

        // Cập nhật dữ liệu trong database
        try {
            $coding->update($data);
            return response()->json([
                'message' => 'Bài tập lập trình đã được cập nhật thành công!',
                'data' => $coding->fresh()
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Lỗi khi cập nhật bài tập lập trình!',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    // Xóa bài tập lập trình
    public function destroy($course_id, $section_id, $lesson_id, $coding_id)
    {
        $course = request()->user()->courses()->with([
            'sections' => function ($query) use ($section_id) {
                $query->where('id', $section_id);
            },
            'sections.lessons' => function ($query) use ($lesson_id) {
                $query->where('id', $lesson_id);
            },
            'sections.lessons.codings' => function ($query) use ($coding_id) {
                $query->where('id', $coding_id);
            }
        ])->find($course_id);

        if (
            !$course ||
            !$course->sections->first() ||
            !$course->sections->first()->lessons->first() ||
            !$coding = $course->sections->first()->lessons->first()->codings->first()
        ) {
            return response()->json(['message' => 'Không tìm thấy tài nguyên'], 404); // Combined check
        }

        // Xóa bài tập lập trình
        try {
            $coding->delete();
            return response()->noContent();
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Lỗi khi xoá bài tập lập trình!',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Lấy chi tiết một bài tập lập trình cụ thể
    public function show(Request $request, $course_id, $section_id, $lesson_id, $coding_id)
    {
        $course = $request->user()->courses()->with([
            'sections' => function ($query) use ($section_id) {
                $query->where('id', $section_id);
            },
            'sections.lessons' => function ($query) use ($lesson_id) {
                $query->where('id', $lesson_id);
            },
            'sections.lessons.codings' => function ($query) use ($coding_id) {
                $query->where('id', $coding_id);
            }
        ])->find($course_id);

        $lesson = optional(optional($course)->sections->first())->lessons->first();
        $coding = optional($lesson)->codings->first();

        if (!$lesson || !$coding) {
            return response()->json(['message' => 'Không tìm thấy tài nguyên'], 404);
        }

        return response()->json($coding);
    }
}
