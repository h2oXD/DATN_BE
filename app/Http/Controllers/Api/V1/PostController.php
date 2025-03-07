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
