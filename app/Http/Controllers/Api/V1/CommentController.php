<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\Section;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    //

    public function index(Request $request, $course, $section, $lesson)
    {
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

        // Lấy danh sách tất cả bình luận trong lesson (bao gồm bình luận cha và con)
        $comments = Comment::with(['user', 'replies.user']) // Eager loading user và replies
            ->where('commentable_type', Lesson::class)
            ->where('commentable_id', $lesson)
            ->orderBy('created_at', 'asc') // Hiển thị theo thứ tự cũ -> mới
            ->get();

        return response()->json($comments);
    }



    public function store(Request $request, $course, $section, $lesson)
    {
        $validated = $request->validate([
            'content' => 'required|string',
            'parent_id' => 'nullable|exists:comments,id',
        ]);

        $user = $request->user(); // Lấy user hiện tại từ request

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

        // Kiểm tra xem user có quyền truy cập khóa học không (đã ghi danh chưa)
        $isEnrolled = Enrollment::where('user_id', $user->id)
            ->where('course_id', $course)
            ->exists();

        if (!$isEnrolled) {
            return response()->json(['message' => 'Bạn chưa ghi danh vào khóa học này!'], 403);
        }

        // Tạo comment
        $comment = Comment::create([
            'user_id' => $user->id,
            'content' => $validated['content'],
            'parent_id' => $validated['parent_id'] ?? null,
            'commentable_type' => Lesson::class,
            'commentable_id' => $lesson,
        ]);

        return response()->json(['message' => 'Bình luận đã được tạo', 'comment' => $comment], 201);
    }

    /**
     * Cập nhật bình luận
     */
    public function update(Request $request, $course, $section, $lesson, $comment)
    {
        $validated = $request->validate([
            'content' => 'required|string',
        ]);

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
        ]);

        return response()->json(['message' => 'Bình luận đã được cập nhật', 'comment' => $comment], 200);
    }


    /**
     * Xóa bình luận
     */
    public function destroy(Request $request, $course, $section, $lesson, $comment)
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
}
