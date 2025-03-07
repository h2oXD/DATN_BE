<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\AdminBaseController;
use App\Models\TransactionWallet;
use App\Models\Wallet;
use Carbon\Carbon;
use Illuminate\Http\Request;

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
        $transaction = TransactionWallet::findOrFail($id);
        $wallet         = Wallet::findOrFail($transaction->wallet_id);
        $balance_now    = $wallet->balance;

        $transaction->update([
            'balance'           => $balance_now,
            'status'            => 'success',
            'transaction_date'  => Carbon::now('Asia/Ho_Chi_Minh')
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
                'Ngày giao dịch'        => Carbon::now('Asia/Ho_Chi_Minh')
            ]
        ]);

        return redirect()->route($this->routePath)->with('success', 'Giao dịch đã được xác nhận.');
    }

    public function reject($id)
    {
        $transaction    = TransactionWallet::findOrFail($id);
        $wallet         = Wallet::findOrFail($transaction->wallet_id);
        $balance_now    = $wallet->balance;

        $transaction->update([
            'balance'           => $transaction->amount + $balance_now,
            'status'            => 'fail',
            'transaction_date'  => Carbon::now('Asia/Ho_Chi_Minh')
        ]);
        $wallet->update([
            'balance'               => $transaction->amount + $balance_now,
            'transaction_history'   => [
                'Loại giao dịch'        => 'Hoàn tiền rút thất bại',
                'Số tiền thanh toán'    => number_format($transaction->amount) . ' VND',
                'Số dư ví'              => number_format($transaction->amount + $balance_now) . ' VND',
                'Tên ngân hàng'         => $transaction->bank_name,
                'Tên người nhận'        => $transaction->bank_nameUser,
                'Số tài khoản'          => $transaction->bank_number,
                'Ngày giao dịch'        => Carbon::now('Asia/Ho_Chi_Minh')
            ]
        ]);

        return redirect()->route($this->routePath)->with('success', 'Giao dịch đã được xác nhận.');
    }
}
