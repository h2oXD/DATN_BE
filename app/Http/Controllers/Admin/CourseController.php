<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Course;
use App\Models\Lecturer;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CourseController extends Controller
{
    const PATH_VIEW = 'admins.courses.';  

    public function index(Request $request)
    {
        $search = $request->get('search');
        $category = $request->get('category');
        $tag = $request->get('tag');
        $status = $request->get('status');
    
        $categories = Category::all();
    
        $courses = Course::with('category', 'tags', 'lecturer')
            ->when($search, function ($query, $search) {
                return $query->where('title', 'like', "%$search%")
                    ->orWhere('description', 'like', "%$search%");
            })
            ->when($category, function ($query, $category) {
                return $query->where('category_id', $category);
            })
            ->when($tag, function ($query, $tag) {
                return $query->whereHas('tags', function ($query) use ($tag) {
                    $query->where('name', 'like', "%$tag%");
                });
            })
            ->when($status, function ($query, $status) {
                return $query->where('status', $status);
            })
            ->latest('id')
            ->paginate(10);
        return view(self::PATH_VIEW . 'index', compact('courses', 'categories'));
    }
    
    public function show($id)
    {
        $course = Course::with(['category', 'tags', 'lecturer'])->findOrFail($id);
        return view(self::PATH_VIEW . 'show', compact('course'));
    }

    public function approve(Request $request, $id)
    {
        $course = Course::findOrFail($id);
        $course->status = 'published'; 
        $course->submited_at = now();  
        $course->save();

        return redirect()->route('courses.index')->with('success', 'Khóa học đã được phê duyệt');
    }
public function reject(Request $request, $id)
{
    $course = Course::findOrFail($id);
    $course->status = 'draft';  
    $course->save();
    return redirect()->route('courses.index')->with('success', 'Khóa học đã bị từ chối');
}

    public function edit($id)
    {
        $course = Course::findOrFail($id);
        $categories = Category::all();
        $tags = Tag::all();
        return view(self::PATH_VIEW . 'edit', compact('course', 'categories', 'tags'));
    }

    public function update(Request $request, $id)
    {
        $course = Course::findOrFail($id);

        $data = $request->validate([
            'title' => 'required|max:255',
            'description' => 'required',
            'category_id' => 'required|exists:categories,id',
            'status' => 'required|in:draft,published',
            'price' => 'nullable|numeric',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
            'thumbnail' => 'nullable|image|max:2048',
        ]);

        try {
            if ($request->hasFile('thumbnail')) {
                if ($course->thumbnail) {
                    Storage::delete($course->thumbnail);
                }
                $data['thumbnail'] = Storage::put('courses', $request->file('thumbnail'));
            }

            $course->update($data);
            if ($request->has('tags')) {
                $course->tags()->sync($request->input('tags'));
            }

            return redirect()->route('courses.index')->with('success', 'Cập nhật khóa học thành công!');
        } catch (\Throwable $th) {
            return back()->with('error', $th->getMessage());
        }
    }
}
