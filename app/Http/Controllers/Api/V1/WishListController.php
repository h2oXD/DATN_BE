<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\WishList;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class WishListController extends Controller
{
    /**
     * @OA\Get(
     *     path="/wishlist",
     *     summary="Lấy danh sách khóa học trong wishlist",
     *     description="API này trả về danh sách các khóa học mà người dùng đã thêm vào wishlist.",
     *     tags={"WishList"},
     *     security={{ "bearerAuth":{} }},
     *     @OA\Response(
     *         response=200,
     *         description="Danh sách khóa học trong wishlist",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Lấy dữ liệu thành công"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="course", type="object",
     *                         @OA\Property(property="id", type="integer", example=10),
     *                         @OA\Property(property="user_id", type="integer", example=2),
     *                         @OA\Property(property="category_id", type="integer", example=3),
     *                         @OA\Property(property="price_regular", type="number", example=200000),
     *                         @OA\Property(property="price_sale", type="number", example=150000),
     *                         @OA\Property(property="title", type="string", example="Lập trình Laravel"),
     *                         @OA\Property(property="thumbnail", type="string", example="https://example.com/thumbnail.jpg"),
     *                         @OA\Property(property="video_preview", type="string", example="https://example.com/preview.mp4"),
     *                         @OA\Property(property="description", type="string", example="Mô tả khóa học Laravel"),
     *                         @OA\Property(property="status", type="string", example="published"),
     *                         @OA\Property(property="is_show_home", type="boolean", example=true),
     *                         @OA\Property(property="target_students", type="string", example="Sinh viên CNTT"),
     *                         @OA\Property(property="learning_outcomes", type="string", example="Hiểu về Laravel"),
     *                         @OA\Property(property="prerequisites", type="string", example="Biết PHP cơ bản"),
     *                         @OA\Property(property="who_is_this_for", type="string", example="Người mới học lập trình"),
     *                         @OA\Property(property="is_free", type="boolean", example=false),
     *                         @OA\Property(property="language", type="string", example="Vietnamese"),
     *                         @OA\Property(property="level", type="string", example="Beginner"),
     *                         @OA\Property(property="created_at", type="string", format="date-time", example="2024-02-25T10:00:00Z"),
     *                         @OA\Property(property="updated_at", type="string", format="date-time", example="2024-02-26T12:00:00Z"),
     *                         @OA\Property(property="category", type="object",
     *                             @OA\Property(property="id", type="integer", example=3),
     *                             @OA\Property(property="name", type="string", example="Lập trình Backend")
     *                         ),
     *                         @OA\Property(property="user", type="object",
     *                             @OA\Property(property="id", type="integer", example=2),
     *                             @OA\Property(property="name", type="string", example="Nguyễn Văn A")
     *                         )
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Không tìm thấy khóa học trong wishlist",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Không tìm thấy khóa học trong wish-list")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Lỗi hệ thống",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Lỗi hệ thống"),
     *             @OA\Property(property="error", type="string", example="Chi tiết lỗi hệ thống")
     *         )
     *     )
     * )
     */

    public function index()
    {
        try {
            $user_id = Auth::id();
            $wishlist = WishList::where('user_id', $user_id)
                ->with([
                    'course' => function ($query) {
                        $query->select([
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
                            // 'who_is_this_for',
                            'is_free',
                            'language',
                            'level',
                            'created_at',
                            'updated_at',
                        ]);
                    },
                    'course.category' => function ($query) {
                        $query->select('id', 'name'); // Chỉ lấy id và name của category
                    },
                    'course.user' => function ($query) {
                        $query->select('id', 'name'); // Chỉ lấy id và name của user (giảng viên)
                    }
                ])
                ->get();

            if ($wishlist->isEmpty()) {
                return response()->json([
                    'message' => 'Không tìm thấy khóa học trong wish-list'
                ], Response::HTTP_NOT_FOUND);
            }

            return response()->json([
                'message' => 'Lấy dữ liệu thành công',
                'data' => $wishlist
            ], Response::HTTP_OK);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Lỗi hệ thống',
                'error' => $th->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/wishlist/{course_id}",
     *     summary="Thêm khóa học vào wish-list",
     *     tags={"WishList"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="course_id",
     *         in="path",
     *         required=true,
     *         description="ID của khóa học cần thêm",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Thêm khóa học thành công",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Khóa học đã được thêm vào danh sách yêu thích"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="user_id", type="integer", example=1),
     *                 @OA\Property(property="course_id", type="integer", example=5),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2025-03-01T10:00:00Z")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Khóa học đã có trong wish-list",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Khóa học này đã có trong danh sách yêu thích")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Không tìm thấy khóa học",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Không tìm thấy khóa học")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Lỗi hệ thống",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Lỗi hệ thống")
     *         )
     *     )
     * )
     */
    public function store($course_id)
    {
        try {
            $user_id = Auth::id();

            // Kiểm tra khóa học có tồn tại không
            if (!Course::where('id', $course_id)->exists()) {
                return response()->json([
                    'message' => 'Không tìm thấy khóa học'
                ], Response::HTTP_NOT_FOUND);
            }

            // Kiểm tra trùng lặp
            if (WishList::where('user_id', $user_id)->where('course_id', $course_id)->exists()) {
                return response()->json([
                    'message' => 'Khóa học này đã có trong danh sách yêu thích'
                ], Response::HTTP_BAD_REQUEST);
            }

            $wishlist = WishList::create([
                'user_id' => $user_id,
                'course_id' => $course_id
            ]);

            return response()->json([
                'message' => 'Khóa học đã được thêm vào danh sách yêu thích',
                'data' => $wishlist
            ], Response::HTTP_CREATED);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Lỗi hệ thống',
                'error' => $th->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/wishlist/{course_id}",
     *     summary="Xóa khóa học khỏi wish-list",
     *     tags={"WishList"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="course_id",
     *         in="path",
     *         required=true,
     *         description="ID của khóa học cần xóa",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Xóa khóa học thành công",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Khóa học đã được xóa khỏi danh sách yêu thích")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Không tìm thấy khóa học trong wish-list",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Khóa học không tồn tại trong danh sách yêu thích")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Lỗi hệ thống",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Lỗi hệ thống")
     *         )
     *     )
     * )
     */
    public function destroy($course_id)
    {
        try {
            $user_id = Auth::id();

            $wishlist = WishList::where('user_id', $user_id)
                ->where('course_id', $course_id)
                ->first();

            if (!$wishlist) {
                return response()->json([
                    'message' => 'Khóa học không tồn tại trong danh sách yêu thích'
                ], Response::HTTP_NOT_FOUND);
            }

            $wishlist->delete();

            return response()->json([
                'message' => 'Khóa học đã được xóa khỏi danh sách yêu thích'
            ], Response::HTTP_OK);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Lỗi hệ thống',
                'error' => $th->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/wishlist/check/{course_id}",
     *     summary="Kiểm tra khóa học trong wish-list",
     *     tags={"WishList"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="course_id",
     *         in="path",
     *         required=true,
     *         description="ID của khóa học cần kiểm tra",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Kết quả kiểm tra",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Khóa học đang nằm trong danh sách yêu thích")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Không tìm thấy khóa học trong wish-list",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Khóa học không tồn tại trong danh sách yêu thích")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Lỗi hệ thống",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Lỗi hệ thống")
     *         )
     *     )
     * )
     */
    public function check($course_id)
    {
        try {
            $user_id = Auth::id();

            $exists = WishList::where('user_id', $user_id)
                ->where('course_id', $course_id)
                ->exists();

            if ($exists) {
                return response()->json([
                    'message' => 'Khóa học đang nằm trong danh sách yêu thích'
                ], Response::HTTP_OK);
            }

            return response()->json([
                'message' => 'Khóa học không tồn tại trong danh sách yêu thích'
            ], Response::HTTP_NOT_FOUND);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Lỗi hệ thống',
                'error' => $th->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
