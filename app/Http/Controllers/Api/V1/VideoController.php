<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use getID3;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;


/**
 * @OA\Server(
 *      url=L5_SWAGGER_CONST_HOST,
 *      description="API Server"
 * )
 */



class VideoController extends Controller
{
    /**
     * @OA\Get(
     *     path="/lecturer/courses/{course_id}/sections/{section_id}/lessons/{lesson_id}/videos",
     *     summary="Lấy danh sách video của bài học",
     *     description="API này trả về danh sách video thuộc về bài học cụ thể",
     *     tags={"Videos"},
     *     security={{"sanctum":{}}},
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
     *         description="Danh sách video",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="lesson_id", type="integer"),
     *                     @OA\Property(property="video_url", type="string", example="storage/videos/sample.mp4"),
     *                     @OA\Property(property="duration", type="integer", example=120),
     *                     @OA\Property(property="created_at", type="string", format="date-time", example="2025-02-16T10:00:00Z"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-02-16T10:30:00Z")
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
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Không tìm thấy tài nguyên")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Lỗi hệ thống",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Đã xảy ra lỗi trong quá trình xử lý")
     *         )
     *     )
     * )
     */




    public function index(Request $request, $course_id, $section_id, $lesson_id)
    {
        try {

            $course = $request->user()->courses()
                ->with(['sections' => function ($query) use ($section_id) {
                    $query->where('id', $section_id);
                }, 'sections.lessons' => function ($query) use ($lesson_id) {
                    $query->where('id', $lesson_id);
                }])
                ->find($course_id);


            if (!$course || !$course->sections->first() || !$lesson = $course->sections->first()->lessons->first()) {
                return response()->json(['message' => 'Không tìm thấy tài nguyên'], 404);
            }


            $videos = $lesson->videos;


            return response()->json([
                'data' => $videos,
            ]);
        } catch (\Exception $e) {

            return response()->json(['message' => 'Đã xảy ra lỗi trong quá trình xử lý'], 500);
        }
    }


    /**
     * @OA\Post(
     *     path="/courses/{course_id}/sections/{section_id}/lessons/{lesson_id}/videos",
     *     summary="Thêm video mới vào bài học",
     *     description="Tải lên và lưu trữ video cho một bài học cụ thể",
     *     operationId="storeVideo",
     *     tags={"Videos"},
     *     security={{"bearerAuth": {}}},
     * 
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
     *         description="ID của phần trong khóa học",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="lesson_id",
     *         in="path",
     *         required=true,
     *         description="ID của bài học",
     *         @OA\Schema(type="integer")
     *     ),
     * 
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"video_url"},
     *                 @OA\Property(
     *                     property="video_url",
     *                     type="string",
     *                     format="binary",
     *                     description="File video (định dạng mp4, mov, avi)"
     *                 )
     *             )
     *         )
     *     ),
     * 
     *     @OA\Response(
     *         response=201,
     *         description="Tạo mới video thành công",
     *         @OA\JsonContent(
     *             @OA\Property(property="video", type="object",
     *                 @OA\Property(property="id", type="integer"),
     *                 @OA\Property(property="lesson_id", type="integer"),
     *                 @OA\Property(property="video_url", type="string", description="Đường dẫn video"),
     *                 @OA\Property(property="duration", type="integer", description="Thời lượng video"),
     *                 @OA\Property(property="created_at", type="string", format="date-time"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time")
     *             ),
     *             @OA\Property(property="message", type="string", example="Tạo mới video thành công")
     *         )
     *     ),
     * 
     *     @OA\Response(
     *         response=400,
     *         description="Không thể lấy thời lượng video",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Không thể lấy thời lượng video")
     *         )
     *     ),
     * 
     *     @OA\Response(
     *         response=404,
     *         description="Không tìm thấy tài nguyên",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Không tìm thấy tài nguyên")
     *         )
     *     ),
     * 
     *     @OA\Response(
     *         response=422,
     *         description="Dữ liệu không hợp lệ hoặc thời lượng video không đủ",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Dữ liệu không hợp lệ"),
     *             @OA\Property(property="errors", type="object",
     *                 @OA\Property(property="video_url", type="array",
     *                     @OA\Items(type="string", example="The video_url field is required.")
     *                 ),
     *                 @OA\Property(property="duration", type="array",
     *                     @OA\Items(type="string", example="Thời lượng video phải lớn hơn 1 phút.")
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
     *         response=500,
     *         description="Lỗi hệ thống",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Lỗi hệ thống"),
     *             @OA\Property(property="error", type="string", example="Chi tiết lỗi")
     *         )
     *     )
     * )
     */

