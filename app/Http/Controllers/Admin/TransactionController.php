<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    const PATH_VIEW = 'admins.transactions.';

    public function index(Request $request)
    {
        $query = Transaction::select(
            'transactions.id',
            'users.name as user_name',
            'users.email as user_email',
            'courses.title as course_title',
            'transactions.amount',
            'transactions.payment_method',
            'transactions.status',
            'transactions.transaction_date'
        )
            ->join('users', 'transactions.user_id', '=', 'users.id')
            ->join('courses', 'transactions.course_id', '=', 'courses.id');

        $columns = [
            'users.name'                    => 'Tên người dùng',
            'users.email'                   => 'Email người dùng',
            'courses.title'                 => 'Tên khóa học',
            'transactions.payment_method'   => 'Phương thức thanh toán',
        ];

        $search = $request->input('search');
        $category = $request->input('category');

        if ($search && $category && array_key_exists($category, $columns)) {
            if ($category === 'transactions.payment_method') {
                $methodMapping = [
                    'Thẻ tín dụng'              => 'credit_card',
                    'Paypal'                    => 'paypal',
                    'Chuyển khoản ngân hàng'    => 'bank_transfer',
                    'Ví điện tử'                => 'wallet'
                ];
                if (array_key_exists($search, $methodMapping)) {
                    $query->where('transactions.payment_method', $methodMapping[$search]);
                }
            } else {
                $query->where($category, 'LIKE', "%{$search}%");
            }
        }

        $transactions = $query->paginate(10);

        return view(self::PATH_VIEW . 'index', compact('transactions', 'columns'));
    }

    public function show($id)
    {
        $item = Transaction::with(['user', 'course'])->findOrFail($id);

        return view(self::PATH_VIEW . 'show', compact('item'));
    }

}
