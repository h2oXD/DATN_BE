<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;


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

        // Đảm bảo parent_id luôn hợp lệ
        $validated['parent_id'] = $validated['parent_id'] ?? null;

        // Kiểm tra danh mục con có danh mục con khác hay không
        if ($validated['parent_id']) {
            $parentCategory = Category::find($validated['parent_id']);
            if ($parentCategory && $parentCategory->parent_id !== null) {
                return back()->withErrors(['parent_id' => 'Danh mục con không thể có danh mục con khác!']);
            }
        }

        // Tạo slug từ name
        $slug = Str::slug($validated['name']);

        // Đảm bảo slug là duy nhất
        $originalSlug = $slug;
        $count = 1;
        while (Category::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $count;
            $count++;
        }

        // Gán slug vào dữ liệu lưu vào database
        $validated['slug'] = $slug;

        // Tạo danh mục
        Category::create([
            'name' => $validated['name'],
            'slug' => $validated['slug'], // Đảm bảo slug luôn có giá trị
            'parent_id' => $validated['parent_id'],
        ]);

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

        // Xác định danh mục có phải là danh mục cha không
        $isParentCategory = $category->parent_id === null;

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => [
                'nullable',
                'exists:categories,id',
                function ($attribute, $value, $fail) use ($isParentCategory) {
                    if ($isParentCategory && $value !== null) {
                        $fail('Danh mục cha không thể có danh mục cha khác.');
                    }
                },
            ],
        ]);

        // Tạo slug từ name nếu tên bị thay đổi
        if ($category->name !== $validated['name']) {
            $slug = Str::slug($validated['name']);

            // Đảm bảo slug là duy nhất
            $count = Category::where('slug', 'LIKE', "$slug%")->where('id', '!=', $id)->count();
            if ($count > 0) {
                $slug .= '-' . ($count + 1);
            }

            $validated['slug'] = $slug;
        }

        // Nếu là danh mục cha thì chỉ cập nhật tên và slug, không thay đổi parent_id
        if ($isParentCategory) {
            $category->update([
                'name' => $validated['name'],
                'slug' => $validated['slug'] ?? $category->slug,
            ]);
        } else {
            $category->update($validated);
        }

        return back()->with('success', 'Cập nhật thành công!');
    }


    // Xóa danh mục
    public function destroy($id)
    {
        $category = Category::findOrFail($id);

        try {
            if ($category->parent_id === null) {
                // Nếu là danh mục cha, xoá mềm tất cả danh mục con
                Category::where('parent_id', $category->id)->delete();
            }

            $category->delete(); // Xoá mềm danh mục hiện tại

            return back()->with('success', 'Danh mục đã được đưa vào thùng rác!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Xoá danh mục thất bại!']);
        }
    }
    

    public function trashed()
    {
        $categories = Category::onlyTrashed()->get();
        return view(self::VIEW_PATH . __FUNCTION__, compact('categories'));
    }

    public function forceDelete($id)
    {
        $category = Category::withTrashed()->findOrFail($id);

        try {
            if ($category->parent_id === null) {
                // Nếu là danh mục cha, xoá vĩnh viễn tất cả danh mục con
                Category::where('parent_id', $category->id)->forceDelete();
            }

            $category->forceDelete(); // Xoá vĩnh viễn danh mục hiện tại

            return back()->with('success', 'Danh mục đã bị xoá vĩnh viễn!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Xoá danh mục thất bại!']);
        }
    }

   

    public function restore($id)
    {
        $category = Category::onlyTrashed()->findOrFail($id);
        $category->restore();

        return back()->with('success', 'Danh mục đã được khôi phục!');
    }
}
