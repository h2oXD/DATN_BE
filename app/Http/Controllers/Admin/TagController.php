<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\AdminBaseController;
use App\Models\Course;
use App\Models\CourseTag;
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
        $this->viewPath = 'admins.tags.';
        $this->routePath = 'admin.tags.index';
        // $this->uploadPath = 'images/products'; 
    }
    protected function storeValidate()
    {
        return [
            'name' => ['required', 'string', 'max:255', Rule::unique('tags')],
            'slug' => ['string', Rule::unique('tags')]
        ];
    }

    public function storeMessage()
    {
        return [
            'name.required' => 'Tên thẻ không được để trống',
            'name.unique' => 'Tên thẻ đã tồn tại'
        ];
    }

    protected function updateValidate($id)
    {
        return [
            'name' => ['required', 'string', 'max:255', Rule::unique('tags')->ignore($id)],
            'slug' => ['string', Rule::unique('tags')->ignore($id)]
        ];
    }
    public function updateMessage()
    {
        return [];
    }

    public function forceDelete($id)
    {
        $tag = Tag::onlyTrashed()->with('courses')->findOrFail($id);

        // Kiểm tra nếu tag có khóa học liên quan
        if ($tag->courses->count() > 0) {
            return redirect()->route('admin.tags.trash')->with('error', 'Không thể xóa! Vui lòng xóa các khóa học chứa tag này trước.');
        }

        $tag->forceDelete();
        return redirect()->route('admin.tags.trash')->with('success', 'Tag đã bị xóa vĩnh viễn.');
    }

    public function trash()
    {
        $trashedTags = Tag::onlyTrashed()->with('courses')->get();

        return view('admins.tags.trash', compact('trashedTags'));
    }


    public function restore($id)
    {
        $tag = Tag::onlyTrashed()->findOrFail($id);
        $tag->restore();

        return redirect()->route('admin.tags.index')->with('success', 'Tag đã được khôi phục thành công!');
    }
}
