<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ForgotPasswordController;
use App\Http\Controllers\Api\ResetPasswordController;
use App\Http\Controllers\Api\V1\BannerController;
use App\Http\Controllers\Api\V1\CategoryController;
use App\Http\Controllers\Api\V1\CertificateController;
use App\Http\Controllers\Api\V1\ChatMessageController;
use App\Http\Controllers\Api\V1\ChatRoomController;
use App\Http\Controllers\Api\V1\CommentController;
use App\Http\Controllers\Api\V1\ComplainController;
use App\Http\Controllers\Api\V1\CompletionController;
use App\Http\Controllers\Api\V1\CourseController;
use App\Http\Controllers\Api\V1\DocumentController;
use App\Http\Controllers\Api\V1\EnrollmentController;
use App\Http\Controllers\Api\V1\GoogleAuthController;
use App\Http\Controllers\Api\V1\LecturerController;
use App\Http\Controllers\Api\V1\LecturerRegisterController;
use App\Http\Controllers\Api\V1\LessonCodingController;
use App\Http\Controllers\Api\V1\LessonController;
use App\Http\Controllers\Api\V1\NoteController;
use App\Http\Controllers\Api\V1\NotificationController;
use App\Http\Controllers\Api\V1\OverviewController;
use App\Http\Controllers\Api\V1\PostController;
use App\Http\Controllers\Api\V1\QuizController;
use App\Http\Controllers\Api\V1\ReviewController;
use App\Http\Controllers\Api\V1\SectionController;
use App\Http\Controllers\Api\V1\StudyController;
use App\Http\Controllers\Api\V1\TagController;
use App\Http\Controllers\Api\V1\TransactionController;
use App\Http\Controllers\Api\V1\TransactionWalletController;
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
use Laravel\Socialite\Facades\Socialite;

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
Route::get('/callBackMomo', [WalletController::class, 'momoCallback']);

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::get('/user', [AuthController::class, 'getUser']);
    Route::post('/broadcasting/auth', [AuthController::class, 'broadcasting']);
    Route::apiResource('users', UserController::class)->only(['show', 'update']);
    Route::get('/courseNew', [OverviewController::class, 'courseNew']);

    Route::post('/createMomo', [WalletController::class, 'momoCreatePayment']);

    Route::put('users', [UserController::class, 'update']);
    Route::apiResource('user/wish-list', WishListController::class)->parameters(['wish-list' => 'wish-list_id']);
    Route::post('register/answers', [LecturerRegisterController::class, 'submitAnswers']);
    Route::get('/lecturer-registrations', [LecturerRegisterController::class, 'getLecturerRegistrations']);

    //wallet in user
    Route::get('/user/wallets', [WalletController::class, 'show']);
    Route::put('/user/wallets', [WalletController::class, 'update']); //test
    Route::post('/user/wallets/deposit', [WalletController::class, 'depositPayment']); // nạp tiền vào ví
    // Pay by VNPay
    Route::post('/user/courses/{course_id}/create-payment', [VNPayAPIController::class, 'createPayment']);
    // Pay by wallet
    Route::post('/user/courses/{course_id}/wallet-payment', [WalletController::class, 'payment']);

    // Voucher in user 
    Route::get('/user/vouchers', [VoucherController::class, 'index']);
    Route::get('/user/voucher/{voucher_id}', [VoucherController::class, 'show']);
    Route::post('/user/course/{course_id}/voucher/{voucher_id}/uses', [VoucherController::class, 'useVoucher']); // Sử dụng voucher
    Route::get('/user/vouchers/history', [VoucherController::class, 'history']); // Lịch sử dùng voucher của người dùng

    //Study
    Route::get('student/{user_id}/courses/{course_id}', [StudyController::class, 'getCourseInfo']);
    Route::post('student/{user_id}/courses/{course_id}/sections/{section_id}/lessons/{lesson_id}/starts', [StudyController::class, 'startLesson']);

    Route::get('student/courses/{course_id}/sections/{section_id}/lessons', [StudyController::class, 'getLessonsBySection']);

    // danh sách khóa học đã đăng ký 
    Route::get('/user/courses', [EnrollmentController::class, 'getUserCoursesWithProgress']);
    Route::get('/user/courses/{course_id}', [EnrollmentController::class, 'getprogress']);

    // review 
    Route::post('/courses/{courseId}/reviews', [ReviewController::class, 'storeCourseReview']);
    Route::post('/lecturers/{lecturerId}/reviews', [ReviewController::class, 'storeLecturerReview']);
    Route::put('/reviews/{review}', [ReviewController::class, 'update']);
    Route::delete('/reviews/{review}', [ReviewController::class, 'destroy']);
    Route::get('/courses/{courseId}/reviews', [ReviewController::class, 'getCourseReviews']);
    Route::get('/lecturers/{lecturerId}/reviews', [ReviewController::class, 'getLecturerReviews']);
    Route::get('/reviews/user', [ReviewController::class, 'getUserReviews']);

    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{notification:id}/mark-as-read', [NotificationController::class, 'markAsRead'])->name('notifications.markAsRead');
    Route::post('/notifications/mark-all-as-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.markAllAsRead');
    Route::delete('/notifications/{notification:id}', [NotificationController::class, 'destroy'])->name('notifications.destroy');
    Route::delete('/notifications', [NotificationController::class, 'destroyAll'])->name('notifications.destroyAll');

    //Post
    Route::get('/posts', [PostController::class, 'index']); // Danh sách bài viết
    Route::post('/posts', [PostController::class, 'store']); // Tạo bài viết mới
    Route::get('/posts/{post}', [PostController::class, 'show']); // Xem chi tiết bài viết
    Route::put('/posts/{post}', [PostController::class, 'update']); // Cập nhật bài viết
    Route::delete('/posts/{post}', [PostController::class, 'destroy']); // Xóa bài viết

    // Comment
    Route::get('/lessons/{lesson}/comments', [CommentController::class, 'getLessonComment']);
    Route::post('lessons/{lesson}/comments', [CommentController::class, 'storeLessonComment']);
    Route::put('/courses/{course}/lessons/{lesson}/comments/{comment}', [CommentController::class, 'updateLessonComment']);
    Route::delete('/courses/{course}/lessons/{lesson}/comments/{comment}', [CommentController::class, 'destroyLessonComment']);

    Route::get('/posts/{post_id}/comments/posts', [CommentController::class, 'getPostComments'])->name('post.comments.index');
    Route::post('/posts/{post_id}/comments/', [CommentController::class, 'storePostComment'])->name('post.comments.store');
    Route::put('/posts/{post_id}/comments/{comment_id}', [CommentController::class, 'updatePostComment'])->name('post.comments.update');
    Route::delete('/posts/{post_id}/comments/{comment_id}', [CommentController::class, 'destroyPostComment'])->name('post.comments.destroy');


    //Đổi mật khẩu
    Route::post('/change-password', [ResetPasswordController::class, 'resetPassword'])->name('change.password');


    //Chat room
    Route::get('/chat-rooms', [ChatRoomController::class, 'index']);
    Route::post('/chat-rooms', [ChatRoomController::class, 'store']);
    Route::get('/chat-rooms/{id}', [ChatRoomController::class, 'show']);
    Route::delete('/chat-rooms/{id}', [ChatRoomController::class, 'destroy']);


    //Messenger
    Route::get('/chat-rooms/{id}/messages', [ChatMessageController::class, 'index']);
    Route::post('/chat-rooms/{id}/messages', [ChatMessageController::class, 'store']);
    Route::delete('/chat-messages/{id}', [ChatMessageController::class, 'destroy']);
    // Lịch sử nạp tiền
    Route::get('/user/wallet/deposit-histories', [TransactionWalletController::class, 'depositHistory']);
    // Lịch sử ví tiền (tất cả các loại giao dịch)
    Route::get('/user/wallet/histories', [TransactionWalletController::class, 'walletHistory']);

});
// Callback payment
Route::get('/user/courses/{course_id}/payment-callback', [VNPayAPIController::class, 'paymentCallback']);
Route::get('/user/wallets/result', [WalletController::class, 'resultPaymemt']); // trả kết quả thanh toán nạp tiền vào ví


