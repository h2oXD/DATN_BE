<?php

use App\Http\Controllers\Admin\StatisticsController;

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CourseController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\TagController;
use App\Http\Controllers\Admin\VoucherController;
use App\Http\Controllers\Admin\VoucherUseController;
use App\Http\Controllers\Api\V1\CategoryController as V1CategoryController;
use App\Models\User;
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
    ;
    return view('admins.dashboards.dash-board');
});
Route::get('login', function () {
    return view('auths.login');
});

Route::resource('tags', TagController::class);

Route::get('/admin/statistics/total-revenue', [StatisticsController::class, 'totalRevenue']);
Route::get('/admin/statistics/revenue-by-course', [StatisticsController::class, 'revenueByCourse']);
Route::get('/admin/statistics/revenue-by-lecturer', [StatisticsController::class, 'revenueByLecturer']);
Route::get('/admin/statistics/revenue-by-time', [StatisticsController::class, 'revenueByTime']);
Route::get('/admin/statistics/count-stats', [StatisticsController::class, 'countStats']);

Route::get('/admin/dashboards/statistics', [StatisticsController::class, 'index']);

Route::resource('admin/vouchers', VoucherController::class);
Route::resource('admin/voucher-use', VoucherUseController::class);


Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('categories/trashed', [CategoryController::class, 'trashed'])->name('categories.trashed');
    Route::delete('categories/{id}/force-delete', [CategoryController::class, 'forceDelete'])
        ->name('categories.forceDelete');
    Route::resource('categories', CategoryController::class)->names('categories');
    Route::post('categories/{id}/restore', [CategoryController::class, 'restore'])->name('categories.restore');
});


Route::prefix('users')->group(function () {
    Route::get('/lecturers', [UserController::class, 'indexLecturers'])->name('lecturers.index');
    Route::get('/students', [UserController::class, 'indexStudents'])->name('students.index');
    Route::get('/create', [UserController::class, 'create'])->name('users.create');
    Route::post('/', [UserController::class, 'store'])->name('users.store');
    Route::get('/{user}', [UserController::class, 'show'])->name('users.show');
    Route::get('/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/{user}', [UserController::class, 'destroy'])->name('users.destroy');
});


Route::resource('tags', TagController::class);

Route::resource('courses', CourseController::class);
Route::post('courses/{id}/approve', [CourseController::class, 'approve'])->name('courses.approve');
Route::post('courses/{id}/reject', [CourseController::class, 'reject'])->name('courses.reject');
Route::get('admin/courses', [CourseController::class, 'censorCourseList'])->name('censor.corse.list');
