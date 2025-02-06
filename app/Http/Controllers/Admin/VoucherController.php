<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\AdminBaseController;
use App\Models\Voucher;
use Illuminate\Http\Request;

class VoucherController extends AdminBaseController
{
    public function __construct()
    {
        $this->model = Voucher::class;
        $this->viewPath = 'admins.vouchers.';
        $this->routePath = 'vouchers.index';
    }

    public function update(Request $request, $id)
    {
        $item = $this->model::findOrFail($id);
        $data = $request->validate($this->model::rules($id));

        if ($request->has('discount_percent') && $request->input('discount_percent') !== null) {
            $data['discount_amount'] = null;
        }elseif ($request->has('discount_amount') && $request->input('discount_amount') !== null) {
            $data['discount_percent'] = null;
        }

        $item->update($data);

        return redirect()->route($this->routePath)->with('success', 'Cập nhật thành công!');
    }
}
