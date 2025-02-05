<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\AdminBaseController;
use App\Models\Tag;
use Illuminate\Http\Request;

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
}
