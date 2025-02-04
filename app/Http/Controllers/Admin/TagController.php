<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\AdminBaseController;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TagController extends AdminBaseController
{

    public function __construct()
    {
        // Định nghĩa các thuộc tính trong controller con
        // Ví dụ: 
        $this->model = Tag::class;
        $this->viewPath = 'admins.tags';
        // $this->uploadPath = 'images/products'; 
    }

    // const PATH_VIEW = 'admins.tags.';

    // /**
    //  * Hiển thị danh sách các tags.
    //  */
    // public function index()
    // {
    //     $tags = Tag::latest('id')->paginate(5);
    //     return view(self::PATH_VIEW . __FUNCTION__, compact('tags'));
    // }

    // /**
    //  * Hiển thị form tạo mới tag.
    //  */
    // public function create()
    // {
    //     return view(self::PATH_VIEW . __FUNCTION__);
    // }

    // /**
    //  * Lưu tag mới vào database.
    //  */
    // public function store(Request $request)
    // {
    //     $data = $request->validate([
    //         'name' => 'required|max:255|unique:tags,name',
    //     ]);

    //     try {
    //         Tag::query()->create($data);

    //         return redirect()
    //             ->route('tags.index')
    //             ->with('success', true);
    //     } catch (\Throwable $th) {
    //         return back()
    //             ->with('success', false)
    //             ->with('error', $th->getMessage());
    //     }
    // }

    // /**
    //  * Hiển thị thông tin chi tiết của một tag.
    //  */
    // public function show(Tag $tag)
    // {
    //     return view(self::PATH_VIEW . __FUNCTION__, compact('tag'));
    // }

    // /**
    //  * Hiển thị form chỉnh sửa tag.
    //  */
    // public function edit(Tag $tag)
    // {
    //     return view(self::PATH_VIEW . __FUNCTION__, compact('tag'));
    // }

    // /**
    //  * Cập nhật thông tin tag.
    //  */
    // public function update(Request $request, Tag $tag)
    // {
    //     $data = $request->validate([
    //         'name' => ['required', 'max:255', Rule::unique('tags')->ignore($tag->id)],
    //     ]);

    //     try {
    //         $tag->update($data);
    //         return redirect()->route('tags.index')->with('success', 'Cập nhật tag thành công!');
    //     } catch (\Throwable $th) {
    //         return back()->with('error', $th->getMessage());
    //     }
    // }

    // /**
    //  * Xóa một tag.
    //  */
    // public function destroy(Tag $tag)
    // {
    //     try {
    //         $tag->delete();
    //         return redirect()->route('tags.index')->with('success', 'Xóa tag thành công!');
    //     } catch (\Throwable $th) {
    //         return back()->with('error', $th->getMessage());
    //     }
    // }
}