<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class PostController extends Controller
{


    /**
     * @OA\Get(
     *     path="/posts",
     *     summary="Lấy danh sách bài viết đã xuất bản",
     *     description="Trả về danh sách bài viết có trạng thái 'published', sắp xếp theo ngày tạo mới nhất và phân trang.",
     *     tags={"Posts"},
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Số trang muốn lấy (mặc định là 1)",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Số bài viết trên mỗi trang (mặc định là 10)",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lấy danh sách bài viết thành công",
     *         @OA\JsonContent(
     *             example={
     *                 "message": "Lấy danh sách thành công",
     *                 "data": {
     *                     {
     *                         "id": 1,
     *                         "user_id": 10,
     *                         "title": "Bài viết mẫu",
     *                         "slug": "bai-viet-mau",
     *                         "content": "Nội dung bài viết...",
     *                         "thumbnail": "https://example.com/image.jpg",
     *                         "status": "published",
     *                         "views": 120,
     *                         "created_at": "2025-03-14 10:00:00",
     *                         "updated_at": "2025-03-14 12:00:00"
     *                     }
     *                 },
     *                 "links": {
     *                     "first": "http://example.com/posts?page=1",
     *                     "last": "http://example.com/posts?page=5",
     *                     "prev": null,
     *                     "next": "http://example.com/posts?page=2"
     *                 },
     *                 "meta": {
     *                     "current_page": 1,
     *                     "from": 1,
     *                     "last_page": 5,
     *                     "path": "http://example.com/posts",
     *                     "per_page": 10,
     *                     "to": 10,
     *                     "total": 50
     *                 }
     *             }
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Không tìm thấy bài viết",
     *         @OA\JsonContent(
     *             example={"message": "Không tìm thấy bài viết!"}
     *         )
     *     )
     * )
     */
    public function index()
    {
        $posts = Post::where('status', 'published')
            ->orderBy('created_at', 'desc')
            ->paginate(10); // Phân trang

        if (!$posts) {
            return response()->json(['message' => 'Không tìm thấy bài viết!']);
        }

        return response()->json([
            'message' => 'Lấy danh sách thành công',
            'data' => $posts
        ], 200);
    }



    /**
     * @OA\Post(
     *     path="/posts",
     *     summary="Tạo bài viết mới",
     *     description="Tạo một bài viết mới với tiêu đề, nội dung, ảnh thumbnail (tùy chọn) và trạng thái.",
     *     tags={"Posts"},
     *     security={{ "bearerAuth":{} }},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Dữ liệu bài viết cần tạo",
     *         @OA\JsonContent(
     *             example={
     *                 "title": "Bài viết mới",
     *                 "content": "Nội dung bài viết...",
     *                 "thumbnail": "file_upload",
     *                 "status": "published"
     *             }
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Bài viết đã được tạo thành công",
     *         @OA\JsonContent(
     *             example={
     *                 "message": "Bài viết đã được tạo",
     *                 "post": {
     *                     "id": 1,
     *                     "user_id": 10,
     *                     "title": "Bài viết mới",
     *                     "slug": "bai-viet-moi",
     *                     "content": "Nội dung bài viết...",
     *                     "thumbnail": "thumbnails/image.jpg",
     *                     "status": "published",
     *                     "views": 0,
     *                     "created_at": "2025-03-14 10:00:00",
     *                     "updated_at": "2025-03-14 10:00:00"
     *                 }
     *             }
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Dữ liệu không hợp lệ",
     *         @OA\JsonContent(
     *             example={
     *                 "message": "Dữ liệu không hợp lệ",
     *                 "errors": {
     *                     "title": {"Tiêu đề đã tồn tại."},
     *                     "content": {"Nội dung là bắt buộc."},
     *                     "thumbnail": {"Ảnh không đúng định dạng."},
     *                     "status": {"Trạng thái không hợp lệ."}
     *                 }
     *             }
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Chưa xác thực",
     *         @OA\JsonContent(
     *             example={"message": "Unauthorized"}
     *         )
     *     )
     * )
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255|unique:posts,title',
            'content' => 'required|string',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => ['required', Rule::in(['draft', 'published'])],
        ]);

        // Nếu validate thất bại, trả về lỗi
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $validator->errors()
            ], 422);
        }

        $validated = $validator->validated();

        // Tạo slug duy nhất
        $slug = Str::slug($validated['title']);
        $originalSlug = $slug;
        $count = 1;
        while (Post::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $count;
            $count++;
        }

        // Xử lý upload ảnh
        $path = null;
        if ($request->hasFile('thumbnail')) {
            $path = $request->file('thumbnail')->store('thumbnails');
        }

        // Tạo bài viết
        $user = $request->user();

        $post = Post::create([
            'user_id' => $user->id,
            'title' => $validated['title'],
            'slug' => $slug,
            'content' => $validated['content'],
            'thumbnail' => $path,
            'status' => $validated['status'],
            'views' => 0,
        ]);

        return response()->json([
            'message' => 'Bài viết đã được tạo',
            'post' => $post
        ], 201);
    }


    /**
     * @OA\Get(
     *     path="/posts/{post}",
     *     summary="Lấy chi tiết bài viết",
     *     description="Lấy thông tin chi tiết của một bài viết dựa trên ID. Nếu bài viết ở trạng thái 'draft', chỉ tác giả có quyền xem.",
     *     tags={"Posts"},
     *     security={{ "bearerAuth":{} }},
     *     @OA\Parameter(
     *         name="post",
     *         in="path",
     *         required=true,
     *         description="ID của bài viết",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lấy chi tiết bài viết thành công",
     *         @OA\JsonContent(
     *             example={
     *                 "message": "Lấy chi tiết bài viết thành công",
     *                 "post": {
     *                     "id": 1,
     *                     "user_id": 10,
     *                     "title": "Tiêu đề bài viết",
     *                     "slug": "tieu-de-bai-viet",
     *                     "content": "Nội dung bài viết...",
     *                     "thumbnail": "thumbnails/image.jpg",
     *                     "status": "published",
     *                     "views": 101,
     *                     "created_at": "2025-03-14 10:00:00",
     *                     "updated_at": "2025-03-14 10:05:00"
     *                 }
     *             }
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Bạn không có quyền xem bài viết này!",
     *         @OA\JsonContent(
     *             example={"message": "Bạn không có quyền xem bài viết này!"}
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Không tìm thấy bài viết",
     *         @OA\JsonContent(
     *             example={"message": "Không tìm thấy bài viết"}
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Chưa xác thực",
     *         @OA\JsonContent(
     *             example={"message": "Unauthorized"}
     *         )
     *     )
     * )
     */
    public function show(Request $request, $id)
    {
        // Lấy bài viết từ database
        $post = Post::findOrFail($id);

        if (!$post) {
            return response()->json(['message' => 'Không tìm thấy bài viết'], 200);
        }

        // Nếu bài viết chưa xuất bản thì chỉ cho phép tác giả xem
        if ($post->status === 'draft' && $post->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Bạn không có quyền xem bài viết này!'], 403);
        }

        // Tăng lượt xem
        $post->increment('views');

        return response()->json([
            'message' => 'Lấy chi tiết bài viết thành công',
            'post' => $post
        ]);
    }


    /**
     * @OA\Put(
     *     path="/posts/{post}",
     *     summary="Cập nhật bài viết",
     *     description="Cho phép tác giả chỉnh sửa bài viết của mình. Nếu tiêu đề thay đổi, slug sẽ được cập nhật.",
     *     tags={"Posts"},
     *     security={{ "bearerAuth":{} }},
     *     @OA\Parameter(
     *         name="post",
     *         in="path",
     *         required=true,
     *         description="ID của bài viết cần cập nhật",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             example={
     *                 "title": "Tiêu đề mới của bài viết",
     *                 "content": "Nội dung cập nhật của bài viết...",
     *                 "status": "published",
     *                 "thumbnail": "image.jpg"
     *             }
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Bài viết đã được cập nhật",
     *         @OA\JsonContent(
     *             example={
     *                 "message": "Bài viết đã được cập nhật",
     *                 "post": {
     *                     "id": 1,
     *                     "user_id": 10,
     *                     "title": "Tiêu đề mới của bài viết",
     *                     "slug": "tieu-de-moi-cua-bai-viet",
     *                     "content": "Nội dung cập nhật...",
     *                     "thumbnail": "thumbnails/new-image.jpg",
     *                     "status": "published",
     *                     "views": 120,
     *                     "created_at": "2025-03-14 10:00:00",
     *                     "updated_at": "2025-03-14 12:00:00"
     *                 }
     *             }
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Bạn không có quyền chỉnh sửa bài viết này!",
     *         @OA\JsonContent(
     *             example={"message": "Bạn không có quyền chỉnh sửa bài viết này!"}
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Dữ liệu không hợp lệ",
     *         @OA\JsonContent(
     *             example={
     *                 "errors": {
     *                     "title": {"Tiêu đề đã tồn tại"},
     *                     "content": {"Nội dung là bắt buộc"},
     *                     "thumbnail": {"Tệp phải là hình ảnh"}
     *                 }
     *             }
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Không tìm thấy bài viết",
     *         @OA\JsonContent(
     *             example={"message": "Không tìm thấy bài viết"}
     *         )
     *     )
     * )
     */
    public function update(Request $request, $id)
    {
        // Lấy bài viết từ database
        $post = Post::findOrFail($id);

        // Kiểm tra quyền chỉnh sửa
        if ($post->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Bạn không có quyền chỉnh sửa bài viết này!'], 403);
        }

        // Validate dữ liệu
        $validator = Validator::make($request->all(), [
            'title' => ['required', 'string', 'max:255', Rule::unique('posts')->ignore($post->id)],
            'content' => 'required|string',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => ['required', Rule::in(['draft', 'published'])],
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $validated = $validator->validated();

        // Cập nhật slug nếu tiêu đề thay đổi
        if ($validated['title'] !== $post->title) {
            $slug = Str::slug($validated['title']);
            $originalSlug = $slug;
            $count = 1;
            while (Post::where('slug', $slug)->where('id', '!=', $post->id)->exists()) {
                $slug = $originalSlug . '-' . $count;
                $count++;
            }
            $post->slug = $slug;
        }

        // Xử lý upload ảnh theo cách mới
        if ($request->hasFile('thumbnail')) {
            try {
                // Lưu ảnh mới trước
                $newPath = Storage::put('thumbnails', $request->file('thumbnail'));

                // Nếu lưu thành công, xóa ảnh cũ (nếu có)
                if ($newPath) {
                    if ($post->thumbnail && Storage::exists($post->thumbnail)) {
                        Storage::delete($post->thumbnail);
                    }
                    $post->thumbnail = $newPath;
                }
            } catch (\Throwable $th) {
                if (!empty($newPath) && Storage::exists($newPath)) {
                    Storage::delete($newPath);
                }
            }
        }

        // Cập nhật dữ liệu bài viết
        $post->update([
            'title' => $validated['title'],
            'content' => $validated['content'],
            'status' => $validated['status'],
        ]);

        return response()->json([
            'message' => 'Bài viết đã được cập nhật',
            'post' => $post,
        ]);
    }


    /**
     * @OA\Delete(
     *     path="/posts/{post}",
     *     summary="Xóa bài viết",
     *     description="Chỉ cho phép admin hoặc chủ sở hữu bài viết thực hiện xóa.",
     *     tags={"Posts"},
     *     security={{ "bearerAuth":{} }},
     *     @OA\Parameter(
     *         name="post",
     *         in="path",
     *         required=true,
     *         description="ID của bài viết cần xóa",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Bài viết đã bị xóa",
     *         @OA\JsonContent(
     *             example={"message": "Bài viết đã bị xóa"}
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Bạn không có quyền xóa bài viết này!",
     *         @OA\JsonContent(
     *             example={"message": "Bạn không có quyền xóa bài viết này!"}
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Không tìm thấy bài viết",
     *         @OA\JsonContent(
     *             example={"message": "Không tìm thấy bài viết"}
     *         )
     *     )
     * )
     */

    public function destroy(Request $request, $id)
    {
        // Lấy bài viết từ database
        $post = Post::findOrFail($id);

        // Kiểm tra quyền xóa: chỉ admin hoặc chủ sở hữu mới được xóa
        if ($post->user_id !== $request->user()->id && !$request->user()->hasRole('admin')) {
            return response()->json(['message' => 'Bạn không có quyền xóa bài viết này!'], 403);
        }

        // Xóa ảnh thumbnail nếu có
        if ($post->thumbnail && Storage::exists($post->thumbnail)) {
            Storage::delete($post->thumbnail);
        }

        // Xóa bài viết
        $post->delete();

        return response()->json(['message' => 'Bài viết đã bị xóa']);
    }
}
