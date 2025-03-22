<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\AdminBaseController;
use App\Models\TransactionWallet;
use App\Models\Wallet;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WalletController extends AdminBaseController
{
    public function __construct()
    {
        $this->model = TransactionWallet::class;
        $this->viewPath = 'admins.withdraws.';
        $this->routePath = 'admin.censor-withdraw.index';
    }

    // Xuất danh sách yêu cầu rút tiền
    public function index()
    {
        $items = TransactionWallet::with('wallet.user')
                    ->where('type', 'withdraw')
                    ->where('status', 'pending')
                    ->orderBy('transaction_date', 'desc')
                    ->get();

        return view($this->viewPath . __FUNCTION__, compact('items'));
    }

    // Xem thông tin chi tiết và kiểm duyệt
    public function censor($id)
    {
        $transaction = TransactionWallet::with('wallet.user')->find($id);

        return view($this->viewPath . 'show', compact('transaction'));
    }

    public function accept($id)
    {
        DB::beginTransaction();
        try {
            
            $transaction    = TransactionWallet::where('id', $id)->lockForUpdate()->firstOrFail();
            $wallet         = Wallet::where('id', $transaction->wallet_id)->lockForUpdate()->firstOrFail();
            $balance_now    = $wallet->balance;

            // Kiểm tra trạng thái trước khi duyệt
            if ($transaction->status !== 'pending') {
                return redirect()->route($this->routePath)->with('error', 'Giao dịch đã được xử lý trước đó.');
            }

            $transaction->update([
                'balance'                   => $balance_now,
                'status'                    => 'success',
                'censor_date'               => Carbon::now('Asia/Ho_Chi_Minh'),
                'note'                      => 'Rút tiền thành công',
                'time_limited_complain'     => Carbon::now('Asia/Ho_Chi_Minh')->addDays(1)
            ]);
            $wallet->update([
                'balance'               => $balance_now,
                'transaction_history'   => [
                    'Loại giao dịch'        => 'Rút tiền thành công',
                    'Số tiền thanh toán'    => number_format($transaction->amount) . ' VND',
                    'Số dư ví'              => number_format($balance_now) . ' VND',
                    'Tên ngân hàng'         => $transaction->bank_name,
                    'Tên người nhận'        => $transaction->bank_nameUser,
                    'Số tài khoản'          => $transaction->bank_number,
                    'Ngày giao dịch'        => $transaction->transaction_date,
                    'Ngày kiểm duyệt'       => Carbon::now('Asia/Ho_Chi_Minh')
                ]
            ]);

            DB::commit();
            return redirect()->route($this->routePath)->with('success', 'Giao dịch đã được xác nhận.');
            
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->route($this->routePath)->with('error', 'Lỗi khi xử lý giao dịch.');
        }
    }

    public function reject($id)
    {
        DB::beginTransaction();
        try {
            
            $transaction    = TransactionWallet::where('id', $id)->lockForUpdate()->firstOrFail();
            $wallet         = Wallet::where('id', $transaction->wallet_id)->lockForUpdate()->firstOrFail();
            $balance_now    = $wallet->balance;

            // Kiểm tra trạng thái trước khi từ chối
            if ($transaction->status !== 'pending') {
                return redirect()->route($this->routePath)->with('error', 'Giao dịch đã được xử lý trước đó.');
            }

            $transaction->update([
                'balance'           => $transaction->amount + $balance_now,
                'status'            => 'fail',
                'censor_date'       => Carbon::now('Asia/Ho_Chi_Minh'),
                'note'              => 'Thông tin ngân hàng không chính xác, tiền rút được hoàn trả vào ví'
            ]);
            $wallet->update([
                'balance'               => $transaction->amount + $balance_now,
                'transaction_history'   => [
                    'Loại giao dịch'        => 'Yêu cầu rút tiền không được chấp nhận',
                    'Số tiền thanh toán'    => number_format($transaction->amount) . ' VND',
                    'Số dư ví'              => number_format($transaction->amount + $balance_now) . ' VND',
                    'Tên ngân hàng'         => $transaction->bank_name,
                    'Tên người nhận'        => $transaction->bank_nameUser,
                    'Số tài khoản'          => $transaction->bank_number,
                    'Ngày giao dịch'        => $transaction->transaction_date,
                    'Ngày kiểm duyệt'       => Carbon::now('Asia/Ho_Chi_Minh')
                ]
            ]);

        DB::commit();
        return redirect()->route($this->routePath)->with('success', 'Giao dịch đã được xác nhận.');

        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->route($this->routePath)->with('error', 'Lỗi khi xử lý từ chối giao dịch.');
        }
    }

    public function historyCensor()
    {
        $items = TransactionWallet::with('wallet.user')
                            ->whereIn('status', ['success', 'fail'])
                            ->where('type', 'withdraw')
                            ->orderBy('censor_date', 'desc')
                            ->get();

        return view($this->viewPath . 'history', compact('items'));
    }

    public function detailHistory($id)
    {
        $transaction = TransactionWallet::with('wallet.user')->find($id);

        return view($this->viewPath . 'detail', compact('transaction'));
    }
}
