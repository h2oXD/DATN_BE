<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\TransactionWallet;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TransactionWalletController extends Controller
{
    /**
     * @OA\Get(
     * path="/user/wallet/histories",
     * summary="Lịch sử ví tiền",
     * description="Lấy lịch sử giao dịch ví tiền của người dùng.",
     * tags={"Wallet"},
     * security={{"sanctum": {}}},
     * @OA\Response(
     * response=200,
     * description="Lịch sử ví tiền của người dùng",
     * @OA\JsonContent(
     * @OA\Property(property="message", type="string", example="Lịch sử ví"),
     * @OA\Property(property="histories", type="array",
     * @OA\Items(type="object",
     * @OA\Property(property="id", type="integer", example=1),
     * @OA\Property(property="wallet_id", type="integer", example=1),
     * @OA\Property(property="type", type="string", example="deposit"),
     * @OA\Property(property="amount", type="number", example=100000),
     * @OA\Property(property="transaction_date", type="string", format="date-time", example="2025-03-21T10:00:00.000000Z"),
     * @OA\Property(property="censor_date", type="string", format="date-time", example="2025-03-21T10:00:00.000000Z"),
     * @OA\Property(property="bank_name", type="string", example="Vietcombank"),
     * @OA\Property(property="bank_nameUser", type="string", example="Nguyen Van A"),
     * @OA\Property(property="bank_number", type="string", example="123456789"),
     * @OA\Property(property="qr_image", type="string", example="qr_image.jpg"),
     * @OA\Property(property="balance", type="number", example=1000000),
     * @OA\Property(property="proof_img", type="string", example="proof_img.jpg"),
     * @OA\Property(property="note", type="string", example="Nạp tiền thành công"),
     * @OA\Property(property="status", type="string", example="success"),
     * @OA\Property(property="time_limited_complain", type="string", format="date-time", example="2025-03-22T10:00:00.000000Z"),
     * @OA\Property(property="created_at", type="string", format="date-time", example="2025-03-21T09:00:00.000000Z"),
     * @OA\Property(property="updated_at", type="string", format="date-time", example="2025-03-21T11:00:00.000000Z")
     * )
     * )
     * )
     * ),
     * @OA\Response(
     * response=401,
     * description="Xác thực không thành công",
     * @OA\JsonContent(
     * @OA\Property(property="message", type="string", example="Unauthenticated.")
     * )
     * ),
     * @OA\Response(
     * response=500,
     * description="Lỗi server",
     * @OA\JsonContent(
     * @OA\Property(property="message", type="string", example="Lỗi server"),
     * @OA\Property(property="error", type="string", example="Chi tiết lỗi")
     * )
     * )
     * )
     */
    public function walletHistory(Request $request)
    {
        try {

            $wallet_id = $request->user()->wallet->id;

            $histories = TransactionWallet::where('wallet_id', $wallet_id)
                ->orderBy('transaction_date', 'desc')
                ->get();

            return response()->json([
                'message' => 'Lịch sử ví',
                'histories' => $histories
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
     * path="/user/wallet/deposit-histories",
     * summary="Lịch sử nạp tiền",
     * description="Lấy lịch sử nạp tiền của người dùng.",
     * tags={"Wallet"},
     * security={{"sanctum": {}}},
     * @OA\Response(
     * response=200,
     * description="Lịch sử nạp tiền của người dùng",
     * @OA\JsonContent(
     * @OA\Property(property="message", type="string", example="Lịch sử nạp tiền"),
     * @OA\Property(property="histories", type="array",
     * @OA\Items(type="object",
     * @OA\Property(property="id", type="integer", example=1),
     * @OA\Property(property="wallet_id", type="integer", example=1),
     * @OA\Property(property="type", type="string", example="deposit"),
     * @OA\Property(property="amount", type="number", example=100000),
     * @OA\Property(property="transaction_date", type="string", format="date-time", example="2025-03-21T10:00:00.000000Z"),
     * @OA\Property(property="censor_date", type="string", format="date-time", example="2025-03-21T10:00:00.000000Z"),
     * @OA\Property(property="bank_name", type="string", example="Vietcombank"),
     * @OA\Property(property="bank_nameUser", type="string", example="Nguyen Van A"),
     * @OA\Property(property="bank_number", type="string", example="123456789"),
     * @OA\Property(property="qr_image", type="string", example="qr_image.jpg"),
     * @OA\Property(property="balance", type="number", example=1000000),
     * @OA\Property(property="proof_img", type="string", example="proof_img.jpg"),
     * @OA\Property(property="note", type="string", example="Nạp tiền thành công"),
     * @OA\Property(property="status", type="string", example="success"),
     * @OA\Property(property="time_limited_complain", type="string", format="date-time", example="2025-03-22T10:00:00.000000Z"),
     * @OA\Property(property="created_at", type="string", format="date-time", example="2025-03-21T09:00:00.000000Z"),
     * @OA\Property(property="updated_at", type="string", format="date-time", example="2025-03-21T11:00:00.000000Z")
     * )
     * )
     * )
     * ),
     * @OA\Response(
     * response=401,
     * description="Xác thực không thành công",
     * @OA\JsonContent(
     * @OA\Property(property="message", type="string", example="Unauthenticated.")
     * )
     * ),
     * @OA\Response(
     * response=500,
     * description="Lỗi server",
     * @OA\JsonContent(
     * @OA\Property(property="message", type="string", example="Lỗi server"),
     * @OA\Property(property="error", type="string", example="Chi tiết lỗi")
     * )
     * )
     * )
     */
    public function depositHistory(Request $request)
    {
        try {

            $wallet_id = $request->user()->wallet->id;

            $histories = TransactionWallet::where('wallet_id', $wallet_id)
                ->where('type', 'deposit')
                ->orderBy('transaction_date', 'desc')
                ->get();

            return response()->json([
                'message' => 'Lịch sử nạp tiền',
                'histories' => $histories
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
     * path="/lecturer/wallet/withdraw-histories",
     * summary="Lịch sử rút tiền",
     * description="Lấy lịch sử rút tiền của giảng viên.",
     * tags={"Wallet"},
     * security={{"sanctum": {}}},
     * @OA\Response(
     * response=200,
     * description="Lịch sử rút tiền của giảng viên",
     * @OA\JsonContent(
     * @OA\Property(property="message", type="string", example="Lịch sử rút tiền"),
     * @OA\Property(property="histories", type="array",
     * @OA\Items(type="object",
     * @OA\Property(property="id", type="integer", example=1),
     * @OA\Property(property="wallet_id", type="integer", example=1),
     * @OA\Property(property="type", type="string", example="withdraw"),
     * @OA\Property(property="amount", type="number", example=500000),
     * @OA\Property(property="transaction_date", type="string", format="date-time", example="2025-03-20T14:30:00.000000Z"),
     * @OA\Property(property="censor_date", type="string", format="date-time", example="2025-03-20T14:30:00.000000Z"),
     * @OA\Property(property="bank_name", type="string", example="Techcombank"),
     * @OA\Property(property="bank_nameUser", type="string", example="Tran Thi B"),
     * @OA\Property(property="bank_number", type="string", example="987654321"),
     * @OA\Property(property="qr_image", type="string", example=null),
     * @OA\Property(property="balance", type="number", example=500000),
     * @OA\Property(property="proof_img", type="string", example="proof_img_withdraw.jpg"),
     * @OA\Property(property="note", type="string", example="Rút tiền thành công"),
     * @OA\Property(property="status", type="string", example="success"),
     * @OA\Property(property="time_limited_complain", type="string", format="date-time", example="2025-03-21T14:30:00.000000Z"),
     * @OA\Property(property="created_at", type="string", format="date-time", example="2025-03-20T14:00:00.000000Z"),
     * @OA\Property(property="updated_at", type="string", format="date-time", example="2025-03-20T15:00:00.000000Z")
     * )
     * )
     * )
     * ),
     * @OA\Response(
     * response=401,
     * description="Xác thực không thành công",
     * @OA\JsonContent(
     * @OA\Property(property="message", type="string", example="Unauthenticated.")
     * )
     * ),
     * @OA\Response(
     * response=500,
     * description="Lỗi server",
     * @OA\JsonContent(
     * @OA\Property(property="message", type="string", example="Lỗi server"),
     * @OA\Property(property="error", type="string", example="Chi tiết lỗi")
     * )
     * )
     * )
     */
    public function withdrawHistory(Request $request)
    {
        try {

            $wallet_id = $request->user()->wallet->id;

            $histories = TransactionWallet::where('wallet_id', $wallet_id)
                ->where('type', 'withdraw')
                ->orderBy('transaction_date', 'desc')
                ->get();

            return response()->json([
                'message' => 'Lịch sử rút tiền',
                'histories' => $histories
            ], Response::HTTP_OK);

        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Lỗi server',
                'error' => $th->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

}
