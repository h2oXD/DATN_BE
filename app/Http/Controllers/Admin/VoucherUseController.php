<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\AdminBaseController;
use App\Models\VoucherUse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class VoucherUseController extends AdminBaseController
{
    public function __construct()
    {
        $this->model = VoucherUse::class;
        $this->viewPath = 'admins.voucheruse.';
        $this->routePath = 'voucher-use.index';
    }

    public function index()
    {
        $items = VoucherUse::select(
            'voucher_uses.id',
            'vouchers.name as voucher_name',
            'vouchers.code',
            'vouchers.type',
            'vouchers.discount_price',
            'users.name as user_name',
            'users.email',
            'users.phone_number',
            'courses.title',
            'courses.price_regular',
            'courses.price_sale',
            'voucher_uses.time_used'
        )
            ->join('vouchers', 'voucher_uses.voucher_id', '=', 'vouchers.id')
            ->join('users', 'voucher_uses.user_id', '=', 'users.id')
            ->join('courses', 'voucher_uses.course_id', '=', 'courses.id')
            ->paginate(5);

        return view($this->viewPath . __FUNCTION__, compact('items'));
    }

    public function show($id)
    {
        $item = VoucherUse::with(['voucher', 'user', 'course'])->findOrFail($id);

        return view($this->viewPath . __FUNCTION__, compact('item'));
    }
}
