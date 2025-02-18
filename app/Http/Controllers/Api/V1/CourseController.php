<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreCourseRequest;
use App\Http\Requests\Api\UpdateCourseRequest;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\Review;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class CourseController extends Controller
{
    /**
     * @OA\Get(
     *      path="api/lecturer/courses",
     *      tags={"Lecturer - Courses"},
     *      summary="Lấy danh sách khóa học của giảng viên",
     *      description="API trả về danh sách các khóa học do giảng viên tạo, có phân trang (8 khóa học mỗi trang).",
     *      security={{"sanctum":{}}},
     *      @OA\Response(
     *          response=200,
     *          description="Lấy danh sách khóa học thành công",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Lấy dữ liệu thành công"),
     *              @OA\Property(property="courses", type="object",
     *                  @OA\Property(property="current_page", type="integer", example=1),
     *                  @OA\Property(property="data", type="array",
     *                      @OA\Items(
     *                          @OA\Property(property="id", type="integer", example=1),
     *                          @OA\Property(property="title", type="string", example="Lập trình Laravel"),
     *                          @OA\Property(property="category", type="object",
     *                              @OA\Property(property="id", type="integer", example=2),
     *                              @OA\Property(property="name", type="string", example="Web Development")
     *                          ),
     *                          @OA\Property(property="created_at", type="string", format="date-time", example="2025-02-16T14:55:30Z")
     *                      )
     *                  ),
     *                  @OA\Property(property="total", type="integer", example=20),
     *                  @OA\Property(property="last_page", type="integer", example=3)
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthorized - Chưa đăng nhập hoặc token không hợp lệ",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Unauthenticated.")
     *          )
     *      ),
     *      @OA\Response(
     *          response=500,
     *          description="Lỗi hệ thống"
     *      )
     * )
     */
    public function index()
    {
        try {
            $courses = request()->user()->courses()->with('category')->paginate(8);

            if (!$courses) {
                return response()->json([
                    'message' => 'Không tìm thấy khoá học',
                ], Response::HTTP_NOT_FOUND);
            }

            return response()->json([
                'message' => 'Lấy dữ liệu thành công',
                'courses' => $courses
            ], Response::HTTP_OK);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Lỗi hệ thống',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\Post(
     *     path="api/lecturer/courses",
     *     summary="Tạo khoá học mới",
     *     tags={"Lecturer - Courses"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"title", "category_id"},
     *             @OA\Property(property="title", type="string", example="Lập trình Laravel"),
     *             @OA\Property(property="category_id", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Tạo khoá học thành công",
     *         @OA\JsonContent(
     *             @OA\Property(property="course_id", type="integer", example=10)
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Lỗi validation",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Dữ liệu không hợp lệ!"),
     *             @OA\Property(property="errors", type="object",
     *                 @OA\Property(property="title", type="array",
     *                     @OA\Items(type="string", example="Tiêu đề không được để trống.")
     *                 ),
     *                 @OA\Property(property="category_id", type="array",
     *                     @OA\Items(type="string", example="Danh mục không hợp lệ.")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Lỗi hệ thống",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Lỗi hệ thống"),
     *             @OA\Property(property="error", type="string")
     *         )
     *     )
     * )
     */

    public function store(StoreCourseRequest $request)
    {
        try {
            $course = $request->user()->courses()->create([
                'title' => $request->title,
                'category_id' => $request->category_id ?? null,
                'status' => 'draft',
                'admin_commission_rate' => 30,
            ]);

            return response()->json([
                'course_id' => $course->id,
            ], Response::HTTP_CREATED);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th,
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\Get(
     *     path="api/lecturer/courses/{course_id}",
     *     summary="Lấy thông tin chi tiết khoá học",
     *     tags={"Lecturer - Courses"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="course_id",
     *         in="path",
     *         required=true,
     *         description="ID của khoá học",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Thông tin chi tiết khoá học",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer"),
     *                 @OA\Property(property="user_id", type="integer"),
     *                 @OA\Property(property="category_id", type="integer", nullable=true),
     *                 @OA\Property(property="price_regular", type="integer", nullable=true),
     *                 @OA\Property(property="price_sale", type="integer", nullable=true),
     *                 @OA\Property(property="title", type="string"),
     *                 @OA\Property(property="thumbnail", type="string", nullable=true),
     *                 @OA\Property(property="video_preview", type="string", nullable=true),
     *                 @OA\Property(property="description", type="string", nullable=true),
     *                 @OA\Property(property="primary_content", type="string", nullable=true),
     *                 @OA\Property(property="status", type="string", enum={"draft", "pending", "published"}),
     *                 @OA\Property(property="is_show_home", type="boolean", nullable=true),
     *                 @OA\Property(property="target_students", type="string", nullable=true),
     *                 @OA\Property(property="learning_outcomes", type="array", @OA\Items(type="string"), nullable=true),
     *                 @OA\Property(property="prerequisites", type="string", nullable=true),
     *                 @OA\Property(property="who_is_this_for", type="string", nullable=true),
     *                 @OA\Property(property="language", type="string", nullable=true),
     *                 @OA\Property(property="level", type="string", nullable=true),
     *                 @OA\Property(property="admin_commission_rate", type="number", format="float", nullable=true),
     *                 @OA\Property(property="submited_at", type="string", format="date-time", nullable=true),
     *                 @OA\Property(property="censored_at", type="string", format="date-time", nullable=true),
     *                 @OA\Property(property="admin_comment", type="string", nullable=true),
     *                 @OA\Property(property="category", type="object", nullable=true,
     *                     @OA\Property(property="id", type="integer"),
     *                     @OA\Property(property="name", type="string"),
     *                     @OA\Property(property="slug", type="string", nullable=true),
     *                     @OA\Property(property="image", type="string", nullable=true),
     *                     @OA\Property(property="parent_id", type="integer", nullable=true)
     *                 ),
     *                 @OA\Property(property="sections", type="array", @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer"),
     *                     @OA\Property(property="title", type="string"),
     *                     @OA\Property(property="description", type="string", nullable=true),
     *                     @OA\Property(property="order", type="integer"),
     *                     @OA\Property(property="lessons", type="array", @OA\Items(
     *                         type="object",
     *                         @OA\Property(property="id", type="integer"),
     *                         @OA\Property(property="title", type="string"),
     *                         @OA\Property(property="description", type="string", nullable=true),
     *                         @OA\Property(property="order", type="integer"),
     *                         @OA\Property(property="documents", type="array", @OA\Items(
     *                             type="object",
     *                             @OA\Property(property="id", type="integer"),
     *                             @OA\Property(property="document_url", type="string"),
     *                             @OA\Property(property="file_type", type="string", enum={"pdf","doc","docx"})
     *                         )),
     *                         @OA\Property(property="videos", type="array", @OA\Items(
     *                             type="object",
     *                             @OA\Property(property="id", type="integer"),
     *                             @OA\Property(property="video_url", type="string"),
     *                             @OA\Property(property="duration", type="integer", nullable=true)
     *                         )),
     *                         @OA\Property(property="codings", type="array", @OA\Items(
     *                             type="object",
     *                             @OA\Property(property="id", type="integer"),
     *                             @OA\Property(property="language", type="string"),
     *                             @OA\Property(property="problem_title", type="string"),
     *                             @OA\Property(property="problem_description", type="string", nullable=true),
     *                             @OA\Property(property="starter_code", type="string"),
     *                             @OA\Property(property="solution_code", type="string"),
     *                             @OA\Property(property="test_cases", type="array", @OA\Items(type="string"))
     *                         )),
     *                         @OA\Property(property="quizzes", type="array", @OA\Items(
     *                             type="object",
     *                             @OA\Property(property="id", type="integer"),
     *                             @OA\Property(property="title", type="string"),
     *                             @OA\Property(property="description", type="string", nullable=true)
     *                         ))
     *                     ))
     *                 ))
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Không tìm thấy khoá học",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Không tìm thấy khoá học")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Lỗi server",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Lỗi server nội bộ")
     *         )
     *     )
     * )
     */

    public function show($course_id)
    {
        try {
            $course = request()->user()->courses()->with([
                'sections' => function ($query) {
                    $query->orderBy('order');
                },
                'sections.lessons' => function ($query) {
                    $query->orderBy('order');
                },
                'sections.lessons.documents',
                'sections.lessons.videos',
                'sections.lessons.codings',
                'sections.lessons.quizzes',
                'category'
            ])->find($course_id);

            if (!$course) {
                return response()->json([
                    'message' => 'Không tìm thấy khoá học',
                ], Response::HTTP_NOT_FOUND);
            }

            return response()->json([
                'data' => $course,
            ], Response::HTTP_OK);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th,
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\Put(
     *     path="api/lecturer/courses/{course_id}",
     *     summary="Cập nhật khoá học",
     *     tags={"Lecturer - Courses"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="course_id",
     *         in="path",
     *         required=true,
     *         description="ID của khoá học cần cập nhật",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"category_id", "title"},
     *                 @OA\Property(property="category_id", type="integer", description="ID danh mục khóa học"),
     *                 @OA\Property(property="title", type="string", maxLength=255, description="Tiêu đề khóa học"),
     *                 @OA\Property(property="description", type="string", nullable=true, description="Mô tả khóa học"),
     *                 @OA\Property(property="price", type="integer", minimum=0, nullable=true, description="Giá khóa học"),
     *                 @OA\Property(property="price_sale", type="integer", minimum=0, nullable=true, description="Giá khuyến mãi (phải nhỏ hơn hoặc bằng giá gốc)"),
     *                 @OA\Property(property="target_students", type="string", nullable=true, description="Đối tượng học viên"),
     *                 @OA\Property(property="learning_outcomes", type="string", format="json", nullable=true, description="Kết quả học tập (JSON hợp lệ)"),
     *                 @OA\Property(property="prerequisites", type="string", nullable=true, description="Điều kiện tiên quyết"),
     *                 @OA\Property(property="who_is_this_for", type="string", nullable=true, description="Dành cho ai"),
     *                 @OA\Property(property="admin_commission_rate", type="number", minimum=0, maximum=100, nullable=true, description="Tỷ lệ hoa hồng của admin"),
     *                 @OA\Property(property="thumbnail", type="string", format="binary", nullable=true, description="Ảnh đại diện (jpeg, png, jpg, gif)"),
     *                 @OA\Property(property="video_preview", type="string", format="binary", nullable=true, description="Video xem trước (mp4, avi, mpeg, quicktime)"),
     *                 @OA\Property(property="language", type="string", nullable=true, description="Ngôn ngữ"),
     *                 @OA\Property(property="level", type="string", nullable=true, description="Trình độ"),
     *                 @OA\Property(property="primary_content", type="string", nullable=true, description="Nội dung chính"),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Cập nhật thành công",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Cập nhật thành công"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer"),
     *                 @OA\Property(property="user_id", type="integer"),
     *                 @OA\Property(property="category_id", type="integer"),
     *                 @OA\Property(property="price_regular", type="integer"),
     *                 @OA\Property(property="price_sale", type="integer"),
     *                 @OA\Property(property="title", type="string"),
     *                 @OA\Property(property="thumbnail", type="string"),
     *                 @OA\Property(property="video_preview", type="string"),
     *                 @OA\Property(property="description", type="string"),
     *                 @OA\Property(property="primary_content", type="string"),
     *                 @OA\Property(property="status", type="string", enum={"draft", "pending", "published"}),
     *                 @OA\Property(property="target_students", type="string"),
     *                 @OA\Property(property="learning_outcomes", type="string", format="json"),
     *                 @OA\Property(property="prerequisites", type="string"),
     *                 @OA\Property(property="who_is_this_for", type="string"),
     *                 @OA\Property(property="language", type="string"),
     *                 @OA\Property(property="level", type="string"),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Không thể cập nhật khoá học này vì không phải bản nháp",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Không thể cập nhật khoá học này vì không phải bản nháp")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Không tìm thấy khoá học",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Không tìm thấy khoá học")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Lỗi validation",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Lỗi validation."),
     *             @OA\Property(property="errors", type="object",
     *                 @OA\Property(property="category_id", type="array", @OA\Items(type="string", example="Danh mục khóa học là bắt buộc.")),
     *                 @OA\Property(property="title", type="array", @OA\Items(type="string", example="Tiêu đề khóa học là bắt buộc.")),
     *                 @OA\Property(property="price", type="array", @OA\Items(type="string", example="Giá khóa học phải là số nguyên.")),
     *                 @OA\Property(property="price_sale", type="array", @OA\Items(type="string", example="Giá khuyến mãi phải nhỏ hơn hoặc bằng giá gốc.")),
     *                 @OA\Property(property="learning_outcomes", type="array", @OA\Items(type="string", example="Kết quả học tập phải là chuỗi JSON hợp lệ.")),
     *                 @OA\Property(property="thumbnail", type="array", @OA\Items(type="string", example="Hình ảnh phải là tệp có định dạng jpeg, png, jpg hoặc gif.")),
     *                 @OA\Property(property="video_preview", type="array", @OA\Items(type="string", example="Video phải có định dạng mp4, avi, mpeg, quicktime."))
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Lỗi hệ thống",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Lỗi hệ thống"),
     *             @OA\Property(property="error", type="string")
     *         )
     *     )
     * )
     */


    public function update(UpdateCourseRequest $request, $course_id)
    {
        try {
            $course = $request->user()->courses()->find($course_id);

            if (!$course) {
                return response()->json([
                    'message' => 'Không tìm thấy khoá học'
                ], Response::HTTP_NOT_FOUND);
            }

            if ($course->status !== 'draft') {
                return response()->json([
                    'message' => 'Không thể cập nhật khoá học này vì không phải bản nháp'
                ], Response::HTTP_FORBIDDEN);
            }

            $data = $request->except('thumbnail', 'video_preview');

            if ($request->hasFile('thumbnail')) {
                $currentThumbnail = $course->thumbnail;
                $file = $request->file('thumbnail');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $data['thumbnail'] = $file->storeAs('images/thumbnails', $fileName, 'public');
            }

            if ($request->hasFile('video_preview')) {
                $currentVideo = $course->video_preview;
                $file = $request->file('video_preview');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $data['video_preview'] = $file->storeAs('videos/courses', $fileName, 'public');
            }

            $course->update($data);

            if (
                isset($currentThumbnail) &&
                $currentThumbnail &&
                !empty($currentThumbnail) &&
                Storage::exists($currentThumbnail)
            ) {
                Storage::delete($currentThumbnail);
            }

            if (
                isset($currentVideo) &&
                $currentVideo &&
                Storage::exists($currentVideo) &&
                !empty($currentVideo)
            ) {
                Storage::delete($currentVideo);
            }

            return response()->json([
                'message' => 'Cập nhật thành công',
                'data' => $course
            ], Response::HTTP_OK);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Lỗi hệ thống',
                'error' => $th
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\Delete(
     *     path="api/lecturer/courses/{course_id}",
     *     summary="Xoá khoá học",
     *     tags={"Lecturer - Courses"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="course_id",
     *         in="path",
     *         required=true,
     *         description="ID của khoá học cần xoá",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Xoá khoá học thành công (Không có nội dung trả về)"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Không thể xoá khoá học này vì không phải bản nháp",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Không thể xoá khoá học này vì không phải bản nháp")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Không tìm thấy khoá học",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Không tìm thấy khoá học")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Lỗi hệ thống",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Lỗi hệ thống"),
     *             @OA\Property(property="error", type="string")
     *         )
     *     )
     * )
     */

    public function destroy($course_id)
    {
        try {
            $course = request()->user()->courses()->find($course_id);

            if (!$course) {
                return response()->json([
                    'message' => 'Không tìm thấy khoá học'
                ], Response::HTTP_NOT_FOUND);
            }

            if ($course->status !== 'draft') {
                return response()->json([
                    'message' => 'Không thể xoá khoá học này vì không phải bản nháp'
                ], Response::HTTP_FORBIDDEN);
            }

            $currentThumbnail = $course->thumbnail;
            $currentVideo = $course->video_preview;

            $course->delete();

            if (
                isset($currentThumbnail) &&
                $currentThumbnail &&
                !empty($currentThumbnail) &&
                Storage::exists($currentThumbnail)
            ) {
                Storage::delete($currentThumbnail);
            }

            if (
                isset($currentVideo) &&
                $currentVideo &&
                Storage::exists($currentVideo) &&
                !empty($currentVideo)
            ) {
                Storage::delete($currentVideo);
            }

            return response()->noContent();
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th,
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function pending($course_id)
    {
        try {
            $course = request()->user()->courses()->find($course_id);

            if (!$course) {
                return response()->json([
                    'message' => 'Không tìm thấy khoá học'
                ], Response::HTTP_NOT_FOUND);
            }

            if ($course->status !== 'draft') {
                return response()->json([
                    'message' => 'Không thể gửi yêu cầu phê duyệt khoá học vì không phải bản nháp'
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $course->status = 'pending';
            $course->submited_at = now();
            $course->save();

            return response()->json([
                'message' => 'Gửi yêu cầu phê duyệt thành công',
                'data' => $course
            ], Response::HTTP_OK);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th,
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
 * @OA\Get(
 *     path="/courses/{course_id}/public",
 *     summary="Lấy chi tiết khoá học",
 *     description="API này trả về thông tin chi tiết của một khoá học đã được xuất bản, bao gồm giảng viên, số học viên, số bài học, rating trung bình và nội dung khoá học.",
 *     tags={"Courses"},
 *     @OA\Parameter(
 *         name="course_id",
 *         in="path",
 *         required=true,
 *         description="ID của khoá học",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Chi tiết khoá học",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="data", type="object",
 *                 @OA\Property(property="course", type="object",
 *                     @OA\Property(property="id", type="integer", example=1),
 *                     @OA\Property(property="title", type="string", example="Lập trình Web từ A-Z"),
 *                     @OA\Property(property="thumbnail", type="string", example="https://example.com/thumbnail.jpg"),
 *                     @OA\Property(property="video_preview", type="string", example="https://example.com/preview.mp4"),
 *                     @OA\Property(property="description", type="string", example="Khoá học lập trình Web toàn diện cho người mới."),
 *                     @OA\Property(property="primary_content", type="string", example="Các kiến thức về HTML, CSS, JS, PHP, Laravel."),
 *                     @OA\Property(property="price_regular", type="number", example=199.99),
 *                     @OA\Property(property="price_sale", type="number", example=99.99),
 *                     @OA\Property(property="status", type="string", example="published"),
 *                     @OA\Property(property="language", type="string", example="Tiếng Việt"),
 *                     @OA\Property(property="level", type="string", example="Beginner"),
 *                     @OA\Property(property="created_at", type="string", format="date-time", example="2025-02-16T16:44:38.000000Z"),
 *                     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-02-16T16:44:38.000000Z")
 *                 ),
 *                 @OA\Property(property="lecturer", type="object",
 *                     @OA\Property(property="id", type="integer", example=1),
 *                     @OA\Property(property="name", type="string", example="Nguyễn Văn A"),
 *                     @OA\Property(property="email", type="string", example="nguyenvana@example.com")
 *                 ),
 *                 @OA\Property(property="student_count", type="integer", example=150),
 *                 @OA\Property(property="total_lessons", type="integer", example=35),
 *                 @OA\Property(property="average_rating", type="number", example=4.7)
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Không tìm thấy khoá học",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Không tìm thấy khoá học")
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Lỗi hệ thống",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Lỗi hệ thống"),
 *             @OA\Property(property="error", type="string", example="Chi tiết lỗi từ server")
 *         )
 *     )
 * )
 */

    public function publicCourseDetail($course_id)
    {
        try {
            $course = Course::with([
                'user:id,name,email', // Lấy thông tin giảng viên
                'sections' => function ($query) {
                    $query->orderBy('order');
                },
                'sections.lessons' => function ($query) {
                    $query->orderBy('order')->select('id', 'section_id', 'title', 'description');
                },
                'sections.lessons.videos:id,lesson_id,video_url',
                'sections.lessons.documents:id,lesson_id,document_url',
                'sections.lessons.codings:id,lesson_id,problem_title,problem_description',
                'sections.lessons.quizzes:id,lesson_id,title,description',
                'category:id,name'
            ])
                ->where('status', 'published')
                ->select([
                    'id',
                    'user_id',
                    'category_id',
                    'price_regular',
                    'price_sale',
                    'title',
                    'thumbnail',
                    'video_preview',
                    'description',
                    'primary_content',
                    'status',
                    'is_show_home',
                    'target_students',
                    'learning_outcomes',
                    'prerequisites',
                    'who_is_this_for',
                    'language',
                    'level',
                    'created_at',
                    'updated_at'
                ])
                ->find($course_id);

            if (!$course) {
                return response()->json([
                    'message' => 'Không tìm thấy khoá học'
                ], Response::HTTP_NOT_FOUND);
            }

            // Đếm số học viên đã đăng ký khóa học
            $studentCount = $course->enrollments()->count(DB::raw('DISTINCT(user_id)'));

            // Đếm tổng số bài học trong khóa học
            $totalLessons = $course->lessons()->count();

            // Tính trung bình rating của khóa học
            $averageRating = $course->reviews()->avg('rating') ?? 0;

            return response()->json([
                'data' => [
                    'course' => $course,
                    'instructor' => $course->user, // Trả về thông tin giảng viên
                    'student_count' => $studentCount,
                    'total_lessons' => $totalLessons,
                    'average_rating' => round($averageRating, 1),
                ]
            ], Response::HTTP_OK);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Lỗi hệ thống',
                'error' => $th
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
