<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Complain;
use App\Models\TransactionWallet;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class ComplainController extends Controller
{
    // Gửi yêu cầu rút tiền
    public function complain(Request $request, $transaction_wallet_id)
    {
        DB::beginTransaction();
        try {

            $wallet                     = $request->user()->wallet;
            $transaction                = TransactionWallet::lockForUpdate()->findOrFail($transaction_wallet_id);
            $transaction_status         = $transaction->status;
            $transaction_type           = $transaction->type;
            $transaction_expired        = $transaction->time_limited_complain;
            $isset_complain             = Complain::where('transaction_wallet_id', $transaction->id)
                                            ->whereIn('status', ['resolved', 'rejected', 'pending'])
                                            ->get();
            $count_complain_canceled    = Complain::where('transaction_wallet_id', $transaction->id)
                                            ->where('status', 'canceled')
                                            ->get();
                                
            // Kiểm tra xem ví có tồn tại hay không
            if (!$wallet) {
                return response()->json([
                    'status'    => 'error',
                    'message'   => 'Không tìm thấy ví của người dùng.'
                ], Response::HTTP_NOT_FOUND);
            }
            // Kiểm tra xem lịch sử rút tiền có tồn tại hay không
            if (!$transaction) {
                return response()->json([
                    'status'    => 'error',
                    'message'   => 'Không tìm thấy lịch sử rút tiền.'
                ], Response::HTTP_NOT_FOUND);
            }
            // Kiểm tra xem lịch sử rút tiền có trạng thái hợp lệ hay không
            if ($transaction_status == 'pending' || $transaction_status == 'fail') {
                return response()->json([
                    'status'    => 'error',
                    'message'   => 'Trạng thái giao dịch không hợp lệ.'
                ], Response::HTTP_BAD_REQUEST);
            }
            // Kiểm tra xem lịch sử rút tiền có loại giao dịch hợp lệ hay không
            if ($transaction_type !== 'withdraw') {
                return response()->json([
                    'status'    => 'error',
                    'message'   => 'Loại giao dịch không hợp lệ.'
                ], Response::HTTP_BAD_REQUEST);
            }
            // Kiểm tra xem ngày hết hạn khiếu nại lịch sử rút tiền còn tác dụng không
            if (Carbon::now()->gt(Carbon::parse($transaction_expired))) {
                return response()->json([
                    'status'    => 'error',
                    'message'   => 'Đã hết thời gian gửi yêu cầu khiếu nại.'
                ], Response::HTTP_BAD_REQUEST);
            }
            // Kiểm tra xem yêu cầu khiếu nại tồn tại hay chưa
            if ($isset_complain->isNotEmpty()) {
                return response()->json([
                    'status'    => 'error',
                    'message'   => 'Yêu cầu khiếu nại của bạn đã được gửi hoặc được xác nhận trước đó.'
                ], Response::HTTP_BAD_REQUEST);
            }
            // Kiểm tra người dùng hủy mấy lần, nếu quá 2 lần sẽ không cho gửi
            if ($count_complain_canceled->count() > 1) {
                return response()->json([
                    'status'    => 'error',
                    'message'   => 'Bạn đã thực hiện yêu cầu quá số lần quy định.'
                ], Response::HTTP_TOO_MANY_REQUESTS);
            }

            // Kiểm tra dữ liệu truyền lên
            $validator = Validator::make($request->all(), [
                'description'    => 'required|string',
                'proof_img'      => 'required|image|max:2048',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'errors' => $validator->errors()
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            // Upload ảnh bằng chứng
            $data = $request->all();
            if ($request->hasFile('proof_img')) {
                $path = $request->file('proof_img')->store('proof_imgs');
                $data['proof_img'] = $path;
            }

            // Tạo yêu cầu khiếu nại
            Complain::create([
                'transaction_wallet_id' => $transaction_wallet_id,
                'status'                => 'pending',
                'description'           => $data['description'],
                'proof_img'             => $data['proof_img'],
                'request_date'          => Carbon::now('Asia/Ho_Chi_Minh')
            ]);
            // Thêm trạng thái khiếu nại cho giao dịch
            $transaction->update([
                'complain' => 1
            ]);

            DB::commit();
            return response()->json([
                'status' => 'success',
                'message' => 'Gửi yêu cầu khiếu nại thành công'
            ], Response::HTTP_OK);

        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'message'   => 'Lỗi server',
                'error'     => $th->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    // Danh sách yêu cầu khiếu nại
    public function listComplain(Request $request)
    {
        
        try {

            $wallet = $request->user()->wallet;
            $listComplain = Complain::whereHas('transaction_wallets', function($query) use ($wallet) 
                                        {
                                            $query->where('wallet_id', '=', $wallet->id);
                                        })
                                        ->orderByDesc('request_date')
                                        ->get();
                                
            // Kiểm tra xem ví có tồn tại hay không
            if (!$wallet) {
                return response()->json([
                    'status'    => 'error',
                    'message'   => 'Không tìm thấy ví của người dùng.'
                ], Response::HTTP_NOT_FOUND);
            }
            // Kiểm tra xem danh sách có lịch sử không
            if ($listComplain->isEmpty()) {
                return response()->json([
                    'status'    => 'error',
                    'message'   => 'Danh sách trống.'
                ], Response::HTTP_NOT_FOUND);
            }

            return response()->json([
                'status' => 'success',
                'Danh sách khiếu nại' => $listComplain
            ], Response::HTTP_OK);

        } catch (\Throwable $th) {
            return response()->json([
                'message'   => 'Lỗi server',
                'error'     => $th->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    // Chi tiết yêu cầu khiếu nại
    public function detailComplain(Request $request, $complain_id)
    {
        
        try {

            $wallet = $request->user()->wallet;
            $detailComplain = Complain::whereHas('transaction_wallets', function($query) use ($wallet) 
                                        {
                                            $query->where('wallet_id', '=', $wallet->id);
                                        })
                                        ->where('id', $complain_id)
                                        ->first();
                               
            // Kiểm tra xem ví có tồn tại hay không
            if (!$wallet) {
                return response()->json([
                    'status'    => 'error',
                    'message'   => 'Không tìm thấy ví của người dùng.'
                ], Response::HTTP_NOT_FOUND);
            }
            // Kiểm tra xem yêu cầu khiếu nại có tồn tại hay không
            if (!$detailComplain) {
                return response()->json([
                    'status'    => 'error',
                    'message'   => 'Không tồn tại yêu cầu khiếu nại.'
                ], Response::HTTP_NOT_FOUND);
            }

            return response()->json([
                'status' => 'success',
                'Chi tiết yêu cầu khiếu nại' => $detailComplain
            ], Response::HTTP_OK);

        } catch (\Throwable $th) {
            return response()->json([
                'message'   => 'Lỗi server',
                'error'     => $th->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function cancelComplain(Request $request, $complain_id)
    {
        DB::beginTransaction();
        try {

            $wallet = $request->user()->wallet;
            $detailComplain = Complain::lockForUpdate()->whereHas('transaction_wallets', function($query) use ($wallet) 
                                        {
                                            $query->where('wallet_id', '=', $wallet->id);
                                        })
                                        ->where('id', $complain_id)
                                        ->first();
            $transaction = TransactionWallet::where('wallet_id', $wallet->id)
                                            ->where('id', $detailComplain->transaction_wallet_id)
                                            ->lockForUpdate()
                                            ->firstOrFail();
                               
            // Kiểm tra xem ví có tồn tại hay không
            if (!$wallet) {
                return response()->json([
                    'status'    => 'error',
                    'message'   => 'Không tìm thấy ví của người dùng.'
                ], Response::HTTP_NOT_FOUND);
            }
            // Kiểm tra xem yêu cầu khiếu nại có tồn tại hay không
            if (!$detailComplain) {
                return response()->json([
                    'status'    => 'error',
                    'message'   => 'Không tồn tại yêu cầu khiếu nại.'
                ], Response::HTTP_NOT_FOUND);
            }
            // Kiểm tra xem khiếu nại đã được admin xác nhận chưa
            if ($detailComplain->status == 'resolved' || $detailComplain->status == 'rejected') {
                return response()->json([
                    'status'    => 'error',
                    'message'   => 'Yêu cầu khiếu nại của bạn đã được xác nhận, không thể thao tác.'
                ], Response::HTTP_NOT_FOUND);
            }
            // Kiểm tra xem khiếu nại đã hủy trước đó chưa
            if ($detailComplain->status == 'canceled') {
                return response()->json([
                    'status'    => 'error',
                    'message'   => 'Yêu cầu khiếu nại của bạn đã được thao tác trước đó.'
                ], Response::HTTP_NOT_FOUND);
            }

            // Hủy yêu cầu khiếu nại và sửa trạng thái khiếu nại bên lịch sử giao dịch
            $detailComplain->update([
                'status' => 'canceled'
            ]);
            $transaction->update([
                'complain' => 0
            ]);

            DB::commit();
            return response()->json([
                'status' => 'success',
                'message' => 'Bạn đã hủy thành công yêu cầu khiếu nại'
            ], Response::HTTP_OK);

        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'message'   => 'Lỗi server',
                'error'     => $th->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

}
