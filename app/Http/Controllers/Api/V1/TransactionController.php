<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TransactionController extends Controller
{
    /**
     * @OA\Get(
     *   path="/student/course-list",
     *   tags={"Student - Courses"},
     *   summary="Lấy danh sách các khóa học đã mua của học viên",
     *   description="API này trả về danh sách các khóa học mà học viên đã thanh toán thành công, bao gồm cả thông tin giảng viên.",
     *   security={{"BearerAuth": {}}},
     *   @OA\Response(
     *     response=200,
     *     description="Danh sách khóa học đã mua hoặc không có khóa học nào",
     *     @OA\JsonContent(
     *       type="object",
     *       @OA\Property(property="status", type="string", example="success"),
     *       @OA\Property(property="Danh sách khóa học đã mua", type="array",
     *         @OA\Items(
     *           type="object",
     *           @OA\Property(property="id", type="integer", example=1),
     *           @OA\Property(property="amount", type="number", format="float", example=250000),
     *           @OA\Property(property="status", type="string", example="success"),
     *           @OA\Property(property="transaction_date", type="string", format="date-time", example="2025-04-01T15:30:00"),
     *           @OA\Property(
     *             property="course",
     *             type="object",
     *             @OA\Property(property="id", type="string", format="uuid", example="cfa23d80-7fa2-40d5-8c2c-3a814c6c7d11"),
     *             @OA\Property(property="title", type="string", example="Lập trình Laravel từ A-Z"),
     *             @OA\Property(property="thumbnail", type="string", example="https://domain.com/storage/thumbnail.jpg"),
     *             @OA\Property(
     *               property="user",
     *               type="object",
     *               description="Thông tin giảng viên",
     *               @OA\Property(property="id", type="integer", example=5),
     *               @OA\Property(property="name", type="string", example="Nguyễn Văn Giảng Viên"),
     *               @OA\Property(property="email", type="string", example="gvien@example.com")
     *             )
     *           )
     *         )
     *       )
     *     )
     *   ),
     *   @OA\Response(
     *     response=404,
     *     description="Không tìm thấy người dùng",
     *     @OA\JsonContent(
     *       type="object",
     *       @OA\Property(property="status", type="string", example="error"),
     *       @OA\Property(property="message", type="string", example="Không tìm thấy người dùng này.")
     *     )
     *   ),
     *   @OA\Response(
     *     response=500,
     *     description="Lỗi server",
     *     @OA\JsonContent(
     *       type="object",
     *       @OA\Property(property="message", type="string", example="Lỗi server"),
     *       @OA\Property(property="error", type="string", example="Chi tiết lỗi")
     *     )
     *   )
     * )
     */
    // Danh sách khóa học đã mua của học viên
    public function courseList(Request $request)
    {
        try {

            $user_id = $request->user()->id;

            $transactions = Transaction::with(['course.user'])
                                        ->where('status', 'success')
                                        ->where('user_id', $user_id)
                                        ->get();

            // Kiểm tra xem người dùng có tồn tại không
            if (!$user_id) {
                return response()->json([
                    'status'    => 'error',
                    'message'   => 'Không tìm thấy người dùng này.'
                ], Response::HTTP_NOT_FOUND);
            }
            // Kiểm tra xem có tồn tại danh sách khóa học không
            if ($transactions->isEmpty()) {
                return response()->json([
                    'status'    => 'success',
                    'message'   => 'Không có khóa học nào đã được mua.'
                ], Response::HTTP_OK);
            }

            return response()->json([
                'status'                    => 'success',
                'Danh sách khóa học đã mua' => $transactions
            ], Response::HTTP_OK);

        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Lỗi server',
                'error' => $th->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\Get(
     *   path="/lecturer/sell-course-list",
     *   tags={"Lecturer - Courses"},
     *   summary="Lấy danh sách các khóa học đã bán của giảng viên",
     *   description="API này trả về danh sách các khóa học mà giảng viên đã xuất bản và có giao dịch thành công, kèm theo doanh thu và số lượt mua.",
     *   security={{"BearerAuth": {}}},
     *   @OA\Response(
     *     response=200,
     *     description="Danh sách khóa học đã bán hoặc không có khóa học nào",
     *     @OA\JsonContent(
     *       type="object",
     *       @OA\Property(property="status", type="string", example="success"),
     *       @OA\Property(property="Danh sách khóa học đã bán", type="array",
     *         @OA\Items(
     *           type="object",
     *           @OA\Property(property="id", type="integer", example=3),
     *           @OA\Property(property="title", type="string", example="Khóa học ReactJS chuyên sâu"),
     *           @OA\Property(property="status", type="string", example="published"),
     *           @OA\Property(property="transactions", type="array",
     *             @OA\Items(
     *               type="object",
     *               @OA\Property(property="id", type="integer", example=21),
     *               @OA\Property(property="user_id", type="integer", example=7),
     *               @OA\Property(property="amount", type="number", format="float", example=200000),
     *               @OA\Property(property="status", type="string", example="success")
     *             )
     *           )
     *         )
     *       ),
     *       @OA\Property(property="Tổng doanh thu", type="array",
     *         @OA\Items(
     *           type="object",
     *           @OA\Property(property="ID", type="integer", example=3),
     *           @OA\Property(property="Tên khóa học", type="string", example="Khóa học ReactJS chuyên sâu"),
     *           @OA\Property(property="Doanh thu", type="number", format="float", example=2000000),
     *           @OA\Property(property="Lợi nhuận", type="number", format="float", example=1400000),
     *           @OA\Property(property="Số lượt mua", type="integer", example=10)
     *         )
     *       )
     *     )
     *   ),
     *   @OA\Response(
     *     response=404,
     *     description="Không tìm thấy người dùng",
     *     @OA\JsonContent(
     *       type="object",
     *       @OA\Property(property="status", type="string", example="error"),
     *       @OA\Property(property="message", type="string", example="Không tìm thấy người dùng này.")
     *     )
     *   ),
     *   @OA\Response(
     *     response=500,
     *     description="Lỗi server",
     *     @OA\JsonContent(
     *       type="object",
     *       @OA\Property(property="message", type="string", example="Lỗi server"),
     *       @OA\Property(property="error", type="string", example="Chi tiết lỗi")
     *     )
     *   )
     * )
     */
    // Danh sách khóa học đã bán của giảng viên
    public function sellList(Request $request)
    {
        try {

            $user_id = $request->user()->id;

            $sell_list = Course::with(['transactions' => function ($query) {
                                    $query->where('status', 'success');
                                }])
                                ->where('user_id', $user_id)
                                ->where('status', 'published')
                                ->get();

            // Xử lý tính doanh thu cho từng khóa học
            $sell_list_with_revenue = $sell_list->map(function ($course) {
                $revenue = $course->transactions->sum('amount');

                return [
                    'ID'            => $course->id,
                    'Tên khóa học'  => $course->title,
                    'Doanh thu'     => $revenue,
                    'Lợi nhuận'     => ($revenue/100) * 70,
                    'Số lượt mua'   => $course->transactions->count(),
                ];
            });

            // Kiểm tra xem người dùng có tồn tại không
            if (!$user_id) {
                return response()->json([
                    'status'    => 'error',
                    'message'   => 'Không tìm thấy người dùng này.'
                ], Response::HTTP_NOT_FOUND);
            }
            // Kiểm tra xem có tồn tại danh sách khóa học không
            if ($sell_list->isEmpty()) {
                return response()->json([
                    'status'    => 'success',
                    'message'   => 'Không có khóa học nào đang được bán.'
                ], Response::HTTP_OK);
            }

            return response()->json([
                'status'                    => 'success',
                'Danh sách khóa học đã bán' => $sell_list,
                'Tổng doanh thu'            => $sell_list_with_revenue
            ], Response::HTTP_OK);

        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Lỗi server',
                'error' => $th->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\Get(
     *   path="/lecturer/sell-course/{course_id}/studentList",
     *   tags={"Lecturer - Courses"},
     *   summary="Lấy danh sách học viên đã đăng ký khóa học",
     *   description="API này trả về danh sách các học viên đã thanh toán thành công cho khóa học của giảng viên.",
     *   security={{"BearerAuth": {}}},
     *   @OA\Parameter(
     *     name="course_id",
     *     in="path",
     *     description="ID của khóa học",
     *     required=true,
     *     @OA\Schema(type="integer")
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Danh sách học viên đã đăng ký hoặc chưa có học viên nào",
     *     @OA\JsonContent(
     *       type="object",
     *       @OA\Property(property="status", type="string", example="success"),
     *       @OA\Property(property="students", type="array",
     *         @OA\Items(
     *           type="object",
     *           @OA\Property(property="ID", type="integer", example=7),
     *           @OA\Property(property="Tên", type="string", example="Nguyễn Văn A"),
     *           @OA\Property(property="Email", type="string", example="student@example.com"),
     *           @OA\Property(property="Số tiền thanh toán", type="number", format="float", example=200000),
     *           @OA\Property(property="Ngày thanh toán", type="string", format="date-time", example="2025-04-01T15:30:00"),
     *           @OA\Property(property="Phương thức thanh toán", type="string", example="vnpay")
     *         )
     *       )
     *     )
     *   ),
     *   @OA\Response(
     *     response=404,
     *     description="Khóa học không tồn tại hoặc không thuộc quyền quản lý",
     *     @OA\JsonContent(
     *       type="object",
     *       @OA\Property(property="status", type="string", example="error"),
     *       @OA\Property(property="message", type="string", example="Khóa học không tồn tại.")
     *     )
     *   ),
     *   @OA\Response(
     *     response=500,
     *     description="Lỗi server",
     *     @OA\JsonContent(
     *       type="object",
     *       @OA\Property(property="message", type="string", example="Lỗi server"),
     *       @OA\Property(property="error", type="string", example="Chi tiết lỗi")
     *     )
     *   )
     * )
     */
    // Danh sách học viên theo khóa học đã bán
    public function studentListByCourse(Request $request, $course_id)
    {
        try {

            $transactions = Transaction::with('user')
                ->where('course_id', $course_id)
                ->where('status', 'success')
                ->get();
            
            $user_id = $request->user()->id;
            $course = Course::where('id', $course_id)
                            ->where('user_id', $user_id)
                            ->first();

            // Kiểm tra khóa học có tồn tại không
            if (!$course) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Khóa học không tồn tại.'
                ], Response::HTTP_NOT_FOUND);
            }
            // Nếu không có học viên
            if ($transactions->isEmpty()) {
                return response()->json([
                    'status'  => 'success',
                    'message' => 'Chưa có học viên nào đăng ký khoá học này.'
                ], Response::HTTP_OK);
            }

            // Lấy danh sách học viên
            $students = $transactions->map(function ($transaction) {
                return [
                    'ID'                        => $transaction->user->id,
                    'Tên'                       => $transaction->user->name,
                    'Email'                     => $transaction->user->email,
                    'Số tiền thanh toán'        => $transaction->amount,
                    'Ngày thanh toán'           => $transaction->transaction_date,
                    'Phương thức thanh toán'    => $transaction->payment_method,
                ];
            });

            return response()->json([
                'status'  => 'success',
                'students' => $students
            ], Response::HTTP_OK);

        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Lỗi server',
                'error' => $th->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

}
