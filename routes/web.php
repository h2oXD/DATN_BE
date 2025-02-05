<?php


use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\TagController;
<<<<<<< HEAD
use App\Http\Controllers\Admin\VoucherController;
=======
use App\Models\User;
>>>>>>> 1670e3ab5543719869537f779dd1feb23419c9d5
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

Route::get('/', function () {;
    return view('admins.dashboards.dash-board');
});
Route::get('/login', function () {
    return view('auths.login');
});

<<<<<<< HEAD
Route::resource('tags', TagController::class);

Route::resource('vouchers', VoucherController::class);
=======
Route::resource('categories', CategoryController::class);
Route::resource('users', UserController::class);
Route::resource('tags', TagController::class);

>>>>>>> 1670e3ab5543719869537f779dd1feb23419c9d5
