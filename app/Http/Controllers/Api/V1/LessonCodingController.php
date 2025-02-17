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

    /**
     * @OA\Get(
     *     path="/courses/{course_id}/sections/{section_id}/lessons/{lesson_id}/codings",
     *     summary="Lấy danh sách codings của bài học",
     *     description="API này trả về danh sách các codings của một bài học cụ thể thuộc một section và khóa học mà user có quyền truy cập.",
     *     tags={"Lessons"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="course_id",
     *         in="path",
     *         required=true,
     *         description="ID của khóa học",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="section_id",
     *         in="path",
     *         required=true,
     *         description="ID của section",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="lesson_id",
     *         in="path",
     *         required=true,
     *         description="ID của bài học",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Danh sách codings của bài học",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="language", type="string", example="hoc php"),
     *                 @OA\Property(property="problem_title", type="string", example="php buoi 1"),
     *                 @OA\Property(property="starter_code", type="string", example="dhdhdh"),
     *                 @OA\Property(property="solution_code", type="string", example="dhdhdh"),
     *                 @OA\Property(
     *                     property="test_cases",
     *                     type="array",
     *                     @OA\Items(
     *                         type="object",
     *                         @OA\Property(property="input", type="array", @OA\Items(type="integer"), example={1,2}),
     *                         @OA\Property(property="output", type="integer", example=3)
     *                     )
     *                 ),
     *                 @OA\Property(property="lesson_id", type="integer", example=1),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2025-02-16T16:44:38.000000Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2025-02-16T16:44:38.000000Z")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Không tìm thấy tài nguyên",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Không tìm thấy tài nguyên")
     *         )
     *     )
     * )
     */

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


    /**
     * @OA\Post(
     *     path="/courses/{course_id}/sections/{section_id}/lessons/{lesson_id}/codings",
     *     summary="Tạo mới coding cho bài học",
     *     description="API này cho phép tạo một coding cho một bài học cụ thể thuộc một section và khóa học mà user có quyền truy cập.",
     *     tags={"Lessons"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="course_id",
     *         in="path",
     *         required=true,
     *         description="ID của khóa học",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="section_id",
     *         in="path",
     *         required=true,
     *         description="ID của section",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="lesson_id",
     *         in="path",
     *         required=true,
     *         description="ID của bài học",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"language", "problem_title", "starter_code", "solution_code", "test_cases"},
     *             @OA\Property(property="language", type="string", example="Python"),
     *             @OA\Property(property="problem_title", type="string", example="Tính tổng hai số"),
     *             @OA\Property(property="problem_description", type="string", nullable=true, example="Viết một function nhận vào hai số và trả về tổng của chúng."),
     *             @OA\Property(property="starter_code", type="string", example="def sum(a, b):\n    return 0"),
     *             @OA\Property(property="solution_code", type="string", example="def sum(a, b):\n    return a + b"),
     *             @OA\Property(
     *                 property="test_cases",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="input", type="array", @OA\Items(type="integer"), example={1, 2}),
     *                     @OA\Property(property="output", type="integer", example=3)
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Bài tập lập trình đã được tạo thành công!",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Bài tập lập trình đã được tạo thành công!"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="language", type="string", example="Python"),
     *                 @OA\Property(property="problem_title", type="string", example="Tính tổng hai số"),
     *                 @OA\Property(property="problem_description", type="string", nullable=true, example="Viết một function nhận vào hai số và trả về tổng của chúng."),
     *                 @OA\Property(property="starter_code", type="string", example="def sum(a, b):\n    return 0"),
     *                 @OA\Property(property="solution_code", type="string", example="def sum(a, b):\n    return a + b"),
     *                 @OA\Property(
     *                     property="test_cases",
     *                     type="array",
     *                     @OA\Items(
     *                         type="object",
     *                         @OA\Property(property="input", type="array", @OA\Items(type="integer"), example={1, 2}),
     *                         @OA\Property(property="output", type="integer", example=3)
     *                     )
     *                 ),
     *                 @OA\Property(property="lesson_id", type="integer", example=1),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2025-02-16T16:44:38.000000Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2025-02-16T16:44:38.000000Z")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Dữ liệu không hợp lệ",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Dữ liệu không hợp lệ"),
     *             @OA\Property(property="errors", type="object", example={"language": {"Trường này là bắt buộc."}})
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Lỗi khi tạo bài tập lập trình!",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Lỗi khi tạo bài tập lập trình!"),
     *             @OA\Property(property="error", type="string", example="Lỗi database...")
     *         )
     *     )
     * )
     */
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


    /**
     * @OA\Put(
     *     path="/courses/{course_id}/sections/{section_id}/lessons/{lesson_id}/codings/{coding_id}",
     *     summary="Cập nhật coding của bài học",
     *     description="API này cho phép cập nhật một coding của bài học thuộc một section và khóa học mà user có quyền truy cập.",
     *     tags={"Lessons"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="course_id",
     *         in="path",
     *         required=true,
     *         description="ID của khóa học",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="section_id",
     *         in="path",
     *         required=true,
     *         description="ID của section",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="lesson_id",
     *         in="path",
     *         required=true,
     *         description="ID của bài học",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="coding_id",
     *         in="path",
     *         required=true,
     *         description="ID của coding cần cập nhật",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"language", "problem_title", "starter_code", "solution_code", "test_cases"},
     *             @OA\Property(property="language", type="string", example="Python"),
     *             @OA\Property(property="problem_title", type="string", example="Tính tổng hai số"),
     *             @OA\Property(property="problem_description", type="string", nullable=true, example="Viết một function nhận vào hai số và trả về tổng của chúng."),
     *             @OA\Property(property="starter_code", type="string", example="def sum(a, b):\n    return 0"),
     *             @OA\Property(property="solution_code", type="string", example="def sum(a, b):\n    return a + b"),
     *             @OA\Property(
     *                 property="test_cases",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="input", type="array", @OA\Items(type="integer"), example={1, 2}),
     *                     @OA\Property(property="output", type="integer", example=3)
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Bài tập lập trình đã được cập nhật thành công!",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Bài tập lập trình đã được cập nhật thành công!"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="language", type="string", example="Python"),
     *                 @OA\Property(property="problem_title", type="string", example="Tính tổng hai số"),
     *                 @OA\Property(property="problem_description", type="string", nullable=true, example="Viết một function nhận vào hai số và trả về tổng của chúng."),
     *                 @OA\Property(property="starter_code", type="string", example="def sum(a, b):\n    return 0"),
     *                 @OA\Property(property="solution_code", type="string", example="def sum(a, b):\n    return a + b"),
     *                 @OA\Property(
     *                     property="test_cases",
     *                     type="array",
     *                     @OA\Items(
     *                         type="object",
     *                         @OA\Property(property="input", type="array", @OA\Items(type="integer"), example={1, 2}),
     *                         @OA\Property(property="output", type="integer", example=3)
     *                     )
     *                 ),
     *                 @OA\Property(property="lesson_id", type="integer", example=1),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2025-02-16T16:44:38.000000Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2025-02-16T16:44:38.000000Z")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Không tìm thấy tài nguyên",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Không tìm thấy tài nguyên")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Dữ liệu không hợp lệ",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Dữ liệu không hợp lệ"),
     *             @OA\Property(property="errors", type="object", example={"language": {"Trường này là bắt buộc."}})
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Lỗi khi cập nhật bài tập lập trình!",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Lỗi khi cập nhật bài tập lập trình!"),
     *             @OA\Property(property="error", type="string", example="Lỗi database...")
     *         )
     *     )
     * )
     */

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



    /**
     * @OA\Delete(
     *     path="/courses/{course_id}/sections/{section_id}/lessons/{lesson_id}/codings/{coding_id}",
     *     summary="Xóa coding của bài học",
     *     description="API này cho phép xóa một coding của bài học thuộc một section và khóa học mà user có quyền truy cập.",
     *     tags={"Lessons"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="course_id",
     *         in="path",
     *         required=true,
     *         description="ID của khóa học",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="section_id",
     *         in="path",
     *         required=true,
     *         description="ID của section",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="lesson_id",
     *         in="path",
     *         required=true,
     *         description="ID của bài học",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="coding_id",
     *         in="path",
     *         required=true,
     *         description="ID của coding cần xóa",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Bài tập lập trình đã được xóa thành công!"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Không tìm thấy tài nguyên",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Không tìm thấy tài nguyên")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Lỗi khi xóa bài tập lập trình!",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Lỗi khi xóa bài tập lập trình!"),
     *             @OA\Property(property="error", type="string", example="Lỗi database...")
     *         )
     *     )
     * )
     */


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



    /**
     * @OA\Get(
     *     path="/courses/{course_id}/sections/{section_id}/lessons/{lesson_id}/codings/{coding_id}",
     *     summary="Lấy chi tiết một bài tập lập trình",
     *     description="API này trả về thông tin chi tiết của một coding thuộc một bài học cụ thể.",
     *     tags={"Lessons"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="course_id",
     *         in="path",
     *         required=true,
     *         description="ID của khóa học",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="section_id",
     *         in="path",
     *         required=true,
     *         description="ID của section",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="lesson_id",
     *         in="path",
     *         required=true,
     *         description="ID của bài học",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="coding_id",
     *         in="path",
     *         required=true,
     *         description="ID của bài tập lập trình",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Chi tiết bài tập lập trình",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="language", type="string", example="Python"),
     *             @OA\Property(property="problem_title", type="string", example="Tính tổng hai số"),
     *             @OA\Property(property="problem_description", type="string", nullable=true, example="Viết một function nhận vào hai số và trả về tổng của chúng."),
     *             @OA\Property(property="starter_code", type="string", example="def sum(a, b):\n    return 0"),
     *             @OA\Property(property="solution_code", type="string", example="def sum(a, b):\n    return a + b"),
     *             @OA\Property(
     *                 property="test_cases",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="input", type="array", @OA\Items(type="integer"), example={1, 2}),
     *                     @OA\Property(property="output", type="integer", example=3)
     *                 )
     *             ),
     *             @OA\Property(property="lesson_id", type="integer", example=1),
     *             @OA\Property(property="created_at", type="string", format="date-time", example="2025-02-16T16:44:38.000000Z"),
     *             @OA\Property(property="updated_at", type="string", format="date-time", example="2025-02-16T16:44:38.000000Z")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Không tìm thấy tài nguyên",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Không tìm thấy tài nguyên")
     *         )
     *     )
     * )
     */

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
