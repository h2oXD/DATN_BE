<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class VNPayAPIController extends Controller
{
    // Tạo giao dịch
    public function createPayment(Request $request)
    {
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
        ]);
    }

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
