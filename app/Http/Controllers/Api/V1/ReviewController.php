<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    /**
     * @OA\Post(
     *     path="/user/{user_id}/courses/{course_id}/reviews",
     *     summary="Thêm đánh giá cho khóa học",
     *     description="API này cho phép người dùng thêm đánh giá cho một khóa học với số điểm từ 1 đến 5 và nội dung đánh giá tùy chọn.",
     *     tags={"Reviews"},
     *     security={{ "bearerAuth":{} }},
     *     @OA\Parameter(
     *         name="course_id",
     *         in="path",
     *         required=true,
     *         description="ID của khóa học",
     *         @OA\Schema(type="integer", example=5)
     *     ),
     *     @OA\Parameter(
     *         name="user_id",
     *         in="path",
     *         required=true,
     *         description="ID của người dùng",
     *         @OA\Schema(type="integer", example=10)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"rating"},
     *             @OA\Property(property="rating", type="integer", example=5, description="Điểm đánh giá từ 1 đến 5"),
     *             @OA\Property(property="review_text", type="string", example="Khoá học rất hay!", description="Nội dung đánh giá (tuỳ chọn)")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Đánh giá đã được thêm thành công!",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Đánh giá đã được thêm thành công!"),
     *             @OA\Property(property="review", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="user_id", type="integer", example=10),
     *                 @OA\Property(property="course_id", type="integer", example=5),
     *                 @OA\Property(property="rating", type="integer", example=5),
     *                 @OA\Property(property="review_text", type="string", example="Khoá học rất hay!"),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2024-02-25T10:30:00Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2024-02-25T10:30:00Z")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Dữ liệu không hợp lệ",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Dữ liệu không hợp lệ"),
     *             @OA\Property(property="errors", type="object",
     *                 @OA\Property(property="rating", type="array",
     *                     @OA\Items(type="string", example="The rating field is required.")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized - Người dùng chưa đăng nhập hoặc token không hợp lệ",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthorized")
     *         )
     *     )
     * )
     */
    public function store(Request $request, $user_id, $course_id)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'review_text' => 'nullable|string',
        ]);

        $review = Review::create([
            'user_id' => $user_id,
            'course_id' => $course_id,
            'rating' => $request->input('rating'),
            'review_text' => $request->input('review_text'),
        ]);

        return response()->json([
            'message' => 'Đánh giá đã được thêm thành công!',
            'review' => $review
        ], 201);
    }

    /**
     * @OA\Put(
     *     path="/user/{user_id}/reviews/{review_id}",
     *     summary="Cập nhật đánh giá của người dùng",
     *     description="API này cho phép người dùng cập nhật đánh giá của họ về một khóa học, bao gồm thay đổi điểm số hoặc nội dung đánh giá.",
     *     tags={"Reviews"},
     *     security={{ "bearerAuth":{} }},
     *     @OA\Parameter(
     *         name="user_id",
     *         in="path",
     *         required=true,
     *         description="ID của người dùng",
     *         @OA\Schema(type="integer", example=10)
     *     ),
     *     @OA\Parameter(
     *         name="review_id",
     *         in="path",
     *         required=true,
     *         description="ID của đánh giá",
     *         @OA\Schema(type="integer", example=5)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="rating", type="integer", example=4, description="Điểm đánh giá từ 1 đến 5 (tuỳ chọn)"),
     *             @OA\Property(property="review_text", type="string", example="Khoá học rất hữu ích!", description="Nội dung đánh giá (tuỳ chọn)")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Cập nhật đánh giá thành công!",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Cập nhật đánh giá thành công!"),
     *             @OA\Property(property="review", type="object",
     *                 @OA\Property(property="id", type="integer", example=5),
     *                 @OA\Property(property="user_id", type="integer", example=10),
     *                 @OA\Property(property="course_id", type="integer", example=2),
     *                 @OA\Property(property="rating", type="integer", example=4),
     *                 @OA\Property(property="review_text", type="string", example="Khoá học rất hữu ích!"),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2024-02-25T10:30:00Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2024-02-26T12:00:00Z")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Đánh giá không tồn tại hoặc không thuộc về người dùng",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Đánh giá không tồn tại hoặc không thuộc về người dùng")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Dữ liệu không hợp lệ",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Dữ liệu không hợp lệ"),
     *             @OA\Property(property="errors", type="object",
     *                 @OA\Property(property="rating", type="array",
     *                     @OA\Items(type="string", example="The rating field must be between 1 and 5.")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized - Người dùng chưa đăng nhập hoặc token không hợp lệ",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthorized")
     *         )
     *     )
     * )
     */
    public function update(Request $request, $user_id, $review_id)
    {
        $review = Review::where('id', $review_id)->where('user_id', $user_id)->first();

        if (!$review) {
            return response()->json(['message' => 'Đánh giá không tồn tại hoặc không thuộc về người dùng'], 404);
        }

        $request->validate([
            'rating' => 'integer|min:1|max:5',
            'review_text' => 'nullable|string',
        ]);

        $review->update([
            'rating' => $request->input('rating', $review->rating),
            'review_text' => $request->input('review_text', $review->review_text),
        ]);

        return response()->json([
            'message' => 'Cập nhật đánh giá thành công!',
            'review' => $review
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/user/{user_id}/reviews/{review_id}",
     *     summary="Xóa đánh giá của người dùng",
     *     description="API này cho phép người dùng xóa đánh giá của họ về một khóa học.",
     *     tags={"Reviews"},
     *     security={{ "bearerAuth":{} }},
     *     @OA\Parameter(
     *         name="user_id",
     *         in="path",
     *         required=true,
     *         description="ID của người dùng",
     *         @OA\Schema(type="integer", example=10)
     *     ),
     *     @OA\Parameter(
     *         name="review_id",
     *         in="path",
     *         required=true,
     *         description="ID của đánh giá",
     *         @OA\Schema(type="integer", example=5)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Xóa đánh giá thành công!",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Xóa đánh giá thành công!")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Đánh giá không tồn tại hoặc không thuộc về người dùng",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Đánh giá không tồn tại hoặc không thuộc về người dùng")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized - Người dùng chưa đăng nhập hoặc token không hợp lệ",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthorized")
     *         )
     *     )
     * )
     */
    public function destroy($user_id, $review_id)
    {
        $review = Review::where('id', $review_id)->where('user_id', $user_id)->first();

        if (!$review) {
            return response()->json(['message' => 'Đánh giá không tồn tại hoặc không thuộc về người dùng'], 404);
        }

        $review->delete();

        return response()->json(['message' => 'Xóa đánh giá thành công!']);
    }

    /**
     * @OA\Get(
     *     path="/courses/{course_id}/reviews",
     *     summary="Lấy danh sách đánh giá của một khóa học",
     *     description="API này trả về danh sách đánh giá của một khóa học, có thể lọc theo số sao và sắp xếp theo thời gian.",
     *     tags={"Reviews"},
     *     @OA\Parameter(
     *         name="course_id",
     *         in="path",
     *         required=true,
     *         description="ID của khóa học",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Parameter(
     *         name="rating",
     *         in="query",
     *         required=false,
     *         description="Lọc đánh giá theo số sao (rating)",
     *         @OA\Schema(type="integer", example=5, minimum=1, maximum=5)
     *     ),
     *     @OA\Parameter(
     *         name="sort_by",
     *         in="query",
     *         required=false,
     *         description="Sắp xếp đánh giá (latest: mới nhất, oldest: cũ nhất)",
     *         @OA\Schema(type="string", enum={"latest", "oldest"}, example="latest")
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         required=false,
     *         description="Số lượng đánh giá trên mỗi trang (mặc định là 10)",
     *         @OA\Schema(type="integer", example=10)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Danh sách đánh giá của khóa học",
     *         @OA\JsonContent(
     *             @OA\Property(property="course_id", type="integer", example=1),
     *             @OA\Property(property="reviews", type="object",
     *                 @OA\Property(property="current_page", type="integer", example=1),
     *                 @OA\Property(property="data", type="array",
     *                     @OA\Items(
     *                         @OA\Property(property="id", type="integer", example=10),
     *                         @OA\Property(property="user", type="object",
     *                             @OA\Property(property="id", type="integer", example=5),
     *                             @OA\Property(property="name", type="string", example="Nguyễn Văn A"),
     *                             @OA\Property(property="email", type="string", example="nguyenvana@example.com")
     *                         ),
     *                         @OA\Property(property="rating", type="integer", example=5),
     *                         @OA\Property(property="review_text", type="string", example="Khóa học rất hữu ích!"),
     *                         @OA\Property(property="created_at", type="string", format="date-time", example="2025-02-24T12:34:56Z")
     *                     )
     *                 ),
     *                 @OA\Property(property="per_page", type="integer", example=10),
     *                 @OA\Property(property="total", type="integer", example=100)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Không tìm thấy khóa học",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Không tìm thấy khóa học")
     *         )
     *     )
     * )
     */
    public function getReviewsByCourse(Request $request, $course_id)
    {
        $query = Review::where('course_id', $course_id)
            ->with('user:id,name,email');

        // Lọc theo số sao (rating)
        if ($request->has('rating')) {
            $query->where('rating', $request->rating);
        }

        // Sắp xếp theo thời gian
        $sortBy = $request->input('sort_by', 'latest'); // Mặc định là mới nhất
        if ($sortBy === 'oldest') {
            $query->orderBy('created_at', 'asc');
        } else {
            $query->orderBy('created_at', 'desc');
        }

        // Phân trang
        $perPage = $request->input('per_page', 10);
        $reviews = $query->paginate($perPage);

        return response()->json([
            'course_id' => $course_id,
            'reviews' => $reviews
        ]);
    }

    /**
     * @OA\Get(
     *     path="/users/{user_id}/reviews",
     *     summary="Lấy danh sách đánh giá của một người dùng",
     *     description="API này trả về danh sách các đánh giá mà một người dùng đã thực hiện, có thể sắp xếp theo thời gian.",
     *     tags={"Reviews"},
     *     @OA\Parameter(
     *         name="user_id",
     *         in="path",
     *         required=true,
     *         description="ID của người dùng",
     *         @OA\Schema(type="integer", example=5)
     *     ),
     *     @OA\Parameter(
     *         name="sort_by",
     *         in="query",
     *         required=false,
     *         description="Sắp xếp đánh giá (latest: mới nhất, oldest: cũ nhất)",
     *         @OA\Schema(type="string", enum={"latest", "oldest"}, example="latest")
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         required=false,
     *         description="Số lượng đánh giá trên mỗi trang (mặc định là 10)",
     *         @OA\Schema(type="integer", example=10)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Danh sách đánh giá của người dùng",
     *         @OA\JsonContent(
     *             @OA\Property(property="user_id", type="integer", example=5),
     *             @OA\Property(property="reviews", type="object",
     *                 @OA\Property(property="current_page", type="integer", example=1),
     *                 @OA\Property(property="data", type="array",
     *                     @OA\Items(
     *                         @OA\Property(property="id", type="integer", example=10),
     *                         @OA\Property(property="course", type="object",
     *                             @OA\Property(property="id", type="integer", example=2),
     *                             @OA\Property(property="title", type="string", example="Khóa học Laravel nâng cao")
     *                         ),
     *                         @OA\Property(property="rating", type="integer", example=4),
     *                         @OA\Property(property="review_text", type="string", example="Khóa học khá tốt, nội dung chi tiết."),
     *                         @OA\Property(property="created_at", type="string", format="date-time", example="2025-02-24T12:34:56Z")
     *                     )
     *                 ),
     *                 @OA\Property(property="per_page", type="integer", example=10),
     *                 @OA\Property(property="total", type="integer", example=50)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Không tìm thấy người dùng hoặc đánh giá",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Không tìm thấy đánh giá nào của người dùng này")
     *         )
     *     )
     * )
     */
    public function getReviewsByUser(Request $request, $user_id)
    {
        $query = Review::where('user_id', $user_id)
            ->with('course:id,title'); // Lấy thêm thông tin khóa học mà user đã review

        // Sắp xếp theo thời gian
        $sortBy = $request->input('sort_by', 'latest');
        if ($sortBy === 'oldest') {
            $query->orderBy('created_at', 'asc');
        } else {
            $query->orderBy('created_at', 'desc');
        }

        // Phân trang
        $perPage = $request->input('per_page', 10);
        $reviews = $query->paginate($perPage);

        return response()->json([
            'user_id' => $user_id,
            'reviews' => $reviews
        ]);
    }
}
