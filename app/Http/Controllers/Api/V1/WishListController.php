<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\WishList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WishListController extends Controller
{
    /**
     * @OA\Get(
     *     path="/users/{userId}/wishlist",
     *     summary="Lấy danh sách wishlist của người dùng",
     *     description="API này trả về danh sách các khóa học mà người dùng đã thêm vào wishlist.",
     *     tags={"WishList"},
     *     security={{ "bearerAuth":{} }},
     *     @OA\Parameter(
     *         name="userId",
     *         in="path",
     *         required=true,
     *         description="ID của người dùng",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Danh sách wishlist của người dùng",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="course_id", type="integer", example=10),
     *                     @OA\Property(property="course_name", type="string", example="Laravel for Beginners"),
     *                     @OA\Property(property="created_at", type="string", format="date-time", example="2024-02-15T12:00:00Z")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Người dùng không có quyền truy cập wishlist này",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Unauthorized")
     *         )
     *     )
     * )
     */
    public function index($user_id)
    {
        if (Auth::id() != $user_id) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 403);
        }

        $wishlist = WishList::where('user_id', $user_id)
            ->with('course')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $wishlist->map(function ($item) {
                return [
                    'id' => $item->id,
                    'course_id' => $item->course_id,
                    'course_name' => $item->course->name,
                    'created_at' => $item->created_at
                ];
            })
        ]);
    }

    /**
     * @OA\Post(
     *     path="/users/{userId}/wishlist/{courseId}",
     *     summary="Thêm khóa học vào danh sách yêu thích",
     *     description="API này cho phép người dùng thêm một khóa học vào wishlist.",
     *     tags={"WishList"},
     *     security={{ "bearerAuth":{} }},
     *     @OA\Parameter(
     *         name="userId",
     *         in="path",
     *         required=true,
     *         description="ID của người dùng",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Parameter(
     *         name="courseId",
     *         in="path",
     *         required=true,
     *         description="ID của khóa học",
     *         @OA\Schema(type="integer", example=10)
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Khóa học đã được thêm vào wishlist",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="khóa học đã được thêm vào danh sách ưu thích"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="user_id", type="integer", example=1),
     *                 @OA\Property(property="course_id", type="integer", example=10),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2024-02-16T12:00:00Z")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Người dùng không có quyền truy cập",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Unauthorized")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Không tìm thấy khóa học",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="không tìm thấy khóa học")
     *         )
     *     )
     * )
     */

    public function store($user_id, $course_id)
    {
        if (Auth::id() != $user_id) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 403);
        }

        // Kiểm tra course_id có hợp lệ không
        if (!Course::where('id', $course_id)->exists()) {
            return response()->json(['status' => 'error', 'message' => 'không tìm thấy khóa học'], 404);
        }

        $wishlist = WishList::create([
            'user_id' => $user_id,
            'course_id' => $course_id
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'khóa học đã được thêm vào danh sách ưu thích',
            'data' => $wishlist
        ], 201);
    }

    /**
     * @OA\Delete(
     *     path="/users/{userId}/wishlist/{courseId}",
     *     summary="Xóa khóa học khỏi danh sách yêu thích",
     *     description="API này cho phép người dùng xóa một khóa học khỏi wishlist.",
     *     tags={"WishList"},
     *     security={{ "bearerAuth":{} }},
     *     @OA\Parameter(
     *         name="userId",
     *         in="path",
     *         required=true,
     *         description="ID của người dùng",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Parameter(
     *         name="courseId",
     *         in="path",
     *         required=true,
     *         description="ID của khóa học",
     *         @OA\Schema(type="integer", example=10)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Xóa khóa học thành công",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Khóa học đã được xóa khỏi danh sách yêu thích")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Người dùng không có quyền xóa khóa học",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Unauthorized")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Khóa học không tồn tại trong danh sách yêu thích",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Khóa học không tồn tại trong danh sách yêu thích")
     *         )
     *     )
     * )
     */

    public function destroy($user_id, $course_id)
    {
        if (Auth::id() != $user_id) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 403);
        }

        try {
            $wishlist = WishList::where('user_id', $user_id)
                ->where('course_id', $course_id)
                ->firstOrFail();

            $wishlist->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Khóa học đã được xóa khỏi danh sách yêu thích'
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Khóa học không tồn tại trong danh sách yêu thích'
            ], 404);
        }
    }

    /**
     * @OA\Get(
     *     path="/users/{userId}/wishlist/{courseId}/check",
     *     summary="Kiểm tra khóa học có trong danh sách yêu thích",
     *     description="API này kiểm tra xem khóa học có trong danh sách yêu thích của người dùng hay không.",
     *     tags={"WishList"},
     *     security={{ "bearerAuth":{} }},
     *     @OA\Parameter(
     *         name="userId",
     *         in="path",
     *         required=true,
     *         description="ID của người dùng",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Parameter(
     *         name="courseId",
     *         in="path",
     *         required=true,
     *         description="ID của khóa học",
     *         @OA\Schema(type="integer", example=10)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Kết quả kiểm tra danh sách yêu thích",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Khóa học đang nằm trong danh sách yêu thích")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Khóa học không tồn tại trong danh sách yêu thích",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Khóa học không tồn tại trong danh sách yêu thích")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Người dùng không có quyền truy cập",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Unauthorized")
     *         )
     *     )
     * )
     */

    public function check($user_id, $course_id)
    {
        if (Auth::id() != $user_id) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 403);
        }

        $exists = WishList::where('user_id', $user_id)
            ->where('course_id', $course_id)
            ->exists();

        if ($exists) {
            return response()->json([
                'status' => 'success',
                'message' => 'Khóa học đang nằm trong danh sách yêu thích'
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Khóa học không tồn tại trong danh sách yêu thích'
        ]);
    }
}
