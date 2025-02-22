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
    /**
     * @OA\Get(
     *     path="/api/courses/{course_id}/sections/{section_id}/lessons",
     *     tags={"Lesson"},
     *     summary="Lấy danh sách các lesson của một section",
     *     description="API cho phép giảng viên lấy danh sách các lesson của một section cụ thể thuộc một khóa học.",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="course_id",
     *         in="path",
     *         description="ID của khóa học",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             example=1
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="section_id",
     *         in="path",
     *         description="ID của section",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             example=1
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lấy danh sách lesson thành công",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Lấy dữ liệu thành công"),
     *             @OA\Property(property="lessons", type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="section_id", type="integer", example=1),
     *                     @OA\Property(property="title", type="string", example="Bài 1: Giới thiệu Laravel"),
     *                     @OA\Property(property="description", type="string", example=null, nullable=true),
     *                     @OA\Property(property="order", type="integer", example=1),
     *                     @OA\Property(property="created_at", type="string", format="date-time", example="2025-02-17T09:00:00.000000Z"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-02-17T09:00:00.000000Z")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized - Chưa đăng nhập hoặc token không hợp lệ",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
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
     *         response=500,
     *         description="Lỗi server",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Lỗi server"),
     *             @OA\Property(property="error", type="string", example="Exception message")
     *         )
     *     )
     * )
     */
    public function index(Request $request, $course_id, $section_id)
    {
        try {
            $course = $request->user()->courses()->with([
                'sections' => function ($query) use ($section_id) {
                    $query->where('id', $section_id)->with('lessons');
                }
            ])->find($course_id);

            if (!$course || !$section = $course->sections->first()) {
                return response()->json(['message' => 'Không tìm thấy tài nguyên'], Response::HTTP_NOT_FOUND);
            }

            return response()->json([
                'message' => 'Lấy dữ liệu thành công',
                'lessons' => $section->lessons
            ], Response::HTTP_OK);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Lỗi server',
                'error' => $th->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\Post(
     * path="/api/courses/{course_id}/sections/{section_id}/lessons",
     * tags={"Lesson"},
     * summary="Tạo mới một lesson cho section",
     * description="API cho phép giảng viên tạo mới một lesson cho một section cụ thể thuộc một khóa học. API này cũng sẽ cập nhật tổng số lessons trong section.",
     * security={{"sanctum":{}}},
     * @OA\Parameter(
     * name="course_id",
     * in="path",
     * description="ID của khóa học",
     * required=true,
     * @OA\Schema(
     * type="integer",
     * example=1
     * )
     * ),
     * @OA\Parameter(
     * name="section_id",
     * in="path",
     * description="ID của section",
     * required=true,
     * @OA\Schema(
     * type="integer",
     * example=1
     * )
     * ),
     * @OA\RequestBody(
     * required=true,
     * @OA\JsonContent(
     * @OA\Property(property="title", type="string", example="Bài 1: Giới thiệu Laravel"),
     * @OA\Property(property="description", type="string", example=null, nullable=true),
     * @OA\Property(property="type", type="string", example="video", nullable=true)
     * )
     * ),
     * @OA\Response(
     * response=201,
     * description="Tạo mới lesson thành công",
     * @OA\JsonContent(
     * @OA\Property(property="lesson", type="object",
     * @OA\Property(property="id", type="integer", example=1),
     * @OA\Property(property="section_id", type="integer", example=1),
     * @OA\Property(property="title", type="string", example="Bài 1: Giới thiệu Laravel"),
     * @OA\Property(property="description", type="string", example=null, nullable=true),
     * @OA\Property(property="order", type="integer", example=1),
     * @OA\Property(property="type", type="string", example="video"),
     * @OA\Property(property="created_at", type="string", format="date-time", example="2025-02-17T09:00:00.000000Z"),
     * @OA\Property(property="updated_at", type="string", format="date-time", example="2025-02-17T09:00:00.000000Z")
     * ),
     * @OA\Property(property="message", type="string", example="Tạo mới lesson thành công")
     * )
     * ),
     * @OA\Response(
     * response=400,
     * description="Lỗi validation",
     * @OA\JsonContent(
     * @OA\Property(property="errors", type="object", example={"title": {"The title field is required."}})
     * )
     * ),
     * @OA\Response(
     * response=401,
     * description="Unauthorized - Chưa đăng nhập hoặc token không hợp lệ",
     * @OA\JsonContent(
     * @OA\Property(property="message", type="string", example="Unauthenticated.")
     * )
     * ),
     * @OA\Response(
     * response=404,
     * description="Không tìm thấy tài nguyên",
     * @OA\JsonContent(
     * @OA\Property(property="message", type="string", example="Không tìm thấy tài nguyên")
     * )
     * ),
     * @OA\Response(
     * response=500,
     * description="Lỗi server",
     * @OA\JsonContent(
     * @OA\Property(property="message", type="string", example="Lỗi server"),
     * @OA\Property(property="error", type="string", example="Exception message")
     * )
     * )
     * )
     */
    public function store(Request $request, $course_id, $section_id)
    {
        try {
            $course = $request->user()->courses()->with([
                'sections' => function ($query) use ($section_id) {
                    $query->where('id', $section_id);
                }
            ])->find($course_id);

            if (!$course || !$section = $course->sections->first()) {
                return response()->json(['message' => 'Không tìm thấy tài nguyên'], Response::HTTP_NOT_FOUND); // Combined check
            }

            $validator = Validator::make($request->all(), [
                'title' => 'required',
                'description' => 'nullable|string',
                'type' => 'nullable'
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $data = $request->all();

            // Lấy order lớn nhất hiện tại trong section và tăng thêm 1
            $maxOrder = $section->lessons()->max('order') ?? 0;
            $data['order'] = $maxOrder + 1;

            $totalLessons = $section->lessons()->count();
            $section->update(['total_lessons' => $totalLessons]);
            $section->increment('total_lessons');

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

    /**
     * @OA\Get(
     *     path="/api/courses/{course_id}/sections/{section_id}/lessons/{lesson_id}",
     *     tags={"Lesson"},
     *     summary="Lấy thông tin chi tiết của một lesson",
     *     description="API cho phép giảng viên lấy thông tin chi tiết của một lesson cụ thể thuộc một section của một khóa học.",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="course_id",
     *         in="path",
     *         description="ID của khóa học",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             example=1
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="section_id",
     *         in="path",
     *         description="ID của section",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             example=1
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="lesson_id",
     *         in="path",
     *         description="ID của lesson",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             example=1
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lấy thông tin lesson thành công",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Lấy dữ liệu thành công"),
     *             @OA\Property(property="lesson", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="section_id", type="integer", example=1),
     *                 @OA\Property(property="title", type="string", example="Bài 1: Giới thiệu Laravel"),
     *                 @OA\Property(property="description", type="string", example=null, nullable=true),
     *                 @OA\Property(property="order", type="integer", example=1),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2025-02-17T09:00:00.000000Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2025-02-17T09:00:00.000000Z")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized - Chưa đăng nhập hoặc token không hợp lệ",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
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
     *         response=500,
     *         description="Lỗi server",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Lỗi server"),
     *             @OA\Property(property="error", type="string", example="Exception message")
     *         )
     *     )
     * )
     */
    public function show(Request $request, $course_id, $section_id, $lesson_id)
    {
        try {
            $course = $request->user()->courses()->with([
                'sections' => function ($query) use ($section_id) {
                    $query->where('id', $section_id);
                },
                'sections.lessons' => function ($query) use ($lesson_id) {
                    $query->where('id', $lesson_id);
                },
            ])->find($course_id);

            if (!$course || !$course->sections->first() || !$lesson = $course->sections->first()->lessons->find($lesson_id)) {
                return response()->json(['message' => 'Không tìm thấy tài nguyên'], Response::HTTP_NOT_FOUND); // Combined check
            }
            return response()->json([
                'message' => 'Lấy dữ liệu thành công',
                'lesson' => $lesson->with(['videos', 'codings', 'documents', 'quizzes'])->find($lesson_id)
            ], Response::HTTP_OK);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Lỗi server',
                'error' => $th->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/courses/{course_id}/sections/{section_id}/lessons/{lesson_id}",
     *     tags={"Lesson"},
     *     summary="Cập nhật thông tin của một lesson",
     *     description="API cho phép giảng viên cập nhật thông tin của một lesson cụ thể thuộc một section của một khóa học.",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="course_id",
     *         in="path",
     *         description="ID của khóa học",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             example=1
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="section_id",
     *         in="path",
     *         description="ID của section",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             example=1
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="lesson_id",
     *         in="path",
     *         description="ID của lesson",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             example=1
     *         )
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="title", type="string", example="Bài 1: Giới thiệu Laravel nâng cao"),
     *             @OA\Property(property="description", type="string", example="Nội dung cập nhật", nullable=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Cập nhật lesson thành công",
     *         @OA\JsonContent(
     *             @OA\Property(property="lesson", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="section_id", type="integer", example=1),
     *                 @OA\Property(property="title", type="string", example="Bài 1: Giới thiệu Laravel nâng cao"),
     *                 @OA\Property(property="description", type="string", example="Nội dung cập nhật", nullable=true),
     *                 @OA\Property(property="order", type="integer", example=1),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2025-02-17T09:00:00.000000Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2025-02-18T10:00:00.000000Z")
     *             ),
     *             @OA\Property(property="message", type="string", example="Cập nhật lesson thành công")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Lỗi validation",
     *         @OA\JsonContent(
     *             @OA\Property(property="errors", type="object", example={"title": {"The title field is required."}})
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized - Chưa đăng nhập hoặc token không hợp lệ",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
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
     *         response=500,
     *         description="Lỗi server",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Lỗi server"),
     *             @OA\Property(property="error", type="object", example="Exception message")
     *         )
     *     )
     * )
     */
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
                return response()->json(['errors' => $validator->errors()], Response::HTTP_UNPROCESSABLE_ENTITY);
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

    /**
     * @OA\Delete(
     * path="/api/courses/{course_id}/sections/{section_id}/lessons/{lesson_id}",
     * tags={"Lesson"},
     * summary="Xóa một lesson",
     * description="API cho phép giảng viên xóa một lesson cụ thể thuộc một section của một khóa học. API này cũng sẽ cập nhật tổng số lessons (total_lessons) trong section.",
     * security={{"sanctum":{}}},
     * @OA\Parameter(
     * name="course_id",
     * in="path",
     * description="ID của khóa học",
     * required=true,
     * @OA\Schema(
     * type="integer",
     * example=1
     * )
     * ),
     * @OA\Parameter(
     * name="section_id",
     * in="path",
     * description="ID của section",
     * required=true,
     * @OA\Schema(
     * type="integer",
     * example=1
     * )
     * ),
     * @OA\Parameter(
     * name="lesson_id",
     * in="path",
     * description="ID của lesson",
     * required=true,
     * @OA\Schema(
     * type="integer",
     * example=1
     * )
     * ),
     * @OA\Response(
     * response=204,
     * description="Xóa lesson thành công"
     * ),
     * @OA\Response(
     * response=401,
     * description="Unauthorized - Chưa đăng nhập hoặc token không hợp lệ",
     * @OA\JsonContent(
     * @OA\Property(property="message", type="string", example="Unauthenticated.")
     * )
     * ),
     * @OA\Response(
     * response=404,
     * description="Không tìm thấy tài nguyên",
     * @OA\JsonContent(
     * @OA\Property(property="message", type="string", example="Không tìm thấy tài nguyên")
     * )
     * ),
     * @OA\Response(
     * response=500,
     * description="Lỗi server",
     * @OA\JsonContent(
     * @OA\Property(property="message", type="string", example="Lỗi server"),
     * @OA\Property(property="error", type="string", example="Exception message")
     * )
     * )
     * )
     */
    public function destroy(Request $request, $course_id, $section_id, $lesson_id)
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

            // Lưu order của lesson trước khi xóa
            $deletedOrder = $lesson->order;
            DB::beginTransaction();
            // Xóa lesson
            $lesson->delete();

            // Xóa lesson
            $lesson->delete();

            // Lấy section để cập nhật total_lessons
            $section = Section::find($section_id);

            // Đếm số lessons hiện tại trong section
            $currentLessonCount = $section->lessons()->count();

            // Cập nhật total_lessons với số lessons vừa đếm được
            $section->update(['total_lessons' => $currentLessonCount]);

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
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function updateOrder()
    {
        try {
            $orders = request()->all();
            foreach ($orders as $order) {
                $lesson = Lesson::find($order['id']);
                $lesson->update([
                    'order' => $order['order']
                ]);
            }
            return response()->json($lesson);
        } catch (\Throwable $th) {
            return response()->json($th, 500);
        }
    }
}
