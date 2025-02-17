<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VNPayAPIController extends Controller
{

    /**
     * @OA\Post(
     *     path="/user/create-payment",
     *     tags={"Thanh toán"},
     *     summary="Tạo yêu cầu thanh toán VNPAY",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="amount", type="integer", description="Số tiền thanh toán (ví dụ: 10000 VND)", example=10000),
     *             @OA\Property(property="bank_code", type="string", description="Mã ngân hàng (ví dụ: NCB, VISA, JCB)", example="NCB")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Tạo yêu cầu thanh toán thành công",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="payment_url", type="string", example="https://sandbox.vnpayment.vn/paymentv2/vpcpay.html?...")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Lỗi",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Lỗi tạo yêu cầu thanh toán")
     *         )
     *     ),
     *     @OA\Response(
     *          response=401,
     *          description="Unauthorized - Chưa đăng nhập hoặc token không hợp lệ",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Unauthenticated.")
     *          )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Lỗi máy chủ"
     *     )
     * )
     */
    // Tạo giao dịch
    public function createPayment(Request $request)
    {
        try {
            // Lấy thông tin cấu hình từ .env
            $vnp_TmnCode = env('VNP_TMN_CODE');
            $vnp_HashSecret = env('VNP_HASH_SECRET');
            $vnp_Url = env('VNP_URL'); // URL của môi trường test hoặc production
            $vnp_Returnurl = env('VNP_RETURN_URL');

            // Tạo các tham số thanh toán
            $vnp_TxnRef = time() . ""; // Mã giao dịch duy nhất
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
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Lỗi server',
                'error' => $th->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    /**
     * @OA\Get(
     *     path="/user/payment-callback",
     *     tags={"Thanh toán"},
     *     summary="Xử lý callback từ VNPAY",
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
     *         description="Callback xử lý thành công",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Thanh toán thành công!")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Callback xử lý thất bại",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Thanh toán thất bại!")
     *         )
     *     )
     * )
     */
    // Trả về thông tin giao dịch
    public function paymentCallback(Request $request)
    {
        $vnp_SecureHash = $request->query('vnp_SecureHash'); // Hash từ VNPAY
        $inputData = $request->except(['vnp_SecureHash']);

        // Tạo hash để kiểm tra
        ksort($inputData);
        $hashData = "";
        foreach ($inputData as $key => $value) {
            if ($key != "vnp_SecureHash" && $value != null) {
                $hashData .= $key . "=" . $value . "&";
            }
        }
        $hashData = rtrim($hashData, "&");

        // Tạo hash kiểm tra với secret key
        $secureHash = hash_hmac('sha512', $hashData, env('VNP_HASH_SECRET'));

        response()->json([
            $request->query('vnp_ResponseCode')
        ]);

        if ($secureHash === $vnp_SecureHash) { // Xác thực chữ ký hợp lệ
            if ($request->query('vnp_ResponseCode') == "00") {
                return response()->json(['message' => 'Thanh toán thành công!'], 200);
            } else {
                return response()->json(['message' => 'Thanh toán thất bại!'], 400);
            }
        } else {
            return response()->json(['message' => 'Chữ ký không hợp lệ!'], 400);
        }
    }
}
