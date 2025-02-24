<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CertificateController;
use App\Http\Controllers\Api\V1\CategoryController;
use App\Http\Controllers\Api\V1\CourseController;
use App\Http\Controllers\Api\V1\DocumentController;
use App\Http\Controllers\Api\V1\EnrollmentController;
use App\Http\Controllers\Api\V1\LecturerController;
use App\Http\Controllers\Api\V1\LecturerRegisterController;
use App\Http\Controllers\Api\V1\LessonCodingController;
use App\Http\Controllers\Api\V1\LessonController;
use App\Http\Controllers\Api\V1\NotificationController;
use App\Http\Controllers\Api\V1\OverviewController;
use App\Http\Controllers\Api\V1\QuizController;
use App\Http\Controllers\Api\V1\SectionController;
use App\Http\Controllers\Api\V1\StudyController;
use App\Http\Controllers\Api\V1\TagController;
use App\Http\Controllers\Api\V1\UserController;
use App\Http\Controllers\Api\V1\VideoController;
use App\Http\Controllers\Api\V1\VNPayAPIController;
use App\Http\Controllers\Api\V1\VoucherController;
use App\Http\Controllers\Api\V1\WalletController;
use App\Http\Controllers\Api\V1\WishListController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use L5Swagger\Http\Controllers\SwaggerController;

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

    Route::put('users', [UserController::class, 'update']);
    Route::apiResource('user/wish-list', WishListController::class)->parameters(['wish-list' => 'wish-list_id']);
    Route::post('register/answers', [LecturerRegisterController::class, 'submitAnswers']);

    //wallet in user
    Route::get('/user/wallets', [WalletController::class, 'show']);
    Route::put('/user/wallets', [WalletController::class, 'update']); //test
    Route::post('/user/wallets/deposit', [WalletController::class, 'depositPayment']); // nạp tiền vào ví
    Route::get('/user/wallets/result', [WalletController::class, 'resultPaymemt']); // trả kết quả thanh toán
    // Pay by VNPay
    Route::post('/user/courses/{course_id}/create-payment', [VNPayAPIController::class, 'createPayment']);
    // Pay by wallet
    Route::post('/user/courses/{course_id}/wallet-payment', [WalletController::class, 'payment']);

    // Voucher in user 
    Route::get('/user/vouchers', [VoucherController::class, 'index']);
    Route::get('/user/voucher/{voucher_id}', [VoucherController::class, 'show']);
    Route::post('/user/course/{course_id}/voucher/{voucher_id}/uses', [VoucherController::class, 'useVoucher']); // Sử dụng voucher

    //Study
    Route::get('student/{user_id}/courses/{course_id}', [StudyController::class, 'getCourseInfo']);
    Route::post('student/{user_id}/courses/{course_id}/sections/{section_id}/lessons/{lesson_id}/starts', [StudyController::class, 'startLesson']);
    Route::post('student/{user_id}/courses/{course_id}/sections/{section_id}/lessons/{lesson_id}/completes', [StudyController::class, 'completeLesson']);
    Route::post('/certificates/student/{user_id}/courses/{course_id}', [CertificateController::class, 'createCertificate']);
    // danh sách khóa học đã đăng ký 
    Route::get('/user/courses', [EnrollmentController::class, 'getUserCoursesWithProgress']);

    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{notification:id}/mark-as-read', [NotificationController::class, 'markAsRead'])->name('notifications.markAsRead');
    Route::post('/notifications/mark-all-as-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.markAllAsRead');
    Route::delete('/notifications/{notification:id}', [NotificationController::class, 'destroy'])->name('notifications.destroy');
    Route::delete('/notifications', [NotificationController::class, 'destroyAll'])->name('notifications.destroyAll');
});
// Callback payment
Route::get('/user/courses/{course_id}/payment-callback', [VNPayAPIController::class, 'paymentCallback']);


Route::group(['middleware' => ['auth:sanctum', 'role:lecturer']], function () {
    Route::get('/lecturer/dashboard', [LecturerController::class, 'dashboard']);
    Route::get('/lecturer', [LecturerController::class, 'getLecturerInfo']);
    Route::get('lecturer/courses/{course_id}/check', [CourseController::class, 'check']);
    Route::get('lecturer/courses/{course_id}/pending', [CourseController::class, 'checkPending']);
    // Route::post('lecturer/courses/{course_id}/sections/{section_id}/lessonsCreateVideo',[CourseController::class , 'lessonCreateVideo']);
    Route::apiResource('/lecturer/courses', CourseController::class)->parameters(['courses' => 'course_id']);
    Route::post('/lecturer/courses/{course_id}/pending', [CourseController::class, 'pending']);
    Route::apiResource('lecturer/courses/{course_id}/sections', SectionController::class)->parameters(['sections' => 'section_id']);
    Route::apiResource('/lecturer/courses/{course_id}/sections/{section_id}/lessons', LessonController::class)->parameters(['lessons' => 'lesson_id']);
    Route::apiResource('/lecturer/courses/{course_id}/sections/{section_id}/lessons/{lesson_id}/videos', VideoController::class)->parameters(['videos' => 'video_id']);
    Route::apiResource('/lecturer/courses/{course_id}/sections/{section_id}/lessons/{lesson_id}/documents', DocumentController::class)->parameters(['documents' => 'document_id']);
    Route::apiResource('/lecturer/courses/{course_id}/sections/{section_id}/lessons/{lesson_id}/codings', LessonCodingController::class)->parameters(['codings' => 'coding_id']);

    // Quản lý Quiz trong một bài học (Lesson)
    Route::apiResource('/lecturer/courses/{course_id}/sections/{section_id}/lessons/{lesson_id}/quizzes', QuizController::class)
        ->parameters(['quizzes' => 'quiz_id']);

    // Quản lý câu hỏi (Questions) trong một Quiz
    Route::apiResource('/lecturer/lessons/{lesson_id}/quizzes/{quiz_id}/questions', QuizController::class)
        ->parameters(['questions' => 'question_id']);

    // Lấy danh sách câu hỏi của một Quiz
    Route::get('/lecturer/quizzes/{quiz_id}/questions', [QuizController::class, 'getQuestions']);
    Route::post('/lecturer/quizzes/{quiz_id}/questions', [QuizController::class, 'storeQuestion']);
    Route::post('/lecturer/questions/{question_id}/answers', [QuizController::class, 'storeAnswer']);

    

    // Cập nhật thứ tự câu hỏi trong Quiz
    Route::post('/user/quizzes/{quiz_id}/update-order', [QuizController::class, 'updateQuizOrder']);


    Route::post('lessons/order', [LessonController::class, 'updateOrder']);
});
Route::group(['middleware' => ['auth:sanctum', 'role:student']], function () {
    Route::get('/student/dashboard', function (Request $request) {
        return response()->json(['message' => 'Chào mừng Học viên']);
    });

    Route::get('/student/home', [OverviewController::class, 'overview']);

    // Nộp bài Quiz
    Route::post('/user/{user_id}/quizzes/{quiz_id}/submit', [QuizController::class, 'submitQuiz']);
});
Route::group(['middleware' => ['auth:sanctum', 'role:admin']], function () {
    Route::get('/admin/dashboard', function (Request $request) {
        return response()->json(['message' => 'Chào mừng Quản trị viên']);
    });
});

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/courses/{course_id}/public', [CourseController::class, 'publicCourseDetail']);
Route::apiResource('/tags', TagController::class)->parameters(['tags' => 'tag_id']);

Route::get('/api/documentation', [SwaggerController::class, 'api'])->name('l5-swagger.default.api');