    public function store(Request $request, $course_id, $section_id, $lesson_id)
    {
        try {
            $course = $request->user()->courses()->with(['sections' => function ($query) use ($section_id) {
                $query->where('id', $section_id);
            }, 'sections.lessons' => function ($query) use ($lesson_id) {
                $query->where('id', $lesson_id);
            }])->find($course_id);

            if (!$course || !$course->sections->first() || !$lesson = $course->sections->first()->lessons->first()) {
                return response()->json(['message' => 'Không tìm thấy tài nguyên'], 404); // Combined check
            }

            $validator = Validator::make($request->all(), [
                'video_url' => 'required|file|mimes:mp4,mov,avi',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Dữ liệu không hợp lệ',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $data = $request->except('video_url');

            if ($request->hasFile('video_url')) {
                $file = $request->file('video_url');
                $newFileName = time() . '_' . $file->getClientOriginalName(); // Add timestamp to filename
                $data['video_url'] = $file->storeAs('videos', $newFileName);
            }

            // Lấy thời lượng video bằng getID3
            $getID3 = new getID3();
            $fileInfo = $getID3->analyze($file->getRealPath());

            if (!isset($fileInfo['playtime_seconds'])) {
                Storage::delete($data['video_url']);
                return response()->json([
                    'message' => 'Không thể lấy thời lượng video',
                ], 400);
            }

            $duration = round($fileInfo['playtime_seconds']);

            $video = $lesson->videos()->create([
                'video_url' => $data['video_url'],
                'duration' => $duration,
            ]);

            return response()->json([
                'video' => $video,
                'message' => 'Tạo mới video thành công',
            ], 201);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Lỗi hệ thống',
                'error' => $th->getMessage(),
            ], 500);
        }
    }


    /**
     * @OA\Get(
     *     path="/lecturer/courses/{course_id}/sections/{section_id}/lessons/{lesson_id}/videos/{video_id}",
     *     summary="Lấy thông tin chi tiết của một video",
     *     description="API này trả về thông tin chi tiết của một video thuộc về bài học cụ thể",
     *     tags={"Videos"},
     *     security={{"sanctum":{}}},
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
     *         name="video_id",
     *         in="path",
     *         required=true,
     *         description="ID của video",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Thông tin chi tiết của video",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="lesson_id", type="integer", example=1),
     *                 @OA\Property(property="video_url", type="string", example="storage/videos/sample.mp4"),
     *                 @OA\Property(property="duration", type="integer", example=120),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2025-02-16T10:00:00Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2025-02-16T10:30:00Z")
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
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Không tìm thấy tài nguyên")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Lỗi hệ thống",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Đã xảy ra lỗi trong quá trình xử lý")
     *         )
     *     )
     * )
     */


    public function show(Request $request, $course_id, $section_id, $lesson_id, $video_id)
    {
        try {
            // Lấy khóa học thuộc về người dùng hiện tại và kiểm tra section và lesson
            $course = $request->user()->courses()
                ->with(['sections' => function ($query) use ($section_id) {
                    $query->where('id', $section_id);
                }, 'sections.lessons' => function ($query) use ($lesson_id) {
                    $query->where('id', $lesson_id);
                }])
                ->find($course_id);

            // Kiểm tra xem course, section, và lesson có tồn tại không
            if (!$course || !$course->sections->first() || !$lesson = $course->sections->first()->lessons->first()) {
                return response()->json(['message' => 'Không tìm thấy tài nguyên'], 404);
            }

            // Kiểm tra xem video có tồn tại trong lesson không
            $video = $lesson->videos->where('id', $video_id)->first();
            if (!$video) {
                return response()->json(['message' => 'Video không tồn tại'], 404);
            }

            // Trả về response dạng JSON
            return response()->json([
                'data' => $video,
            ]);
        } catch (\Exception $e) {
            // Xử lý lỗi nếu có
            return response()->json(['message' => 'Đã xảy ra lỗi trong quá trình xử lý'], 500);
        }
    }


