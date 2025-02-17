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
    /**
     * @OA\Get(
     *     path="/api/courses/{course_id}/sections",
     *     tags={"Section"},
     *     summary="Lấy danh sách các section của một khóa học",
     *     description="API trả về danh sách các section của một khóa học.",
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
     *     @OA\Response(
     *         response=200,
     *         description="Lấy danh sách section thành công",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Lấy dữ liệu thành công"),
     *             @OA\Property(property="sections", type="object",
     *                 @OA\Property(property="current_page", type="integer", example=1),
     *                 @OA\Property(property="data", type="array",
     *                     @OA\Items(
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="course_id", type="integer", example=1),
     *                         @OA\Property(property="title", type="string", example="Cài đặt môi trường Laravel"),
     *                         @OA\Property(property="description", type="string", example=null, nullable=true), 
     *                         @OA\Property(property="order", type="integer", example=1),
     *                         @OA\Property(property="created_at", type="string", format="date-time", example="2025-02-16T14:14:49.000000Z"),
     *                         @OA\Property(property="updated_at", type="string", format="date-time", example="2025-02-16T14:14:49.000000Z")
     *                     )
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
     *         description="Không tìm thấy khóa học",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Không tìm thấy khoá học")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Lỗi hệ thống",
     *          @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Lỗi hệ thống")
     *         )
     *     )
     * )
     */
    public function index(Request $request, $course_id)
    {
        try {
            $course = $request->user()->courses()->find($course_id);

            if (!$course) {
                return response()->json([
                    'message' => 'Không tìm thấy khoá học',
                ], Response::HTTP_NOT_FOUND);
            }

            $sections = $course->sections()->paginate(5);

            return response()->json([
                'message' => 'Lấy dữ liệu thành công',
                'sections' => $sections
            ], Response::HTTP_OK);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Lỗi hệ thống',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/courses/{course_id}/sections",
     *     tags={"Section"},
     *     summary="Tạo mới một section cho khóa học",
     *     description="API cho phép giảng viên tạo mới một section cho một khóa học cụ thể.",
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
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="title", type="string", example="Giới thiệu về Laravel"),
     *             @OA\Property(property="description", type="string", example=null, nullable=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Tạo mới section thành công",
     *         @OA\JsonContent(
     *             @OA\Property(property="section", type="object",
     *                 @OA\Property(property="id", type="integer", example=3),
     *                 @OA\Property(property="course_id", type="integer", example=1),
     *                 @OA\Property(property="title", type="string", example="Giới thiệu về Laravel"),
     *                 @OA\Property(property="description", type="string", example=null, nullable=true),
     *                 @OA\Property(property="order", type="integer", example=3),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2025-02-17T09:00:00.000000Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2025-02-17T09:00:00.000000Z")
     *             ),
     *             @OA\Property(property="message", type="string", example="Tạo mới section thành công")
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
     *         description="Không tìm thấy khóa học",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Không tìm thấy khoá học")
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
    public function store(Request $request, $course_id)
    {
        try {
            $course = $request->user()->courses()->find($course_id);
            if (!$course) {
                return response()->json([
                    'message' => 'Không tìm thấy khoá học'
                ], Response::HTTP_NOT_FOUND);
            }

            $validator = Validator::make($request->all(), [
                'title' => 'required',
                'description' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], Response::HTTP_UNPROCESSABLE_ENTITY);
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

    /**
     * @OA\Get(
     *     path="/api/courses/{course_id}/sections/{section_id}",
     *     tags={"Section"},
     *     summary="Lấy thông tin chi tiết của một section",
     *     description="API cho phép giảng viên lấy thông tin chi tiết của một section cụ thể thuộc một khóa học.",
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
     *         description="Lấy thông tin section thành công",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Lấy dữ liệu thành công"),
     *             @OA\Property(property="section", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="course_id", type="integer", example=1),
     *                 @OA\Property(property="title", type="string", example="Giới thiệu về Laravel"),
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
    public function show(Request $request, $course_id, $section_id)
    {
        try {
            $course = $request->user()->courses()->with([
                'sections' => function ($query) use ($section_id) {
                    $query->where('id', $section_id);
                }
            ])->find($course_id);

            if (!$course || !$section = $course->sections->first()) {
                return response()->json(['message' => 'Không tìm thấy tài nguyên'], Response::HTTP_NOT_FOUND);
            }

            return response()->json([
                'message' => 'Lấy dữ liệu thành công',
                'section' => $section
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
     *     path="/api/courses/{course_id}/sections/{section_id}",
     *     tags={"Section"},
     *     summary="Cập nhật thông tin của một section",
     *     description="API cho phép giảng viên cập nhật thông tin của một section cụ thể thuộc một khóa học.",
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
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="title", type="string", example="Giới thiệu về Laravel nâng cao"),
     *             @OA\Property(property="description", type="string", example="Nội dung cập nhật", nullable=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Cập nhật section thành công",
     *         @OA\JsonContent(
     *             @OA\Property(property="section", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="course_id", type="integer", example=1),
     *                 @OA\Property(property="title", type="string", example="Giới thiệu về Laravel nâng cao"),
     *                 @OA\Property(property="description", type="string", example="Nội dung cập nhật", nullable=true),
     *                 @OA\Property(property="order", type="integer", example=1),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2025-02-17T09:00:00.000000Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2025-02-18T10:00:00.000000Z")
     *             ),
     *             @OA\Property(property="message", type="string", example="Cập nhật section thành công")
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
    public function update(Request $request, $course_id, $section_id)
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
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], Response::HTTP_UNPROCESSABLE_ENTITY);
            }
            $data = $request->all();

            $data['order'] = $section->order;

            $section->update($data);

            return response()->json([
                'section' => $section,
                'message' => 'Cập nhật section thành công'
            ], Response::HTTP_OK);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Lỗi server',
                'error' => $th
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    /**
     * @OA\Delete(
     *     path="/api/courses/{course_id}/sections/{section_id}",
     *     tags={"Section"},
     *     summary="Xóa một section",
     *     description="API cho phép giảng viên xóa một section cụ thể thuộc một khóa học.",
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
     *         response=204,
     *         description="Xóa section thành công"
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
                return response()->json(['message' => 'Không tìm thấy tài nguyên'], Response::HTTP_NOT_FOUND); // Combined check
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
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
