<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DeepSeekController;
use App\Http\Controllers\Api\V1\CategoryController;
use App\Http\Controllers\Api\V1\CourseController;
use App\Http\Controllers\Api\V1\LecturerController;
use App\Models\UserRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::get('/user', [AuthController::class, 'getUser']);
    Route::post('/logout', [AuthController::class, 'logout']);
});
Route::group(['middleware' => ['auth:sanctum', 'role:lecturer']], function () {
    Route::get('/lecturer/dashboard', [LecturerController::class, 'dashboard']);
    Route::get('/lecturer', [LecturerController::class, 'getLecturerInfo']);

    Route::get('/lecturer/courses', [LecturerController::class, 'getLecturerCourse']);  //Lấy danh sách khóa học
    Route::post('/lecturer/courses', [LecturerController::class, 'createLecturerCourse']); //Tạo mới khoá học
    Route::get('/lecturer/courses/{course_id}', [LecturerController::class, 'showLecturerCourse']); //Lấy chi tiết khóa học
    Route::put('/lecturer/courses/{course_id}', [LecturerController::class, 'updateLecturerCourse']); //Cập nhật khóa học 
    Route::delete('/lecturer/courses/{course_id}', [LecturerController::class, 'destroyLecturerCourse']); //Xoá khoá học

    
});
Route::group(['middleware' => ['auth:sanctum', 'role:student']], function () {
    Route::get('/student/dashboard', function (Request $request) {
        return response()->json(['message' => 'Chào mừng Học viên']);
    });
});
Route::group(['middleware' => ['auth:sanctum', 'role:admin']], function () {
    Route::get('/admin/dashboard', function (Request $request) {
        return response()->json(['message' => 'Chào mừng Quản trị viên']);
    });
});

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/categories', [CategoryController::class, 'index']);