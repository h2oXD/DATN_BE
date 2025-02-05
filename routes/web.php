<?php


use App\Http\Controllers\Admin\UserController;

use App\Http\Controllers\Admin\TagController;
use App\Http\Controllers\Admin\VoucherController;
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
Route::resource('users', UserController::class);

Route::resource('tags', TagController::class);

Route::resource('vouchers', VoucherController::class);