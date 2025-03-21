<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Progress;
use App\Models\Transaction;
use App\Models\TransactionWallet;
use App\Models\Wallet;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class WalletController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * @OA\Get(
     *   path="/user/wallets",
     *   tags={"Wallet"},
     *   summary="Lấy thông tin ví của người dùng",
     *   description="API này trả về thông tin chi tiết về ví của người dùng đã xác thực.",
     *   security={{"BearerAuth": {}}},
     *   @OA\Response(
     *     response=200,
     *     description="Thành công - Trả về thông tin ví",
     *     @OA\JsonContent(
     *       type="object",
     *       @OA\Property(property="status", type="string", example="success"),
     *       @OA\Property(
     *         property="wallet",
     *         type="object",
     *         @OA\Property(property="id", type="integer", example=1),
     *         @OA\Property(property="user_id", type="integer", example=2),
     *         @OA\Property(property="balance", type="number", example=999000),
     *         @OA\Property(property="transaction_history", type="string", example=null),
     *         @OA\Property(property="created_at", type="string", example=null),
     *         @OA\Property(property="updated_at", type="string", example=null),
     *       )
     *     )
     *   ),
     *   @OA\Response(
     *     response=404,
     *     description="Không tìm thấy ví",
     *     @OA\JsonContent(
     *       type="object",
     *       @OA\Property(property="status", type="string", example="error"),
     *       @OA\Property(property="message", type="string", example="Không tìm thấy ví của người dùng này.")
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
    public function show(Request $request)
    {
        try {

            $wallet = $request->user()->wallet;

            // Kiểm tra xem ví có tồn tại hay không
            if (!$wallet) {
                return response()->json([
                    'status'    => 'error',
                    'message'   => 'Không tìm thấy ví của người dùng này.'
                ], Response::HTTP_NOT_FOUND);
            }

            return response()->json([
                'status' => 'success',
                'wallet' => $wallet
            ], Response::HTTP_OK);

        } catch (\Throwable $th) {
            return response()->json([
                'message'   => 'Lỗi server',
                'error'     => $th->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\Put(
     *   path="/user/wallets",
     *   tags={"Wallet"},
     *   summary="Api này để backend test, bên frontend không dùng",
     *   description="Api này để backend test, bên frontend không dùng",
     *   security={{"BearerAuth": {}}},
     *   @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(
     *       type="object",
     *       @OA\Property(property="balance", type="integer", example=15000, description="Số dư mới của ví")
     *     )
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Ví được cập nhật",
     *     @OA\JsonContent(
     *       type="object",
     *       @OA\Property(property="status", type="string", example="Ví được cập nhật"),
     *       @OA\Property(
     *         property="wallet",
     *         type="object",
     *         @OA\Property(property="id", type="integer", example=1),
     *         @OA\Property(property="user_id", type="integer", example=2),
     *         @OA\Property(property="balance", type="number", example=99000),
     *         @OA\Property(property="transaction_history", type="string", example=null),
     *         @OA\Property(property="created_at", type="string", example=null),
     *         @OA\Property(property="updated_at", type="string", example=null),
     *       )
     *     )
     *   ),
     *   @OA\Response(
     *     response=400,
     *     description="Lỗi validation",
     *     @OA\JsonContent(
     *       type="object",
     *       @OA\Property(property="errors", type="object", description="Các lỗi validation")
     *     )
     *   ),
     *   @OA\Response(
     *     response=404,
     *     description="Không tìm thấy ví",
     *     @OA\JsonContent(
     *       type="object",
     *       @OA\Property(property="status", type="string", example="error"),
     *       @OA\Property(property="message", type="string", example="Không tìm thấy ví của người dùng này.")
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
    public function update(Request $request)
    {
        try {

            $wallet = $request->user()->wallet;

            // Kiểm tra xem ví có tồn tại hay không
            if (!$wallet) {
                return response()->json([
                    'status'    => 'error',
                    'message'   => 'Không tìm thấy ví của người dùng này.'
                ], Response::HTTP_NOT_FOUND);
            }

            // Validate dữ liệu truyền vào
            $validator = Validator::make($request->all(), [
                'balance' => 'required|int',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'errors' => $validator->errors()
                ], Response::HTTP_BAD_REQUEST);
            }
            $data = $request->all();

            $wallet->update($data);

            return response()->json([
                'status' => 'Ví được cập nhật',
                'wallet' => $wallet
            ], Response::HTTP_OK);

        } catch (\Throwable $th) {
            return response()->json([
                'message'   => 'Lỗi server',
                'error'     => $th->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    /**
     * @OA\Post(
     *   path="/user/courses/{course_id}/wallet-payment",
     *   tags={"Wallet"},
     *   summary="Thanh toán khóa học bằng ví",
     *   description="API này cho phép người dùng thanh toán khóa học bằng số dư trong ví (trừ khóa học miễn phí).",
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
     *       @OA\Property(property="amount", type="integer", example=10000, description="Số tiền cần thanh toán")
     *     )
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Thanh toán thành công / Khóa học miễn phí, đã tự động đăng ký",
     *     @OA\JsonContent(
     *       type="object",
     *       @OA\Property(property="status", type="string", example="success"),
     *       @OA\Property(property="message", type="string", example="Thanh toán thành công / Khóa học miễn phí, đã tự động đăng ký")
     *     )
     *   ),
     *   @OA\Response(
     *     response=400,
     *     description="Lỗi validation / Số dư ví không đủ / Đã tham gia khóa học",
     *     @OA\JsonContent(
     *       type="object",
     *       @OA\Property(property="errors", type="object", description="Các lỗi validation"),
     *       @OA\Property(property="error", type="string", example="Số dư ví không đủ / Bạn đã tham gia khóa học")
     *     )
     *   ),
     *   @OA\Response(
     *     response=404,
     *     description="Không tìm thấy ví hoặc khóa học",
     *     @OA\JsonContent(
     *       type="object",
     *       @OA\Property(property="status", type="string", example="error"),
     *       @OA\Property(property="message", type="string", example="Không tìm thấy ví của người dùng này")
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
    public function payment(Request $request, $course_id)
    {

        // Bắt đầu transaction để đảm bảo tính toàn vẹn dữ liệu
        DB::beginTransaction();

        try {

            $wallet = $request->user()->wallet()->lockForUpdate()->first();
            $own_course = $request->user()->courses()->find($course_id);
            $course = Course::lockForUpdate()->findOrFail($course_id);
            $is_free = $course->is_free;
            $lecturer_id = $course->user; // id giảng viên khóa học

            // Kiểm tra giảng viên có tồn tại không
            if (!$lecturer_id) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Không tìm thấy giảng viên của khóa học này'
                ], Response::HTTP_NOT_FOUND);
            }
            // Lấy ví của giảng viên
            $lecturer_wallet = $lecturer_id->wallet;
            if (!$lecturer_wallet) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Không tìm thấy ví của giảng viên'
                ], Response::HTTP_NOT_FOUND);
            }

            if ($is_free == 0) {
                
                // Kiểm tra ví có tồn tại hay không
                if (!$wallet) {
                    return response()->json([
                        'status'    => 'error',
                        'message'   => 'Không tìm thấy ví của người dùng này'
                    ], Response::HTTP_NOT_FOUND);
                }
                // Kiểm tra khóa học đã được sở hữu chưa
                if ($own_course) {
                    return response()->json([
                        'status'    => 'error',
                        'message'   => 'Khóa học đã sở hữu không cần thanh toán'
                    ], Response::HTTP_LOCKED);
                }
                // Kiểm tra khóa học có tồn tại không
                if (!$course) {
                    return response()->json([
                        'status'    => 'error',
                        'message'   => 'Không tìm thấy khóa học cần thanh toán'
                    ], Response::HTTP_NOT_FOUND);
                }
                // Kiểm tra trạng thái của khóa học có hợp lệ không
                if ($course->status === "pending" || $course->status === "draft") {
                    return response()->json([
                        'status'    => 'error',
                        'message'   => 'Trạng thái khóa học không hợp lệ'
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

                // Kiểm tra số dư ví người dùng
                if ($wallet->balance < $request->amount) {
                    return response()->json([
                        'error' => 'Số dư ví không đủ'
                    ], Response::HTTP_BAD_REQUEST);
                }

                // Trừ tiền trong ví sinh viên, ghi lịch sử giao dịch vào database và tham gia khóa học
                $wallet->decrement('balance', $request->amount);
                $wallet->update([
                    'transaction_history' => [
                        'Loại giao dịch'        => 'Thanh toán khóa học',
                        'Số tiền thanh toán'    => number_format($request->amount) . ' VND',
                        'Số dư'                 => number_format($wallet->balance) . ' VND',
                        'Ngày giao dịch'        => Carbon::now('Asia/Ho_Chi_Minh')
                    ]
                ]);
                Transaction::create([
                    'user_id'           => $request->user()->id,
                    'course_id'         => $course_id,
                    'amount'            => $request->amount,
                    'payment_method'    => 'wallet',
                    'status'            => 'success',
                    'transaction_date'  => Carbon::now('Asia/Ho_Chi_Minh')
                ]);
                TransactionWallet::create([
                    'wallet_id'         => $wallet->id,
                    'transaction_code'  => Str::uuid(),
                    'amount'            => $request->amount,
                    'balance'           => $wallet->balance,
                    'type'              => 'payment',
                    'status'            => 'success',
                    'transaction_date'  => Carbon::now('Asia/Ho_Chi_Minh')
                ]);
                Enrollment::create([
                    'user_id'       => $request->user()->id,
                    'course_id'     => $course_id,
                    'status'        => 'active',
                    'enrolled_at'   => Carbon::now('Asia/Ho_Chi_Minh')
                ]);
                Progress::create([
                    'user_id'           => $request->user()->id,
                    'course_id'         => $course_id,
                    'status'            => 'in_progress',
                    'progress_percent'  => 0
                ]);

                
                // Cộng tiền 70% lợi nhuận bán khóa học vào ví giảng viên, cập nhật lịch sử
                $profit = $request->amount * 0.7;
                $lecturer_wallet->increment('balance', $profit);
                $lecturer_wallet->update([
                    'transaction_history' => [
                        'Loại giao dịch'        => 'Nhận lợi nhuận bán khóa học',
                        'Số tiền nhận'          => number_format($profit) . ' VND',
                        'Số dư'                 => number_format($lecturer_wallet->balance) . ' VND',
                        'Ngày giao dịch'        => Carbon::now('Asia/Ho_Chi_Minh')
                    ]
                ]);
                TransactionWallet::create([
                    'wallet_id'         => $lecturer_wallet->id,
                    'transaction_code'  => Str::uuid(),
                    'amount'            => $profit,
                    'balance'           => $lecturer_wallet->balance,
                    'type'              => 'profit',
                    'status'            => 'success',
                    'transaction_date'  => Carbon::now('Asia/Ho_Chi_Minh')
                ]);

                DB::commit(); // Commit transaction

                return response()->json([
                    'status' => 'success',
                    'message' => 'Thanh toán thành công!'
                ], Response::HTTP_OK);

            } else {

                // Kiểm tra khóa học đã được sở hữu chưa
                if ($own_course) {
                    return response()->json([
                        'status'    => 'error',
                        'message'   => 'Khóa học đã sở hữu không cần thanh toán'
                    ], Response::HTTP_LOCKED);
                }
                // Kiểm tra khóa học có tồn tại không
                if (!$course) {
                    return response()->json([
                        'status'    => 'error',
                        'message'   => 'Không tìm thấy khóa học cần thanh toán'
                    ], Response::HTTP_NOT_FOUND);
                }
                // Kiểm tra trạng thái của khóa học có hợp lệ không
                if ($course->status === "pending" || $course->status === "draft") {
                    return response()->json([
                        'status'    => 'error',
                        'message'   => 'Trạng thái khóa học không hợp lệ'
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
                
                // Ghi lịch sử giao dịch vào database và tham gia khóa học
                Transaction::create([
                    'user_id'           => $request->user()->id,
                    'course_id'         => $course_id,
                    'amount'            => 0,
                    'payment_method'    => 'wallet',
                    'status'            => 'success',
                    'transaction_date'  => Carbon::now('Asia/Ho_Chi_Minh')
                ]);
                Enrollment::create([
                    'user_id'       => $request->user()->id,
                    'course_id'     => $course_id,
                    'status'        => 'active',
                    'enrolled_at'   => Carbon::now('Asia/Ho_Chi_Minh')
                ]);
                Progress::create([
                    'user_id'           => $request->user()->id,
                    'course_id'         => $course_id,
                    'status'            => 'in_progress',
                    'progress_percent'  => 0
                ]);

                DB::commit(); // Commit transaction

                return response()->json([
                    'status' => 'success',
                    'message' => 'Thanh toán thành công!'
                ], Response::HTTP_OK);

            }

        } catch (\Throwable $th) {
            DB::rollBack(); // Rollback transaction nếu có lỗi
            return response()->json([
                'message'   => 'Lỗi server',
                'error'     => $th->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\Post(
     *   path="/user/wallets/deposit",
     *   tags={"Wallet"},
     *   summary="Nạp tiền vào ví",
     *   description="API này cho phép người dùng nạp tiền vào ví thông qua VNPay.",
     *   security={{"BearerAuth": {}}},
     *   @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(
     *       type="object",
     *       @OA\Property(property="amount", type="integer", example=10000, description="Số tiền cần nạp"),
     *       @OA\Property(property="bank_code", type="string", example="NCB", description="Mã ngân hàng (ví dụ: NCB)")
     *     )
     *   ),
     *   @OA\Response(
     *     response=201,
     *     description="Tạo yêu cầu nạp tiền thành công",
     *     @OA\JsonContent(
     *       type="object",
     *       @OA\Property(property="status", type="string", example="success"),
     *       @OA\Property(property="payment_url", type="string", example="https://sandbox.vnpayment.vn/paymentv2/vpcpay.html?vnp_Amount=...", description="URL thanh toán VNPay")
     *     )
     *   ),
     *   @OA\Response(
     *     response=400,
     *     description="Lỗi validation",
     *     @OA\JsonContent(
     *       type="object",
     *       @OA\Property(property="errors", type="object", description="Các lỗi validation")
     *     )
     *   ),
     *   @OA\Response(
     *     response=404,
     *     description="Không tìm thấy ví",
     *     @OA\JsonContent(
     *       type="object",
     *       @OA\Property(property="status", type="string", example="error"),
     *       @OA\Property(property="message", type="string", example="Không tìm thấy ví của người dùng này")
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
    // Nạp tiền vào ví
    public function depositPayment(Request $request)
    {
        try {
            
            $wallet = $request->user()->wallet()->lockForUpdate()->first();

            // Kiểm tra ví có tồn tại hay không
            if (!$wallet) {
                return response()->json([
                    'status'    => 'error',
                    'message'   => 'Không tìm thấy ví của người dùng này'
                ], Response::HTTP_NOT_FOUND);
            }

            // Kiểm tra dữ liệu truyền lên
            $validator = Validator::make($request->all(), [
                'amount'    => 'required|int',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'errors' => $validator->errors()
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            // Lấy thông tin cấu hình từ .env
            $vnp_TmnCode    = env('VNP_TMN_CODE');
            $vnp_HashSecret = env('VNP_HASH_SECRET');
            $vnp_Url        = env('VNP_URL'); // URL của môi trường test hoặc production
            $vnp_Returnurl  = env('VNP_RETURN_URL_FOR_DEPOSIT');

            // Tạo các tham số thanh toán
            $vnp_TxnRef     = $request->user()->id . "_" . time();
            $vnp_OrderInfo  = "Thanh toán khóa học";
            $vnp_OrderType  = "education";
            $vnp_Amount     = $request->input('amount', 0) * 100; // Số tiền (nhân với 100) để loại bỏ phần thập phân
            $vnp_Locale     = "vn";
            $vnp_BankCode   = $request->input('bank_code', "");
            $vnp_IpAddr     = $request->ip();

            // Mảng các tham số gửi lên VNPAY
            $inputData = [
                "vnp_Version"       => "2.1.0",
                "vnp_TmnCode"       => $vnp_TmnCode,
                "vnp_Amount"        => $vnp_Amount,
                "vnp_Command"       => "pay",
                "vnp_CreateDate"    => date('YmdHis'),
                "vnp_CurrCode"      => "VND",
                "vnp_IpAddr"        => $vnp_IpAddr,
                "vnp_Locale"        => $vnp_Locale,
                "vnp_OrderInfo"     => $vnp_OrderInfo,
                "vnp_OrderType"     => $vnp_OrderType,
                "vnp_ReturnUrl"     => $vnp_Returnurl,
                "vnp_TxnRef"        => $vnp_TxnRef,
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
                'status'        => 'success',
                'payment_url'   => $paymentUrl
            ], Response::HTTP_CREATED);
            
        } catch (\Throwable $th) {
            return response()->json([
                'message'   => 'Lỗi server',
                'error'     => $th->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\Get(
     *   path="/user/wallets/result",
     *   tags={"Wallet"},
     *   summary="Xử lý kết quả nạp tiền vào ví từ VNPay",
     *   description="API này nhận kết quả nạp tiền từ VNPay và cập nhật số dư ví.",
     *   security={{"BearerAuth": {}}},
     *   @OA\Response(
     *     response=200,
     *     description="Nạp tiền thành công",
     *     @OA\JsonContent(
     *       type="object",
     *       @OA\Property(property="message", type="string", example="Thanh toán thành công!"),
     *       @OA\Property(property="Số tiền giao dịch", type="string", example="10,000 VND")
     *     )
     *   ),
     *   @OA\Response(
     *     response=400,
     *     description="Lỗi giao dịch / Giao dịch đã được xử lý trước đó",
     *     @OA\JsonContent(
     *       type="object",
     *       @OA\Property(property="message", type="string", example="Giao dịch đã được xử lý trước đó.")
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
    // Trả về kết quả thanh toán
    public function resultPaymemt(Request $request)
    {

        // Bắt đầu transaction để đảm bảo tính toàn vẹn dữ liệu
        DB::beginTransaction();

        try {

            $user_id = $request->query('vnp_TxnRef');
            list($user_id, $timestamp) = explode('_', $request->query('vnp_TxnRef'));
            $wallet = Wallet::where('user_id', $user_id)->first();
            $amount = $request->query('vnp_Amount') / 100;
            $transactionCode = $request->query('vnp_BankTranNo').$request->query('vnp_PayDate').$request->query('vnp_SecureHash');

            if ($request->query('vnp_ResponseCode') == "00") {

                // Kiểm tra giao dịch đã tồn tại chưa
                $existingTransaction = TransactionWallet::where('transaction_code', $transactionCode)->first();
                if ($existingTransaction) {
                    return response()->json([
                        'message' => 'Giao dịch đã được xử lý trước đó.'
                    ], Response::HTTP_BAD_REQUEST);
                }

                // Cộng tiền vào ví
                $wallet->increment('balance', $amount);
                $wallet->update([
                    'transaction_history' => [
                        'Loại giao dịch'        => 'Nạp tiền vào ví',
                        'Số tiền thanh toán'    => number_format($amount) . ' VND',
                        'Số dư'                 => number_format($wallet->balance) . ' VND',
                        'Ngày giao dịch'        => Carbon::now('Asia/Ho_Chi_Minh')
                    ]
                ]);

                // Tạo lịch sử giao dịch
                TransactionWallet::create([
                    'wallet_id'             => $wallet->id,
                    'transaction_code'      => $transactionCode,
                    'amount'                => $amount,
                    'balance'               => $wallet->balance,
                    'type'                  => 'deposit',
                    'status'                => 'success',
                    'transaction_date'      => Carbon::now('Asia/Ho_Chi_Minh')
                ]);

                DB::commit(); // Commit transaction

                return redirect('http://localhost:5173/student/walletStudent?status=success');

            } else {
                DB::rollBack(); // Rollback transaction nếu có lỗi
                return redirect('http://localhost:5173/student/walletStudent?status=failed');
            }

        } catch (\Throwable $th) {
            DB::rollBack(); // Rollback transaction nếu có lỗi
            return response()->json([
                'message'   => 'Lỗi server',
                'error'     => $th->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\Post(
     *   path="/lecturer/wallets/withdraw",
     *   tags={"Wallet"},
     *   summary="Rút tiền từ ví giảng viên",
     *   description="API này cho phép giảng viên rút tiền từ ví về tài khoản ngân hàng của họ. Chỉ tài khoản có vai trò 'lecturer' mới được phép rút tiền.",
     *   security={{"BearerAuth": {}}},
     *   @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(
     *       type="object",
     *       required={"amount", "bank_name", "bank_nameUser", "bank_number"},
     *       @OA\Property(property="amount", type="integer", example=500000, description="Số tiền cần rút (tối thiểu 10,000 VND)"),
     *       @OA\Property(property="bank_name", type="string", example="MBank", description="Tên ngân hàng"),
     *       @OA\Property(property="bank_nameUser", type="string", example="Nguyen Van A", description="Tên chủ tài khoản ngân hàng"),
     *       @OA\Property(property="bank_number", type="integer", example=123456789, description="Số tài khoản ngân hàng"),
     *       @OA\Property(property="qr_image", type="string", format="binary", description="Hình ảnh mã QR (tùy chọn, tối đa 2MB)")
     *     )
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Gửi yêu cầu rút tiền thành công",
     *     @OA\JsonContent(
     *       type="object",
     *       @OA\Property(property="status", type="string", example="success"),
     *       @OA\Property(property="message", type="string", example="Gửi yêu cầu rút tiền thành công!")
     *     )
     *   ),
     *   @OA\Response(
     *     response=403,
     *     description="Không có quyền rút tiền",
     *     @OA\JsonContent(
     *       type="object",
     *       @OA\Property(property="status", type="string", example="error"),
     *       @OA\Property(property="message", type="string", example="Chỉ tài khoản giảng viên mới có thể rút tiền.")
     *     )
     *   ),
     *   @OA\Response(
     *     response=404,
     *     description="Không tìm thấy ví",
     *     @OA\JsonContent(
     *       type="object",
     *       @OA\Property(property="status", type="string", example="error"),
     *       @OA\Property(property="message", type="string", example="Không tìm thấy ví của người dùng này.")
     *     )
     *   ),
     *   @OA\Response(
     *     response=400,
     *     description="Lỗi số tiền rút hoặc số dư không đủ",
     *     @OA\JsonContent(
     *       type="object",
     *       @OA\Property(property="status", type="string", example="error"),
     *       @OA\Property(property="message", type="string", example="Số tiền rút ít nhất là 10,000 VND / Số dư không đủ để rút tiền.")
     *     )
     *   ),
     *   @OA\Response(
     *     response=422,
     *     description="Lỗi dữ liệu đầu vào không hợp lệ",
     *     @OA\JsonContent(
     *       type="object",
     *       @OA\Property(property="errors", type="object", description="Danh sách các lỗi validation")
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
    public function withdraw(Request $request)
    {
        DB::beginTransaction();

        try {
            
            $user           = $request->user();
            $wallet         = $user->wallet()->lockForUpdate()->first();
            $amount         = $request->input('amount');
            $bank_code      = $request->input('bank_name');
            $bank_nameUser  = $request->input('bank_nameUser');
            $bank_number    = $request->input('bank_number');
            
            // Kiểm tra người dùng có vai trò giảng viên hay không
            if (!$user->roles()->where('name', 'lecturer')->exists()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Chỉ tài khoản giảng viên mới có thể rút tiền.'
                ], Response::HTTP_FORBIDDEN);
            }
            // Kiểm tra ví có tồn tại không
            if (!$wallet) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Không tìm thấy ví của người dùng này.'
                ], Response::HTTP_NOT_FOUND);
            }
            // Kiểm tra số tiền rút có hợp lệ không
            if (!is_numeric($amount) || $amount <= 10000) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Số tiền rút ít nhất là 10,000 VND'
                ], Response::HTTP_BAD_REQUEST);
            }
            // Kiểm tra số dư ví có đủ không
            if ($wallet->balance < $amount) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Số dư không đủ để rút tiền.'
                ], Response::HTTP_BAD_REQUEST);
            }

            // Kiểm tra dữ liệu truyền lên
            $validator = Validator::make($request->all(), [
                'amount'            => 'required|int',
                'bank_name'         => 'required|string',
                'bank_nameUser'     => 'required|string',
                'bank_number'       => 'required|int',
                'qr_image'          => 'nullable|image|max:2048',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'errors' => $validator->errors()
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            // Upload mã QR code
            $data = $request->all();
            if ($request->hasFile('qr_image')) {
                $path = $request->file('qr_image')->store('qr_images');
                $data['qr_image'] = $path;
            }

            // Trừ tiền khỏi ví và lưu thông tin tài khoản người dùng
            $wallet->decrement('balance', $amount);
            $wallet->update([
                'transaction_history' => [
                    'Loại giao dịch'        => 'Gửi yêu cầu rút tiền',
                    'Số tiền thanh toán'    => number_format($amount) . ' VND',
                    'Số dư ví'              => number_format($wallet->balance) . ' VND',
                    'Mã ngân hàng'          => $bank_code,
                    'Tên người nhận'        => $bank_nameUser,
                    'Số tài khoản'          => $bank_number,
                    'Ngày giao dịch'        => Carbon::now('Asia/Ho_Chi_Minh')
                ]
            ]);

            // Ghi lại lịch sử giao dịch
            if ( isset($request['qr_image']) ) {
                
                TransactionWallet::create([
                    'wallet_id'         => $wallet->id,
                    'transaction_code'  => Str::uuid(), // Mã giao dịch duy nhất
                    'amount'            => $amount,
                    'balance'           => $wallet->balance,
                    'type'              => 'withdraw',
                    'status'            => 'pending',
                    'bank_name'         => $bank_code,
                    'bank_nameUser'     => $bank_nameUser,
                    'bank_number'       => $bank_number,
                    'qr_image'          => $data['qr_image'],
                    'transaction_date'  => Carbon::now('Asia/Ho_Chi_Minh')
                ]);

            } else {
                
                TransactionWallet::create([
                    'wallet_id'         => $wallet->id,
                    'transaction_code'  => Str::uuid(), // Mã giao dịch duy nhất
                    'amount'            => $amount,
                    'balance'           => $wallet->balance,
                    'type'              => 'withdraw',
                    'status'            => 'pending',
                    'bank_name'         => $bank_code,
                    'bank_nameUser'     => $bank_nameUser,
                    'bank_number'       => $bank_number,
                    'transaction_date'  => Carbon::now('Asia/Ho_Chi_Minh')
                ]);
                
            }
            
            DB::commit(); // Lưu thay đổi vào database
            return response()->json([
                'status' => 'success',
                'message' => 'Gửi yêu cầu rút tiền thành công!'
            ], Response::HTTP_OK);
            
        } catch (\Throwable $th) {
            DB::rollBack(); // Rollback transaction nếu có lỗi
            return response()->json([
                'message'   => 'Lỗi server',
                'error'     => $th->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

}
