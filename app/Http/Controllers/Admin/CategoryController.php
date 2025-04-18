<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;


class CategoryController extends Controller
{
    //
    protected const VIEW_PATH = 'admins.categories.';

    public function index(Request $request)
    {
        $search = $request->get('search');
        $selectedCategory = $request->get('category');

        $parentCategory = Category::whereNull('parent_id')
            ->orderBy('name', 'asc')
            ->get();

        $categories = Category::with('children')
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%$search%")
                        ->orWhere('slug', 'like', "%$search%");
                })
                    ->orWhereHas('children', function ($q) use ($search) {
                        $q->where('name', 'like', "%$search%")
                            ->orWhere('slug', 'like', "%$search%");
                    });
            })
            ->when($selectedCategory, function ($query, $selectedCategory) {
                $query->where(function ($q) use ($selectedCategory) {
                    $q->where('id', $selectedCategory)
                        ->orWhere('parent_id', $selectedCategory);
                });
            })
            ->orderBy('name', 'asc')
            ->get();

        $noResults = $categories->isEmpty();



        return view(self::VIEW_PATH  . __FUNCTION__, compact('categories', 'parentCategory', 'noResults'));
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
        $validated = $request->validate(
            [
                'name' => ['required', 'string', 'max:255'],
                'parent_id' => ['nullable', Rule::exists('categories', 'id')],
            ],

            [
                'name.required' => 'Phải điền tên cho danh mục',
                'name.max'      => 'Tối đa là 255 kí tự'
            ]
        );


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
        while (Category::withTrashed()->where('slug', $slug)->exists()) {
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

        $validated = $request->validate(
            [
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
            ],
            [
                'name.required' => 'Phải điền tên cho danh mục',
                'name.max'      => 'Tối đa là 255 kí tự'

            ]
        );

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
        // Lấy danh mục cha đã bị xóa (nếu có)
        $trashedParents = Category::onlyTrashed()->whereNull('parent_id')->with(['children' => function ($query) {
            $query->onlyTrashed(); // Lấy danh mục con bị xóa của danh mục cha đã bị xóa
        }])->get();

        // Lấy danh mục con bị xóa nhưng có danh mục cha chưa bị xóa
        $trashedChildren = Category::onlyTrashed()->whereNotNull('parent_id')->get();

        return view('admins.categories.trashed', compact('trashedParents', 'trashedChildren'));
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

        // Nếu danh mục là danh mục con nhưng danh mục cha vẫn bị xóa
        if ($category->parent_id) {
            $parent = Category::onlyTrashed()->find($category->parent_id);
            if ($parent) {
                return back()->with('error', 'Vui lòng khôi phục danh mục cha trước!');
            }
        }

        // Khôi phục danh mục cha và tất cả danh mục con
        $category->restore();


        return back()->with('success', 'Danh mục đã được khôi phục!');
    }
}
