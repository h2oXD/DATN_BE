<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DeepSeekController;
use App\Http\Controllers\Api\V1\CategoryController;
use App\Http\Controllers\Api\V1\CourseController;
use App\Http\Controllers\Api\V1\LecturerController;
use App\Http\Controllers\Api\V1\UserController;
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
    //wallet in user
    Route::get('/user/wallet',[UserController::class ,'show']);
    Route::put('/user/wallet/{wallet_id}',[UserController::class ,'update']);

    Route::post('/logout', [AuthController::class, 'logout']);
});
Route::group(['middleware' => ['auth:sanctum', 'role:lecturer']], function () {
    Route::get('/lecturer/dashboard', [LecturerController::class, 'dashboard']);
    Route::get('/lecturer', [LecturerController::class, 'getLecturerInfo']);

    //course in lecturer
    Route::get('/lecturer/courses', [CourseController::class, 'getLecturerCourse']);  //Lấy danh sách khóa học
    Route::post('/lecturer/courses', [CourseController::class, 'createLecturerCourse']); //Tạo mới khoá học
    Route::get('/lecturer/courses/{course_id}', [CourseController::class, 'showLecturerCourse']); //Lấy chi tiết khóa học
    Route::put('/lecturer/courses/{course_id}', [CourseController::class, 'updateLecturerCourse']); //Cập nhật khóa học 
    Route::delete('/lecturer/courses/{course_id}', [CourseController::class, 'destroyLecturerCourse']); //Xoá khoá học

    //section in course hieu
    Route::post('/lecturer/courses/{course_id}/sections', [CourseController::class, 'createSection']); //Tạo mới section trong khoá học
    Route::put('/lecturer/courses/{course_id}/sections/{section_id}', [CourseController::class, 'updateSection']); //Cập nhật section trong khóa học 
    Route::delete('/lecturer/courses/{course_id}/sections/{section_id}', [CourseController::class, 'destroySection']); //Xoá section trong khoá học

    //lesson in section hieu
    Route::post('/lecturer/courses/{course_id}/sections/{section_id}/lessons', [CourseController::class, 'createLesson']); //Tạo mới lesson trong section
    Route::put('/lecturer/courses/{course_id}/sections/{section_id}/lessons/{lesson_id}', [CourseController::class, 'updateLesson']); //Cập nhật lesson trong section 
    Route::delete('/lecturer/courses/{course_id}/sections/{section_id}/lessons/{lesson_id}', [CourseController::class, 'destroyLesson']); //Xoá lesson trong section

    //video in lesson thuyet
    Route::post('/lecturer/courses/{course_id}/sections/lessons/videos', [CourseController::class, 'createVideo']); //Tạo mới section trong khoá học
    Route::put('/lecturer/courses/{course_id}/sections/{section_id}/lessons/{lesson_id}/videos/{video_id}', [CourseController::class, 'updateVideo']); //Cập nhật section trong khóa học 
    Route::delete('/lecturer/courses/{course_id}/sections/{section_id}/lessons/{lesson_id}/videos/{video_id}', [CourseController::class, 'destroyVideo']); //Xoá section trong khoá học

    //document in lesson tai
    Route::post('/lecturer/courses/{course_id}/sections/lessons/documents', [CourseController::class, 'createDocument']); //Tạo mới section trong khoá học
    Route::put('/lecturer/courses/{course_id}/sections/{section_id}/lessons/{lesson_id}/documents/{document_id}', [CourseController::class, 'updateDocument']); //Cập nhật section trong khóa học 
    Route::delete('/lecturer/courses/{course_id}/sections/{section_id}/lessons/{lesson_id}/documents/{document_id}', [CourseController::class, 'destroyDocument']); //Xoá section trong khoá học

    //coding in lesson tu
    Route::post('/lecturer/courses/{course_id}/sections/lessons/codings', [CourseController::class, 'createVideo']); //Tạo mới section trong khoá học
    Route::put('/lecturer/courses/{course_id}/sections/{section_id}/lessons/{lesson_id}/codings/{coding_id}', [CourseController::class, 'updateCoding']); //Cập nhật section trong khóa học 
    Route::delete('/lecturer/courses/{course_id}/sections/{section_id}/lessons/{lesson_id}/codings/{coding_id}', [CourseController::class, 'destroyCoding']); //Xoá section trong khoá học

    //quiz in lesson tu
    Route::post('/lecturer/courses/{course_id}/sections/lessons/quizzes', [CourseController::class, 'createQuiz']); //Tạo mới section trong khoá học
    Route::put('/lecturer/courses/{course_id}/sections/{section_id}/lessons/{lesson_id}/quizzes/{quiz_id}', [CourseController::class, 'updateQuiz']); //Cập nhật section trong khóa học 
    Route::delete('/lecturer/courses/{course_id}/sections/{section_id}/lessons/{lesson_id}/quizzes/{quiz_id}', [CourseController::class, 'destroyQuiz']); //Xoá section trong khoá học


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