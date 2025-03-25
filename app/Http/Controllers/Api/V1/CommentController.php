<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\Post;
use App\Models\Section;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CommentController extends Controller
{
    //

    public function getLessonComment(Request $request, $lesson)
    {
        // Lấy danh sách tất cả bình luận trong lesson (bao gồm bình luận cha và con)
        $comments = Comment::with(['user', 'replies.user']) // Eager loading user và replies
            ->where('commentable_type', Lesson::class)
            ->where('commentable_id', $lesson)
            ->where('parent_id', null)
            ->orderBy('created_at', 'desc') // Hiển thị theo thứ tự cũ -> mới
            ->get();

        return response()->json($comments);
    }



    public function storeLessonComment(Request $request, $lesson)
    {
        $validator = Validator::make($request->all(), [
            'content' => 'required|string',
            'parent_id' => 'nullable|exists:comments,id',
        ]);

        // Kiểm tra nếu validation thất bại
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $validated = $validator->validated();
        $user = $request->user(); // Lấy user hiện tại từ request

        // Tạo comment
        $comment = Comment::create([
            'user_id' => $user->id,
            'content' => $validated['content'],
            'parent_id' => $validated['parent_id'] ?? null,
            'commentable_type' => Lesson::class,
            'commentable_id' => $lesson,
        ]);

        return response()->json([
            'message' => 'Bình luận đã được tạo',
            'comment' => $comment
        ], 201);
    }


    /**
     * Cập nhật bình luận
     */
    public function updateLessonComment(Request $request, $course, $section, $lesson, $comment)
    {
        $validator = Validator::make($request->all(), [
            'content' => 'required|string',
            'parent_id' => 'nullable|exists:comments,id',
        ]);

        // Kiểm tra nếu validation thất bại
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Dữ liệu không hợp lệ!',
                'errors' => $validator->errors()
            ], 422);
        }

        $validated = $validator->validated();
        $user = $request->user();

        // Kiểm tra xem lesson có thuộc section không
        $lessonExists = Lesson::where('id', $lesson)
            ->where('section_id', $section)
            ->exists();

        if (!$lessonExists) {
            return response()->json(['message' => 'Bài học không thuộc phần học này!'], 400);
        }

        // Kiểm tra xem section có thuộc course không
        $sectionExists = Section::where('id', $section)
            ->where('course_id', $course)
            ->exists();

        if (!$sectionExists) {
            return response()->json(['message' => 'Phần học không thuộc khóa học này!'], 400);
        }

        // Lấy comment cần cập nhật
        $comment = Comment::where('id', $comment)
            ->where('commentable_type', Lesson::class)
            ->where('commentable_id', $lesson)
            ->first();

        if (!$comment) {
            return response()->json(['message' => 'Bình luận không tồn tại!'], 404);
        }

        // Kiểm tra quyền sửa bình luận (chỉ chủ sở hữu mới có thể chỉnh sửa)
        if ($comment->user_id !== $user->id) {
            return response()->json(['message' => 'Bạn không có quyền chỉnh sửa bình luận này!'], 403);
        }

        // Cập nhật nội dung bình luận
        $comment->update([
            'content' => $validated['content'],
            'parent_id' => $validated['parent_id'] ?? $comment->parent_id, // Giữ nguyên nếu không được cập nhật
        ]);

        return response()->json([
            'message' => 'Bình luận đã được cập nhật',
            'comment' => $comment
        ], 200);
    }


    /**
     * Xóa bình luận
     */
    public function destroyLessonComment(Request $request, $course, $section, $lesson, $comment)
    {
        $user = $request->user();

        // Kiểm tra xem lesson có thuộc section không
        $lessonExists = Lesson::where('id', $lesson)
            ->where('section_id', $section)
            ->exists();

        if (!$lessonExists) {
            return response()->json(['message' => 'Bài học không thuộc phần học này!'], 400);
        }

        // Kiểm tra xem section có thuộc course không
        $sectionExists = Section::where('id', $section)
            ->where('course_id', $course)
            ->exists();

        if (!$sectionExists) {
            return response()->json(['message' => 'Phần học không thuộc khóa học này!'], 400);
        }

        // Lấy comment cần xóa
        $comment = Comment::where('id', $comment)
            ->where('commentable_type', Lesson::class)
            ->where('commentable_id', $lesson)
            ->first();

        if (!$comment) {
            return response()->json(['message' => 'Bình luận không tồn tại!'], 404);
        }

        // Kiểm tra quyền xóa bình luận (chỉ chủ sở hữu hoặc admin mới có thể xóa)
        if ($comment->user_id !== $user->id && !$user->hasRole('admin')) {
            return response()->json(['message' => 'Bạn không có quyền xóa bình luận này!'], 403);
        }

        // Xóa bình luận

        if ($comment->replies()->exists()) {
            $comment->replies()->delete();
        }
        $comment->delete();

        return response()->json(['message' => 'Bình luận đã được xóa'], 200);
    }



    //Post Comment


    public function getPostComments($post_id)
    {
        // Kiểm tra bài viết có tồn tại không
        $post = Post::find($post_id);
        if (!$post) {
            return response()->json(['message' => 'Bài viết không tồn tại!'], 404);
        }

        // Lấy danh sách comment của bài post cụ thể
        $comments = Comment::where('commentable_id', $post_id)
            ->where('commentable_type', Post::class)
            ->with('user') // Eager Loading để lấy thông tin người dùng bình luận
            ->orderBy('created_at', 'desc')
            ->get();

        // Nếu không có bình luận nào
        if ($comments->isEmpty()) {
            return response()->json(['message' => 'Bài viết này chưa có bình luận nào!'], 200);
        }

        return response()->json([
            'message' => 'Danh sách bình luận của bài viết',
            'comments' => $comments
        ]);
    }


    public function storePostComment(Request $request, $post_id)
    {
        // Validate dữ liệu đầu vào
        $validator = Validator::make($request->all(), [
            'content' => 'required|string|max:500',
            'parent_id' => 'nullable|exists:comments,id' // Kiểm tra nếu có parent_id thì phải tồn tại
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Kiểm tra bài viết có tồn tại không
        $post = Post::findOrFail($post_id);

        // Tạo comment mới
        $comment = Comment::create([
            'user_id' => $request->user()->id,
            'content' => $request->input('content'),
            'commentable_id' => $post->id,
            'commentable_type' => Post::class,
            'parent_id' => $request->input('parent_id', null) // Nếu không có thì là null
        ]);

        return response()->json([
            'message' => 'Bình luận đã được thêm!',
            'comment' => $comment
        ], 201);
    }


    public function updatePostComment(Request $request, $post_id, $comment_id)
    {
        // Kiểm tra bài viết có tồn tại không
        $post = Post::find($post_id);
        if (!$post) {
            return response()->json(['message' => 'Bài viết không tồn tại!'], 404);
        }

        // Kiểm tra comment có thuộc bài viết này không
        $comment = Comment::where('id', $comment_id)
            ->where('commentable_id', $post_id)
            ->where('commentable_type', Post::class)
            ->first();

        if (!$comment) {
            return response()->json(['message' => 'Bình luận không thuộc bài viết này!'], 404);
        }

        // Kiểm tra nếu user hiện tại có phải chủ sở hữu bình luận không
        if ($comment->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Bạn không có quyền chỉnh sửa bình luận này!'], 403);
        }

        // Validate dữ liệu đầu vào
        $validator = Validator::make($request->all(), [
            'content' => 'required|string',
            'parent_id' => 'nullable|exists:comments,id' // parent_id nếu có thì phải tồn tại
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Kiểm tra nếu có parent_id thì parent đó phải thuộc cùng bài viết
        if ($request->has('parent_id') && $request->parent_id != null) {
            $parentComment = Comment::where('id', $request->parent_id)
                ->where('commentable_id', $post_id)
                ->where('commentable_type', Post::class)
                ->first();

            if (!$parentComment) {
                return response()->json(['message' => 'Bình luận cha không hợp lệ!'], 400);
            }
        }

        // Cập nhật bình luận
        $comment->content = $request->content;
        $comment->parent_id = $request->input('parent_id', null); // Nếu không có thì là null
        $comment->save();

        return response()->json([
            'message' => 'Bình luận đã được cập nhật!',
            'comment' => $comment
        ]);
    }



    public function destroyPostComment(Request $request, $post_id, $comment_id)
    {
        // Kiểm tra bài viết có tồn tại không
        $post = Post::find($post_id);
        if (!$post) {
            return response()->json(['message' => 'Bài viết không tồn tại!'], 404);
        }

        // Kiểm tra comment có thuộc bài viết này không
        $comment = Comment::where('id', $comment_id)
            ->where('commentable_id', $post_id)
            ->where('commentable_type', Post::class)
            ->first();

        if (!$comment) {
            return response()->json(['message' => 'Bình luận không thuộc bài viết này!'], 404);
        }

        // Lấy thông tin user hiện tại
        $user = $request->user();

        // Kiểm tra quyền xóa: chỉ chủ sở hữu hoặc admin mới có thể xóa
        if ($comment->user_id !== $user->id && !$user->hasRole('admin')) {
            return response()->json(['message' => 'Bạn không có quyền xóa bình luận này!'], 403);
        }

        // Xóa bình luận
        $comment->delete();

        return response()->json(['message' => 'Bình luận đã được xóa thành công!'], 200);
    }
}
