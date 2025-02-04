<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('admins.dashboards.dash-board');
});
Route::get('/login', function () {
    return view('auths.login');
});

Route::resource('categories', CategoryController::class);
Route::resource('users', UserController::class);
