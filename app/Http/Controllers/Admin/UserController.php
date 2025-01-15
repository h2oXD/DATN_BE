<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\AdminBaseController;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends AdminBaseController
{
    public $model = User::class;

    public $pathView = 'admin.base.';
    public $urlbase = 'users.';

    public $fieldImage = 'avatar';

    public $folderImage = 'users/avatar';
    public $columns = [
        'email' => 'Email',
        'name' => 'Tên',
        'phone_number' => 'Số điện thoại',
        'profile_picture' => 'Ảnh đại diện',
        'bio' => 'Mô tả',
        'google_id' => 'google id',
    ];
}
