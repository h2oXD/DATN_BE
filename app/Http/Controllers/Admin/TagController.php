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
        $this->viewPath = 'admins.tags.';
        $this->routePath = 'tags.index';
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
        return [

        ];
    }
}