    /**
     * @OA\Put(
     *     path="/lecturer/courses/{course_id}/sections/{section_id}/lessons/{lesson_id}/videos/{video_id}",
     *     summary="Cập nhật video của bài học",
     *     description="API này cho phép giảng viên cập nhật video cho một bài học",
     *     tags={"Videos"},
     *     security={{"sanctum":{}}},
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
     *         name="video_id",
     *         in="path",
     *         required=true,
     *         description="ID của video cần cập nhật",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Dữ liệu video cần cập nhật",
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"video_url"},
     *                 @OA\Property(
     *                     property="video_url",
     *                     type="string",
     *                     format="binary",
     *                     description="File video (mp4, mov, avi)"
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Cập nhật video thành công",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="video", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="lesson_id", type="integer"),
     *                 @OA\Property(property="video_url", type="string", example="storage/videos/new_video.mp4"),
     *                 @OA\Property(property="duration", type="integer", example=180),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2025-02-16T10:00:00Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2025-02-16T12:00:00Z")
     *             ),
     *             @OA\Property(property="message", type="string", example="Cập nhật video thành công")
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
     *         response=400,
     *         description="Không thể lấy thời lượng video",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Không thể lấy thời lượng video")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Dữ liệu không hợp lệ hoặc thời lượng video không đủ",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Dữ liệu không hợp lệ"),
     *             @OA\Property(property="errors", type="object",
     *                 @OA\Property(property="video_url", type="array", @OA\Items(type="string", example="File không đúng định dạng")),
     *                 @OA\Property(property="duration", type="array", @OA\Items(type="string", example="Thời lượng video phải lớn hơn 1 phút."))
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
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Lỗi hệ thống",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Lỗi hệ thống"),
     *             @OA\Property(property="error", type="string", example="Chi tiết lỗi hệ thống")
     *         )
     *     )
     * )
     */
    public function update(Request $request, $course_id, $section_id, $lesson_id, $video_id)
    {
        try {
            $course = $request->user()->courses()->with(['sections' => function ($query) use ($section_id) {
                $query->where('id', $section_id);
            }, 'sections.lessons' => function ($query) use ($lesson_id) {
                $query->where('id', $lesson_id);
            }, 'sections.lessons.videos' => function ($query) use ($video_id) {
                $query->where('id', $video_id);
            }])->find($course_id);

            if (
                !$course ||
                !$course->sections->first() ||
                !$course->sections->first()->lessons->first() ||
                !$video = $course->sections->first()->lessons->first()->videos->first()
            ) {
                return response()->json(['message' => 'Không tìm thấy tài nguyên'], 404); // Combined check
            }

            $validator = Validator::make($request->all(), [
                'video_url' => 'required|file|mimes:mp4,mov,avi',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Dữ liệu không hợp lệ',
                    'errors' => $validator->errors(),
                ], 422);
            }
            $data = $request->except('video_url');

            $currentVideo = $video->video_url;

            if ($request->hasFile('video_url')) {
                if ($request->hasFile('video_url')) {
                    $file = $request->file('video_url');

                    $newFileName = time() . '_' . $file->getClientOriginalName(); // Add timestamp to filename
                    $data['video_url'] = $file->storeAs('videos', $newFileName);
                }
            }


            // Lấy thời lượng video bằng getID3
            $getID3 = new getID3();
            $fileInfo = $getID3->analyze($file->getRealPath());

            if (!isset($fileInfo['playtime_seconds'])) {
                Storage::delete($data['video_url']);
                return response()->json([
                    'message' => 'Không thể lấy thời lượng video',
                ], 400);
            }

            $duration = round($fileInfo['playtime_seconds']);

            // Cập nhật video mới vào database
            $video->update([
                'video_url' => $data['video_url'],
                'duration' => $duration,
            ]);
            if ($currentVideo && Storage::exists($currentVideo) && !empty($currentVideo)) {
                Storage::delete($currentVideo);
            }

            return response()->json([
                'video' => $video,
                'message' => 'Cập nhật video thành công',
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Lỗi hệ thống',
                'error' => $th->getMessage(),
            ], 500);
        }
    }


    /**
     * @OA\Delete(
     *     path="/lecturer/courses/{course_id}/sections/{section_id}/lessons/{lesson_id}/videos/{video_id}",
     *     summary="Xóa video của bài học",
     *     description="API này cho phép giảng viên xóa một video khỏi bài học",
     *     tags={"Videos"},
     *     security={{"sanctum":{}}},
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
     *         name="video_id",
     *         in="path",
     *         required=true,
     *         description="ID của video cần xóa",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized - Chưa đăng nhập hoặc token không hợp lệ",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Xóa video thành công"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Không tìm thấy tài nguyên",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Không tìm thấy tài nguyên")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Lỗi hệ thống",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Lỗi hệ thống"),
     *             @OA\Property(property="error", type="string", example="Chi tiết lỗi hệ thống")
     *         )
     *     )
     * )
     */

    public function destroy(Request $request, $course_id, $section_id, $lesson_id, $video_id)
    {
        try {
            $course = $request->user()->courses()->with(['sections' => function ($query) use ($section_id) {
                $query->where('id', $section_id);
            }, 'sections.lessons' => function ($query) use ($lesson_id) {
                $query->where('id', $lesson_id);
            }, 'sections.lessons.videos' => function ($query) use ($video_id) {
                $query->where('id', $video_id);
            }])->find($course_id);

            if (
                !$course ||
                !$course->sections->first() ||
                !$course->sections->first()->lessons->first() ||
                !$video = $course->sections->first()->lessons->first()->videos->first()
            ) {
                return response()->json(['message' => 'Không tìm thấy tài nguyên'], 404); // Combined check
            }

            $currentVideo = $video->video_url;

            $video->delete();

            if ($currentVideo && Storage::exists($currentVideo) && !empty($currentVideo)) {
                Storage::delete($currentVideo);
            }

            return response()->noContent();
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Lỗi hệ thống',
                'error' => $th->getMessage(),
            ], 500);
        }
    }
}
