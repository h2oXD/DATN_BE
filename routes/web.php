<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CourseController;
use App\Http\Controllers\Admin\DashBoardController;
use App\Http\Controllers\Admin\LecturerRegisterController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\TagController;
use App\Http\Controllers\Admin\VoucherController;
use App\Http\Controllers\Admin\VoucherUseController;
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

Route::get('/', [DashBoardController::class, 'index'])->name('admin.home');
Route::get('login', [AuthController::class, 'showLoginForm'])->name('admin.login');
Route::post('login', [AuthController::class, 'login'])->name('login');


Route::middleware(['auth', 'admin'])->group(function (): void {
    Route::prefix('admin')->name('admin.')->group(function () {
        //Dashboard
        Route::get('/', [DashBoardController::class, 'dashboard'])->name('dashboard');
        Route::post('logout', [AuthController::class, 'logout'])->name('admin.logout');

        //Voucher
        Route::resource('vouchers', VoucherController::class);
        Route::resource('voucher-use', VoucherUseController::class);

        //Categories
        Route::get('categories/trashed', [CategoryController::class, 'trashed'])->name('categories.trashed');
        Route::delete('categories/{id}/force-delete', [CategoryController::class, 'forceDelete'])
            ->name('categories.forceDelete');
        Route::resource('categories', CategoryController::class)->names('categories');
        Route::post('categories/{id}/restore', [CategoryController::class, 'restore'])->name('categories.restore');

        // User
        Route::prefix('users')->group(function () {
            Route::get('lecturers', [UserController::class, 'indexLecturers'])->name('lecturers.index');
            Route::get('students', [UserController::class, 'indexStudents'])->name('students.index');
            Route::get('/create', [UserController::class, 'create'])->name('users.create');
            Route::post('/', [UserController::class, 'store'])->name('users.store');
            Route::get('/{user}', [UserController::class, 'show'])->name('users.show');
            Route::get('/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
            Route::put('/{user}', [UserController::class, 'update'])->name('users.update');
            Route::delete('/{user}', [UserController::class, 'destroy'])->name('users.destroy');
        });

        // Tag
        Route::resource('tags', TagController::class);
        Route::delete('/forceDelete/{id}', [TagController::class, 'forceDelete'])->name('tags.forceDelete');
        Route::get('/trash', [TagController::class, 'trash'])->name('tags.trash');
        Route::post('/restore/{id}', [TagController::class, 'restore'])->name('tags.restore');

        //Course
        // Route::resource('courses', CourseController::class);
        Route::get('courses/{course_id}/censor', [CourseController::class, 'checkCourse'])->name('check.course');
        Route::post('courses/{id}/approve', [CourseController::class, 'approve'])->name('courses.approve');
        Route::post('courses/{id}/reject', [CourseController::class, 'reject'])->name('courses.reject');
        Route::get('courses', [CourseController::class, 'index'])->name('courses.index');
        Route::get('courses/censor', [CourseController::class, 'censorCourseList'])->name('censor.courses.list');

        Route::resource('lecturer_registers', LecturerRegisterController::class);
        
    });
});







