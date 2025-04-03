<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\AdminBaseController;
use App\Models\Complain;
use App\Models\TransactionWallet;
use App\Models\Wallet;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ComplainController extends AdminBaseController
{
    
    public function __construct()
    {
        $this->model = Complain::class;
        $this->viewPath = 'admins.complains.';
        $this->routePath = 'admin.censor-complain.index';
    }

    public function index(Request $request)
    {
        $query = Complain::with('transaction_wallets.wallet.user')
                ->where('status', 'pending');

        // Lấy dữ liệu từ request
        $search = $request->input('search');
        $category = $request->input('category');

        // Danh sách các cột có thể tìm kiếm
        $columns = [
            'user_name' => 'Tên người dùng',
            'email' => 'Email',
        ];

        // Áp dụng điều kiện tìm kiếm
        if ($search && array_key_exists($category, $columns)) {
            if ($category == 'user_name') {
                $query->whereHas('transaction_wallets.wallet.user', function ($q) use ($search) {
                    $q->where('name', 'LIKE', "%$search%");
                });
            } elseif ($category == 'email') {
                $query->whereHas('transaction_wallets.wallet.user', function ($q) use ($search) {
                    $q->where('email', 'LIKE', "%$search%");
                });
            }
        }

        $items = $query->orderBy('request_date', 'desc')->paginate(10);

        return view($this->viewPath . 'index', compact('items', 'columns'));
    }

    public function censor($id)
    {
        $complain = Complain::with('transaction_wallets.wallet.user')->find($id);

        return view($this->viewPath . 'show', compact('complain'));
    }

    public function accept(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            
            $complain       = Complain::where('id', $id)->lockForUpdate()->firstOrFail();
            $transaction    = TransactionWallet::where('id', $complain->transaction_wallet_id)->lockForUpdate()->firstOrFail();
            $wallet         = Wallet::where('id', $transaction->wallet_id)->lockForUpdate()->firstOrFail();
            $balance_now    = $wallet->balance;

            $validator = Validator::make($request->all(), [
                'money_refund'          => 'required|integer|min:1|max:100000000',
                'feedback_by_admin'     => 'required|string|max:255',
            ], [
                'money_refund.required'         => 'Số tiền hoàn trả không được để trống.',
                'money_refund.integer'          => 'Số tiền hoàn trả phải là 1 số.',
                'money_refund.min'              => 'Số tiền hoàn trả nhỏ nhất là 1.',
                'money_refund.max'              => 'Số tiền hoàn trả lớn nhất là 100.000.000.',
                'feedback_by_admin.required'    => 'Nội dung phản hồi không được để trống.',
                'feedback_by_admin.string'      => 'Nội dung phản hồi phải là một chuỗi.',
                'feedback_by_admin.max'         => 'Nội dung phản hồi quá dài.',
            ]);
        
            if ($validator->fails()) {
                return response()->json([
                    'error' => $validator->errors()
                ], 422);
            }

            // Kiểm tra khiếu nại có tồn tại không
            if (!$complain) {
                return response()->json([
                    'error' => 'Khiếu nại không tồn tại'
                ], 404);
            }
            // Kiểm tra trạng thái trước khi duyệt
            if ($complain->status !== 'pending') {
                return response()->json([
                    'error' => 'Trạng thái kiểm duyệt đã được cập nhật trước đó'
                ], 422);
            }
            // Kiểm tra lịch sử giao dịch có tồn tại không
            if (!$transaction) {
                return response()->json([
                    'error' => 'Lịch sử giao dịch không tồn tại'
                ], 404);
            }
            // Kiểm tra ví có tồn tại không
            if (!$wallet) {
                return response()->json([
                    'error' => 'Ví người dùng không tồn tại'
                ], 404);
            }

            // Trả thông tin khiếu nại và sửa trạng thái khiếu nại của giao dịch
            $complain->update([
                'status'                    => 'resolved',
                'feedback_by_admin'         => $request->feedback_by_admin,
                'feedback_date'             => Carbon::now('Asia/Ho_Chi_Minh'),
            ]);
            $transaction->update([
                'complain'                  => 0,
            ]);

            // Hoàn tiền vào ví và tạo lịch sử giao dịch
            $wallet->update([
                'balance'               => $balance_now + $request->money_refund,
                'transaction_history'   => [
                    'Loại giao dịch'        => 'Hoàn tiền khiếu nại vào ví',
                    'Số tiền hoàn trả'      => number_format($request->money_refund) . ' VND',
                    'Số dư ví'              => number_format($wallet->balance + $request->money_refund) . ' VND',
                    'Ngày giao dịch'        => Carbon::now('Asia/Ho_Chi_Minh')
                ]
            ]);
            TransactionWallet::create([
                'wallet_id'         => $wallet->id,
                'transaction_code'  => Str::uuid(),
                'amount'            => $request->money_refund,
                'balance'           => $wallet->balance,
                'type'              => 'refund',
                'status'            => 'success',
                'transaction_date'  => Carbon::now('Asia/Ho_Chi_Minh')
            ]);

            DB::commit();
            return response()->json([
                'success' => 'Thao tác kiểm duyệt thành công'
            ], 200);
            
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'error' => 'Thao tác kiểm duyệt không thành công'
            ], 200);
        }
    }

    public function reject($id)
    {
        DB::beginTransaction();
        try {
            
            $complain       = Complain::where('id', $id)->lockForUpdate()->firstOrFail();
            $transaction    = TransactionWallet::where('id', $complain->transaction_wallet_id)->lockForUpdate()->firstOrFail();

            // Kiểm tra khiếu nại có tồn tại không
            if (!$complain) {
                return response()->json([
                    'error' => 'Khiếu nại không tồn tại'
                ], 404);
            }
            // Kiểm tra trạng thái trước khi duyệt
            if ($complain->status !== 'pending') {
                return response()->json([
                    'error' => 'Trạng thái kiểm duyệt đã được cập nhật trước đó'
                ], 422);
            }
            // Kiểm tra lịch sử giao dịch có tồn tại không
            if (!$transaction) {
                return response()->json([
                    'error' => 'Lịch sử giao dịch không tồn tại'
                ], 404);
            }

            // Trả thông tin khiếu nại và sửa trạng thái khiếu nại của giao dịch
            $complain->update([
                'status'                    => 'rejected',
                'feedback_by_admin'         => 'Khiếu nại của bạn đã bị từ chối',
                'feedback_date'             => Carbon::now('Asia/Ho_Chi_Minh'),
            ]);
            $transaction->update([
                'complain'                  => 0,
            ]);

            DB::commit();
            return redirect()->route($this->routePath)->with('success', 'Khiếu nại đã được xác nhận.');

        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->route($this->routePath)->with('error', 'Lỗi khi xử lý từ chối khiếu nại.');
        }
    }

    public function historyCensor(Request $request)
    {
        $query = Complain::with('transaction_wallets.wallet.user')
        ->whereIn('status', ['resolved', 'rejected']);

        // Lấy dữ liệu từ request
        $search = $request->input('search');
        $category = $request->input('category');

        // Danh sách các cột có thể tìm kiếm
        $columns = [
            'user_name' => 'Tên người dùng',
            'email'     => 'Email',
            'status'    => 'Kết quả duyệt',
        ];

        // Áp dụng điều kiện tìm kiếm
        if ($search && array_key_exists($category, $columns)) {
            if ($category == 'user_name') {
                $query->whereHas('transaction_wallets.wallet.user', function ($q) use ($search) {
                    $q->where('name', 'LIKE', "%$search%");
                });
            } elseif ($category == 'email') {
                $query->whereHas('transaction_wallets.wallet.user', function ($q) use ($search) {
                    $q->where('email', 'LIKE', "%$search%");
                });
            } elseif ($category == 'status') {
                if (str_contains(strtolower($search), 'xác nhận')) {
                    $query->where('status', 'resolved');
                } elseif (str_contains(strtolower($search), 'từ chối')) {
                    $query->where('status', 'rejected');
                }
            }
        }

        $items = $query->orderBy('feedback_date', 'desc')->paginate(10);

        return view($this->viewPath . 'history', compact('items', 'columns'));
    }

    public function detailHistory($id)
    {
        $complain = Complain::with('transaction_wallets.wallet.user')->find($id);

        return view($this->viewPath . 'detail', compact('complain'));
    }

}
