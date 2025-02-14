<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\V1\CategoryController;
use App\Http\Controllers\Api\V1\CourseController;
use App\Http\Controllers\Api\V1\DocumentController;
use App\Http\Controllers\Api\V1\LecturerController;
use App\Http\Controllers\Api\V1\LessonCodingController;
use App\Http\Controllers\Api\V1\LessonController;
use App\Http\Controllers\Api\V1\QuizController;
use App\Http\Controllers\Api\V1\SectionController;
use App\Http\Controllers\Api\V1\TagController;
use App\Http\Controllers\Api\V1\UserController;
use App\Http\Controllers\Api\V1\VideoController;
use App\Http\Controllers\Api\V1\WalletController;
use App\Http\Controllers\Api\V1\WishListController;
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
    Route::apiResource('user', UserController::class)->only(['show','update']);
    Route::apiResource('user/wish-list', WishListController::class);
    //wallet in user
    Route::get('/user/wallet', [WalletController::class, 'show']);
    Route::put('/user/wallet/{wallet_id}', [WalletController::class, 'update']);

    Route::post('/logout', [AuthController::class, 'logout']);
});
Route::group(['middleware' => ['auth:sanctum', 'role:lecturer']], function () {
    Route::get('/lecturer/dashboard', [LecturerController::class, 'dashboard']);
    Route::get('/lecturer', [LecturerController::class, 'getLecturerInfo']);

    Route::apiResource('/lecturer/courses', CourseController::class)->parameters(['courses' => 'course_id']);
    Route::apiResource('lecturer/courses/{course_id}/sections', SectionController::class)->only(['store', 'update', 'destroy'])->parameters(['sections' => 'section_id']);
    Route::apiResource('/lecturer/courses/{course_id}/sections/{section_id}/lessons', LessonController::class)->only(['store', 'update', 'destroy'])->parameters(['lessons' => 'lesson_id']);
    Route::apiResource('/lecturer/courses/{course_id}/sections/{section_id}/lessons/{lesson_id}/videos', VideoController::class)->only(['store', 'update', 'destroy'])->parameters(['videos' => 'video_id']);
    Route::apiResource('/lecturer/courses/{course_id}/sections/{section_id}/lessons/{lesson_id}/documents', DocumentController::class)->only(['store', 'update', 'destroy'])->parameters(['documents' => 'document_id']);
    Route::apiResource('/lecturer/courses/{course_id}/sections/{section_id}/lessons/{lesson_id}/codings', LessonCodingController::class)->only(['store', 'update', 'destroy'])->parameters(['codings' => 'coding_id']);

    Route::apiResource('/lecturer/courses/{course_id}/sections/{section_id}/lessons/{lesson_id}/quizzes',QuizController::class)->parameters(['quizzes' => 'quiz_id']);
    Route::apiResource('/lecturer/courses/{course_id}/sections/{section_id}/lessons/{lesson_id}/quizzes/{quiz_id}/questions',QuizController::class)->parameters(['questions' => 'question_id']);



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
Route::get('/tags', [TagController::class, 'index']);
