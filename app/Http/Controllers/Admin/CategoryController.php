<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    //
    protected const VIEW_PATH = 'admins.categories.';

    public function index()
    {
        $categories = Category::with('children')->whereNull('parent_id')->get();
        return view(self::VIEW_PATH  . __FUNCTION__, compact('categories'));
    }

    // Form thêm danh mục
    public function create()
    {
        $categories = Category::whereNull('parent_id')->get();;
        return view(self::VIEW_PATH  . __FUNCTION__, compact('categories'));
    }

    // Xử lý thêm danh mục
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:categories,id',
        ]);

        if ($validated['parent_id']) {
            $parentCategory = Category::find($validated['parent_id']);
            if ($parentCategory && $parentCategory->parent_id !== null) {
                return back()->withErrors(['parent_id' => 'Danh mục con không thể có danh mục con khác!']);
            }
        }

        Category::create($validated);
        return back()->with('success', 'Thêm mới thành công!');
    }

    // Hiển thị chi tiết danh mục
    public function show($id)
    {
        $category = Category::with('children')->findOrFail($id);
        return view(self::VIEW_PATH  . __FUNCTION__, compact('category'));
    }

    // Form cập nhật danh mục
    public function edit($id)
    {
        $category = Category::findOrFail($id);
        $categories = Category::whereNull('parent_id')->where('id', '!=', $id)->get();
        return view(self::VIEW_PATH  . __FUNCTION__, compact('category', 'categories'));
    }

    // Xử lý cập nhật danh mục
    public function update(Request $request, $id)
    {
        $category = Category::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:categories,id',
        ]);

        $category->update($validated);
        return back()->with('success', 'Cập nhật thành công!');
    }

    // Xóa danh mục
    public function destroy($id)
    {
        $category = Category::findOrFail($id);

        if ($category->parent_id === null) {
            // Nếu là danh mục cha, xóa luôn danh mục con
            Category::where('parent_id', $category->id)->delete();
        }

        $category->delete();
        return back()->with('success', 'Xóa thành công!');
    }
}