Route::group(['middleware' => ['auth:sanctum', 'role:lecturer']], function () {
    Route::get('/lecturer/dashboard', [LecturerController::class, 'dashboard']);
    Route::get('/lecturer', [LecturerController::class, 'getLecturerInfo']);
    Route::get('lecturer/courses/{course_id}/check', [CourseController::class, 'check']);
    Route::get('lecturer/courses/{course_id}/pending', [CourseController::class, 'checkPending']);

    // Thống kê giảng viên
    Route::get('/lecturer/statistics', [LecturerController::class, 'statistics']);

    // Route::post('lecturer/courses/{course_id}/sections/{section_id}/lessonsCreateVideo',[CourseController::class , 'lessonCreateVideo']);
    Route::apiResource('/lecturer/courses', CourseController::class)->parameters(['courses' => 'course_id']);
    Route::post('/lecturer/courses/{course_id}/pending', [CourseController::class, 'pending']);
    Route::apiResource('lecturer/courses/{course_id}/sections', SectionController::class)->parameters(['sections' => 'section_id']);
    Route::apiResource('/lecturer/courses/{course_id}/sections/{section_id}/lessons', LessonController::class)->parameters(['lessons' => 'lesson_id']);
    Route::apiResource('/lecturer/courses/{course_id}/sections/{section_id}/lessons/{lesson_id}/videos', VideoController::class)->parameters(['videos' => 'video_id']);
    Route::apiResource('/lecturer/courses/{course_id}/sections/{section_id}/lessons/{lesson_id}/documents', DocumentController::class)->parameters(['documents' => 'document_id']);
    // Route::apiResource('/lecturer/courses/{course_id}/sections/{section_id}/lessons/{lesson_id}/codings', LessonCodingController::class)->parameters(['codings' => 'coding_id']);
    Route::post('/lecturer/courses/{course_id}/sections/{section_id}/codings', [LessonCodingController::class, 'store']);

    // Quản lý Quiz trong một bài học (Lesson)
    Route::apiResource('/lecturer/courses/{course_id}/sections/{section_id}/lessons/{lesson_id}/quizzes', QuizController::class)
        ->parameters(['quizzes' => 'quiz_id']);

    // Quản lý câu hỏi (Questions) trong một Quiz
    Route::apiResource('/lecturer/lessons/{lesson_id}/quizzes/{quiz_id}/questions', QuizController::class)
        ->parameters(['questions' => 'question_id']);

    // Lấy danh sách câu hỏi của một Quiz
    Route::get('/lecturer/quizzes/{quiz_id}/questions', [QuizController::class, 'getQuestions']);
    Route::get('/lecturer/quizzes/questions/{question_id}', [QuizController::class, 'showQuestions']);

    // Tạo câu hỏi trong Quiz
    Route::post('/lecturer/quizzes/{quiz_id}/questions', [QuizController::class, 'storeQuestion']);
    Route::put('/lecturer/quizzes/{quiz_id}/questions/{question_id}', [QuizController::class, 'updateQuestion']);
    // Route::post('/lecturer/questions/{question_id}/answers', [QuizController::class, 'storeAnswer']);
    Route::delete('/lecturer/questions/{question_id}', [QuizController::class, 'deleteQuestion']);
    // Cập nhật thứ tự câu hỏi trong Quiz
    Route::post('/user/quizzes/{quiz_id}/update-order', [QuizController::class, 'updateQuizOrder']);

    Route::post('lessons/order', [LessonController::class, 'updateOrder']);

    Route::post('/chat-rooms/{id}/add-user', [ChatRoomController::class, 'addUser']);
    Route::post('/chat-rooms/{id}/remove-user', [ChatRoomController::class, 'removeUser']);
    Route::post('/chat-rooms/{id}/mute-user', [ChatRoomController::class, 'muteUser']);

    // rút tiền ví giảng viên
    Route::post('/lecturer/wallets/withdraw', [WalletController::class, 'withdraw']);
    // Lịch sử rút tiền
    Route::get('/lecturer/wallet/withdraw-histories', [TransactionWalletController::class, 'withdrawHistory']);

    // Gửi khiếu nại rút tiền
    Route::post('/lecturer/wallets/withdraws/{transaction_wallet_id}/complain', [ComplainController::class, 'complain']);
    // Danh sách khiếu nại
    Route::get('/lecturer/wallet/complain', [ComplainController::class, 'listComplain']);
    // Xem chi tiết khiếu nại
    Route::get('/lecturer/wallet/complains/{complain_id}', [ComplainController::class, 'detailComplain']);
    // Hủy yêu cầu khiếu nại
    Route::put('/lecturer/wallet/complain/{complain_id}/cancel', [ComplainController::class, 'cancelComplain']);

    // Thêm thẻ ngân hàng vào thông tin người dùng
    Route::post('/lecturer/insertBank', [UserController::class, 'insertBank']);
    // Lấy thông tin thẻ ngân hàng của người dùng
    Route::get('/lecturer/getBank', [UserController::class, 'getBank']);

    // Danh sách khóa học đã bán của giảng viên
    Route::get('/lecturer/sell-course-list', [TransactionController::class, 'sellList']);
    // Danh sách học viên đã đăng kí theo khóa học
    Route::get('/lecturer/sell-course/{course_id}/studentList', [TransactionController::class, 'studentListByCourse']);

    // Excel quiz
    Route::post('/lessons/{lessonId}/quizzes/{quiz_id}/upload', [QuizController::class, 'uploadQuizExcel']);

    Route::get('/lecturer-course-student-infor', [CourseController::class, 'getStudentInfoInCourse']);

});
Route::group(['middleware' => ['auth:sanctum', 'role:student']], function () {

    Route::get('/student/courses/{course_id}', [EnrollmentController::class, 'showUserEnrollmentCourse']);
    Route::get('/lesson/{lesson_id}', [EnrollmentController::class, 'showLesson']);
    Route::get('course/{course_id}/lesson', [EnrollmentController::class, 'getStatusLesson']);
    Route::post('student/courses/{course_id}/lessons/{lesson_id}/completes', [StudyController::class, 'completeLesson']);
    Route::post('/certificates/student/courses/{course_id}', [CertificateController::class, 'createCertificate']);
    Route::get('show/certificates/{course_id}', [CertificateController::class, 'showCertificate']);
    Route::get('certificates/{certificate_id}', [CertificateController::class, 'certificate']);
    Route::get('progress/course/{course_id}', [CompletionController::class, 'showUserCourseProgress']);
    Route::get('progress/{course_id}', [CompletionController::class, 'getLatestCourseInProgress']);

    // Nộp bài Quiz
    Route::post('/user/{user_id}/quizzes/{quiz_id}/submit', [QuizController::class, 'submitQuiz']);

    //chức năng ghi chú
    Route::get('/learning/notes/{course_id}', [NoteController::class, 'index']); // Lấy danh sách ghi chú
    Route::get('/learning/notes/{course_id}/{lesson_id}', [NoteController::class, 'noteInSection']); // Lấy danh sách ghi chú
    Route::post('/learning/lesson/{lesson_id}/notes', [NoteController::class, 'store']); // Tạo ghi chú
    Route::put('/learning/notes/{note}', [NoteController::class, 'update']); // Cập nhật ghi chú
    Route::delete('/learning/notes/{note}', [NoteController::class, 'destroy']); // Xóa ghi chú

    // chức năng wish-list
    Route::get('/user/wishlist', [WishListController::class, 'index']); // Lấy toàn bộ wish-list
    Route::post('/user/wishlist/{course_id}', [WishListController::class, 'store']); // Thêm course vào wish-list
    Route::delete('/user/wishlist/{course_id}', [WishListController::class, 'destroy']); // Xóa course khỏi wish-list
    Route::get('/user/wishlist/check/{course_id}', [WishListController::class, 'check']); // Kiểm tra course

    // Danh sách khóa học đã mua
    Route::get('/student/course-list', [TransactionController::class, 'courseList']);


    Route::get('/course-category', [OverviewController::class, 'courseCategory']);

});

Route::get('/student/home', [OverviewController::class, 'overview']);
Route::get('/course-category-guest', [OverviewController::class, 'courseCategoryGuest']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
//Forgot password
Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('forgot-password');
Route::post('/reset-password', [ResetPasswordController::class, 'reset'])->name('password.reset');

Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/courses/{course_id}/public', [CourseController::class, 'publicCourseDetail']);
Route::get('/courses/{course_id}/related', [CourseController::class, 'relatedCourses']);
// Route::apiResource('/tags', TagController::class)->parameters(['tags' => 'tag_id']);
//Xem đánh giá

Route::get('/auth/google/redirect', [GoogleAuthController::class, 'redirectToGoogle']);
Route::get('/auth/google/callback', [GoogleAuthController::class, 'handleGoogleCallback']);

Route::get('/api/documentation', [SwaggerController::class, 'api'])->name('l5-swagger.default.api');
Route::get('/banners', [BannerController::class, 'index']);
Route::get('/guest/lecturer', [OverviewController::class, 'guestLecturer']);
Route::get('/guest/lecturer-info/{lecturer_id}', [OverviewController::class, 'guestLecturerInfo']);
