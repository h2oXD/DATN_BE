<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\TransactionWallet;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TransactionWalletController extends Controller
{
    // Xuất lịch sử nạp tiền
    public function walletHistory(Request $request)
    {
        try {

            $wallet_id = $request->user()->wallet->id;
            
            $histories = TransactionWallet::where('wallet_id', $wallet_id)
                            ->orderBy('transaction_date', 'desc')
                            ->get();
            
            return response()->json([
                'message'     => 'Lịch sử ví',
                'histories'   => $histories
            ], Response::HTTP_OK);
            
        } catch (\Throwable $th) {
            return response()->json([
                'message'   => 'Lỗi server',
                'error'     => $th->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    // Xuất lịch sử nạp tiền
    public function depositHistory(Request $request)
    {
        try {

            $wallet_id = $request->user()->wallet->id;
            
            $histories = TransactionWallet::where('wallet_id', $wallet_id)
                            ->where('type', 'deposit')
                            ->orderBy('transaction_date', 'desc')
                            ->get();
            
            return response()->json([
                'message'     => 'Lịch sử nạp tiền',
                'histories'   => $histories
            ], Response::HTTP_OK);
            
        } catch (\Throwable $th) {
            return response()->json([
                'message'   => 'Lỗi server',
                'error'     => $th->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    // Xuất lịch sử rút tiền
    public function withdrawHistory(Request $request)
    {
        try {

            $wallet_id = $request->user()->wallet->id;
            
            $histories = TransactionWallet::where('wallet_id', $wallet_id)
                            ->where('type', 'withdraw')
                            ->orderBy('transaction_date', 'desc')
                            ->get();
            
            return response()->json([
                'message'     => 'Lịch sử rút tiền',
                'histories'   => $histories
            ], Response::HTTP_OK);
            
        } catch (\Throwable $th) {
            return response()->json([
                'message'   => 'Lỗi server',
                'error'     => $th->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

}
