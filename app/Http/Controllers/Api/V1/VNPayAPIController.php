<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Completion;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\Progress;
use App\Models\Section;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class VNPayAPIController extends Controller
{

    /**
     * @OA\Post(
     *   path="/user/courses/{course_id}/create-payment",
     *   tags={"VNPay"},
     *   summary="Tạo yêu cầu thanh toán VNPay cho khóa học",
     *   description="API này cho phép người dùng tạo yêu cầu thanh toán VNPay cho khóa học (trừ khóa học miễn phí).",
     *   security={{"BearerAuth": {}}},
     *   @OA\Parameter(
     *       name="course_id",
     *       in="path",
     *       description="ID của khóa học",
     *       required=true,
     *       @OA\Schema(type="integer")
     *   ),
     *   @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(
     *       type="object",
     *       @OA\Property(property="amount", type="integer", example=10000, description="Số tiền cần thanh toán"),
     *       @OA\Property(property="bank_code", type="string", example="NCB", description="Mã ngân hàng (ví dụ: NCB, VISA, JCB)")
     *     )
     *   ),
     *   @OA\Response(
     *     response=201,
     *     description="Tạo yêu cầu thanh toán thành công",
     *     @OA\JsonContent(
     *       type="object",
     *       @OA\Property(property="status", type="string", example="success"),
     *       @OA\Property(property="payment_url", type="string", example="https://sandbox.vnpayment.vn/paymentv2/vpcpay.html?vnp_Amount=...", description="URL thanh toán VNPay")
     *     )
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Khóa học miễn phí, đã tự động đăng ký",
     *     @OA\JsonContent(
     *         type="object",
     *         @OA\Property(property="message", type="string", example="Khóa học miễn phí, đã tự động đăng ký")
     *     )
     *   ),
     *   @OA\Response(
     *     response=400,
     *     description="Lỗi validation hoặc đã tham gia khóa học",
     *     @OA\JsonContent(
     *       type="object",
     *       @OA\Property(property="errors", type="object", description="Các lỗi validation"),
     *       @OA\Property(property="error", type="string", example="Bạn đã tham gia khóa học")
     *     )
     *   ),
     *   @OA\Response(
     *     response=404,
     *     description="Không tìm thấy khóa học",
     *     @OA\JsonContent(
     *       type="object",
     *       @OA\Property(property="status", type="string", example="error"),
     *       @OA\Property(property="message", type="string", example="Không tìm thấy khóa học cần thanh toán")
     *     )
     *   ),
     *   @OA\Response(
     *     response=423,
     *     description="Khóa học đã sở hữu hoặc trạng thái khóa học không hợp lệ",
     *     @OA\JsonContent(
     *       type="object",
     *       @OA\Property(property="status", type="string", example="error"),
     *       @OA\Property(property="message", type="string", example="Khóa học đã sở hữu không cần thanh toán")
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
    // Tạo giao dịch
    public function createPayment(Request $request, $course_id)
    {

        // Bắt đầu transaction để đảm bảo tính toàn vẹn dữ liệu
        DB::beginTransaction();

        try {

            $own_course = $request->user()->courses()->find($course_id);
            $course = Course::lockForUpdate()->findOrFail($course_id);
            $is_free = $course->is_free;

            if ($is_free == 0) {

                // Kiểm tra khóa học đã được sở hữu chưa
                if ($own_course) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Khóa học đã sở hữu không cần thanh toán'
                    ], Response::HTTP_LOCKED);
                }
                // Kiểm tra khóa học có tồn tại không
                if (!$course) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Không tìm thấy khóa học cần thanh toán'
                    ], Response::HTTP_NOT_FOUND);
                }
                // Kiểm tra trạng thái của khóa học có hợp lệ không
                if ($course->status === "pending" || $course->status === "draft") {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Trạng thái khóa học không hợp lệ'
                    ], Response::HTTP_LOCKED);
                }
                // Kiểm tra người dùng đã tham gia khóa học chưa
                $Enrolled = Enrollment::where('user_id', $request->user()->id)
                    ->where('course_id', $course_id)
                    ->first();
                if ($Enrolled) {
                    return response()->json([
                        'error' => 'Bạn đã tham gia khóa học'
                    ], Response::HTTP_BAD_REQUEST);
                }
                // Kiểm tra giao dịch có bị trùng lặp không
                $existingTransaction = Transaction::where('user_id', $request->user()->id)
                    ->where('course_id', $course_id)
                    ->whereIn('status', ['pending', 'success'])
                    ->first();
                if ($existingTransaction) {
                    return response()->json([
                        'error' => 'Giao dịch thất bại'
                    ], Response::HTTP_BAD_REQUEST);
                }

                // Kiểm tra dữ liệu truyền lên
                $validator = Validator::make($request->all(), [
                    'amount' => 'required|int',
                ]);
                if ($validator->fails()) {
                    return response()->json([
                        'errors' => $validator->errors()
                    ], Response::HTTP_UNPROCESSABLE_ENTITY);
                }

                // Lấy thông tin cấu hình từ .env
                $vnp_TmnCode = env('VNP_TMN_CODE');
                $vnp_HashSecret = env('VNP_HASH_SECRET');
                $vnp_Url = env('VNP_URL'); // URL của môi trường test hoặc production
                $vnp_Returnurl = str_replace('{course_id}', $course_id, env('VNP_RETURN_URL'));

                // Tạo các tham số thanh toán
                $vnp_TxnRef = $request->user()->id . "_" . time();
                $vnp_OrderInfo = "Thanh toán khóa học";
                $vnp_OrderType = "education";
                $vnp_Amount = $request->input('amount', 0) * 100; // Số tiền (nhân với 100) để loại bỏ phần thập phân
                $vnp_Locale = "vn";
                $vnp_BankCode = $request->input('bank_code', "");
                $vnp_IpAddr = $request->ip();

                // Mảng các tham số gửi lên VNPAY
                $inputData = [
                    "vnp_Version" => "2.1.0",
                    "vnp_TmnCode" => $vnp_TmnCode,
                    "vnp_Amount" => $vnp_Amount,
                    "vnp_Command" => "pay",
                    "vnp_CreateDate" => date('YmdHis'),
                    "vnp_CurrCode" => "VND",
                    "vnp_IpAddr" => $vnp_IpAddr,
                    "vnp_Locale" => $vnp_Locale,
                    "vnp_OrderInfo" => $vnp_OrderInfo,
                    "vnp_OrderType" => $vnp_OrderType,
                    "vnp_ReturnUrl" => $vnp_Returnurl,
                    "vnp_TxnRef" => $vnp_TxnRef,
                ];

                if (!empty($vnp_BankCode)) {
                    $inputData['vnp_BankCode'] = $vnp_BankCode;
                }

                // Sắp xếp mảng theo thứ tự key
                ksort($inputData);
                // Tạo chuỗi hash data
                $hashdata = "";
                $query = "";
                $i = 0;
                foreach ($inputData as $key => $value) {
                    if ($i == 1) {
                        $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
                    } else {
                        $hashdata .= urlencode($key) . "=" . urlencode($value);
                        $i = 1;
                    }
                    $query .= urlencode($key) . "=" . urlencode($value) . '&';
                }

                // Tạo mã hash bảo mật
                $vnpSecureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);
                $paymentUrl = $vnp_Url . "?" . $query . 'vnp_SecureHash=' . $vnpSecureHash;

                // Trả về URL thanh toán cho client
                return response()->json([
                    'status' => 'success',
                    'payment_url' => $paymentUrl
                ], Response::HTTP_CREATED);

            } else {

                // Kiểm tra khóa học đã được sở hữu chưa
                if ($own_course) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Khóa học đã sở hữu không cần thanh toán'
                    ], Response::HTTP_LOCKED);
                }
                // Kiểm tra khóa học có tồn tại không
                if (!$course) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Không tìm thấy khóa học cần thanh toán'
                    ], Response::HTTP_NOT_FOUND);
                }
                // Kiểm tra trạng thái của khóa học có hợp lệ không
                if ($course->status === "pending" || $course->status === "draft") {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Trạng thái khóa học không hợp lệ'
                    ], Response::HTTP_LOCKED);
                }
                // Kiểm tra người dùng đã tham gia khóa học chưa
                $Enrolled = Enrollment::where('user_id', $request->user()->id)
                    ->where('course_id', $course_id)
                    ->first();
                if ($Enrolled) {
                    return response()->json([
                        'error' => 'Bạn đã tham gia khóa học'
                    ], Response::HTTP_BAD_REQUEST);
                }
                // Kiểm tra giao dịch có bị trùng lặp không
                $existingTransaction = Transaction::where('user_id', $request->user()->id)
                    ->where('course_id', $course_id)
                    ->whereIn('status', ['pending', 'success'])
                    ->first();
                if ($existingTransaction) {
                    return response()->json([
                        'error' => 'Giao dịch thất bại'
                    ], Response::HTTP_BAD_REQUEST);
                }

                Transaction::create([
                    'user_id' => $request->user()->id,
                    'course_id' => $course_id,
                    'amount' => 0,
                    'payment_method' => 'bank_transfer',
                    'status' => 'success',
                    'transaction_date' => Carbon::now('Asia/Ho_Chi_Minh')
                ]);
                Enrollment::create([
                    'user_id' => $request->user()->id,
                    'course_id' => $course_id,
                    'status' => 'active',
                    'enrolled_at' => Carbon::now('Asia/Ho_Chi_Minh')
                ]);
                Progress::create([
                    'user_id' => $request->user()->id,
                    'course_id' => $course_id,
                    'status' => 'in_progress',
                    'progress_percent' => 0
                ]);

                DB::commit(); // Commit transaction

                return response()->json([
                    'message' => 'Thanh toán thành công!'
                ], Response::HTTP_OK);

            }

        } catch (\Throwable $th) {
            DB::rollBack(); // Rollback transaction nếu có lỗi
            return response()->json([
                'message' => 'Lỗi server',
                'error' => $th->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    /**
     * @OA\Get(
     *     path="/user/courses/{course_id}/payment-callback",
     *     tags={"VNPay"},
     *     summary="Xử lý callback từ VNPay",
     *     description="API này nhận kết quả thanh toán từ VNPay và cập nhật trạng thái giao dịch.",
     *     security={{"BearerAuth": {}}},
     *     @OA\Parameter(
     *       name="course_id",
     *       in="path",
     *       description="ID của khóa học",
     *       required=true,
     *       @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="vnp_Amount",
     *         in="query",
     *         description="Số tiền thanh toán",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="vnp_BankCode",
     *         in="query",
     *         description="Mã ngân hàng",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="vnp_CardType",
     *         in="query",
     *         description="Loại thẻ",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="vnp_OrderInfo",
     *         in="query",
     *         description="Thông tin đơn hàng",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="vnp_ResponseCode",
     *         in="query",
     *         description="Mã phản hồi từ VNPAY (00 là thành công)",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="vnp_TmnCode",
     *         in="query",
     *         description="Mã merchant ID",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="vnp_TxnRef",
     *         in="query",
     *         description="Mã giao dịch",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="vnp_SecureHash",
     *         in="query",
     *         description="Hash bảo mật",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Thanh toán thành công",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Thanh toán thành công!")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Thanh toán thất bại hoặc đã tham gia vào khóa học",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Thanh toán thất bại!")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Lỗi server",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Lỗi server"),
     *             @OA\Property(property="error", type="string", example="Chi tiết lỗi")
     *         )
     *     )
     * )
     */
    // Trả về thông tin giao dịch
    public function paymentCallback(Request $request, $course_id)
    {
        // $vnp_SecureHash = $request->query('vnp_SecureHash'); // Hash từ VNPAY
        // $inputData = $request->except(['vnp_SecureHash']);

        // // Tạo hash để kiểm tra
        // ksort($inputData);
        // $hashData = "";
        // foreach ($inputData as $key => $value) {
        //     if ($key != "vnp_SecureHash" && $value != null) {
        //         $hashData .= $key . "=" . $value . "&";
        //     }
        // }
        // $hashData = rtrim($hashData, "&");

        // // Tạo hash kiểm tra với secret key
        // $secureHash = hash_hmac('sha512', $hashData, env('VNP_HASH_SECRET'));

        // if ($secureHash === $vnp_SecureHash) { // Xác thực chữ ký hợp lệ

        // } else {
        //     return response()->json(['message' => 'Chữ ký không hợp lệ!'], 400);
        // }

        // Bắt đầu transaction để đảm bảo tính toàn vẹn dữ liệu
        DB::beginTransaction();

        try {

            $user_id = $request->query('vnp_TxnRef');
            list($user_id, $timestamp) = explode('_', $request->query('vnp_TxnRef'));
            // Kiểm tra người dùng đã tham gia khóa học chưa
            $Enrolled = Enrollment::where('user_id', $user_id)
                ->where('course_id', $course_id)
                ->first();
            if ($Enrolled) {
                return response()->json([
                    'error' => 'Bạn đã tham gia khóa học'
                ], Response::HTTP_BAD_REQUEST);
            }
            // Kiểm tra giao dịch có bị trùng lặp không
            $existingTransaction = Transaction::where('user_id', $user_id)
                ->where('course_id', $course_id)
                ->whereIn('status', ['pending', 'success'])
                ->first();
            if ($existingTransaction) {
                return response()->json([
                    'error' => 'Lỗi giao dịch'
                ], Response::HTTP_BAD_REQUEST);
            }

            response()->json([
                $request->query('vnp_ResponseCode')
            ]);

            $amount = $request->query('vnp_Amount') / 100;

            if ($request->query('vnp_ResponseCode') == "00") {

                Transaction::create([
                    'user_id' => $user_id,
                    'course_id' => $course_id,
                    'amount' => $amount,
                    'payment_method' => 'bank_transfer',
                    'status' => 'success',
                    'transaction_date' => Carbon::now('Asia/Ho_Chi_Minh')
                ]);
                Enrollment::create([
                    'user_id' => $user_id,
                    'course_id' => $course_id,
                    'status' => 'active',
                    'enrolled_at' => Carbon::now('Asia/Ho_Chi_Minh')
                ]);
                Progress::create([
                    'user_id' => $user_id,
                    'course_id' => $course_id,
                    'status' => 'in_progress',
                    'progress_percent' => 0
                ]);
                // Lấy tất cả sections của khóa học
                $sections = Section::where('course_id', $course_id)->with('lessons')->get();

                // Duyệt qua từng section và khởi tạo tiến trình cho từng lesson
                foreach ($sections as $section) {
                    foreach ($section->lessons as $lesson) {
                        Completion::create([
                            'user_id' => $user_id,
                            'course_id' => $course_id,
                            'lesson_id' => $lesson->id,
                            'status' => 'in_progress',
                            'created_at' => Carbon::now('Asia/Ho_Chi_Minh')
                        ]);
                    }
                }

                DB::commit(); // Commit transaction

                return redirect('http://localhost:5173/student/MyCourse');

            } else {

                DB::rollBack(); // Rollback transaction nếu có lỗi
                return redirect("http://localhost:5173/student/home/$course_id/coursedetail?status=error");

            }
        } catch (\Throwable $th) {
            DB::rollBack(); // Rollback transaction nếu có lỗi
            return response()->json([
                'message' => 'Lỗi server',
                'error' => $th->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

    }
}
