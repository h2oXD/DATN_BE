<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Tag;
use Illuminate\Http\Request;

class TagController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Nếu có course_id, lấy tag thuộc khóa học đó
        if ($request->has('course_id')) {
            $course = Course::find($request->course_id);
            if (!$course) {
                return response()->json(['message' => 'Không tìm thấy khóa học'], 404);
            }

            $tags = $course->tags()->get();
            if ($tags->isEmpty()) {
                return response()->json(['message' => 'Khóa học không có tag nào'], 200);
            }

            return response()->json($tags);
        }

        // Lấy tất cả tag nếu không lọc theo khóa học
        $tags = Tag::all();

        if ($tags->isEmpty()) {
            return response()->json(['message' => 'Không có tag nào'], 200);
        }

        return response()->json($tags);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:tags,name',
            'course_id' => 'nullable|exists:courses,id', // Kiểm tra khóa học có tồn tại không (nếu có)
        ]);

        // Kiểm tra nếu tag đã tồn tại
        if (Tag::where('name', $request->name)->exists()) {
            return response()->json(['message' => 'Tag đã tồn tại'], 400);
        }

        // Tạo tag mới
        $tag = Tag::create(['name' => $request->name]);

        // Nếu có course_id, gắn tag vào khóa học
        if ($request->filled('course_id')) {
            $course = Course::find($request->course_id);
            $course->tags()->attach($tag->id);
        }

        return response()->json(['message' => 'Tạo tag thành công', 'data' => $tag], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $tag = Tag::find($id);
        if (!$tag) {
            return response()->json(['message' => 'Không tìm thấy tag'], 404);
        }
        return response()->json($tag);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $tag = Tag::find($id);
        if (!$tag) {
            return response()->json(['message' => 'Không tìm thấy tag'], 404);
        }

        $request->validate([
            'name' => 'required|string|max:255|unique:tags,name,' . $id,
        ]);

        $tag->update(['name' => $request->name]);
        return response()->json(['message' => 'Cập nhật tag thành công', 'data' => $tag]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $tag = Tag::find($id);
        if (!$tag) {
            return response()->json(['message' => 'Không tìm thấy tag'], 404);
        }

        // Kiểm tra xem có khóa học nào đang sử dụng tag này không
        if ($tag->courses()->exists()) {
            return response()->json(['message' => 'Không thể xóa tag vì có khóa học đang sử dụng nó'], 400);
        }

        $tag->delete();
        return response()->json(['message' => 'Xóa tag thành công']);
    }
}
