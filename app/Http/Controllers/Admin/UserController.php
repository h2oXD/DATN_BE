<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public $model = User::class;

    public $pathView = 'admin.users.';

    public $fieldImage = 'avatar';

    public $folderImage = 'users/avatar';
}
