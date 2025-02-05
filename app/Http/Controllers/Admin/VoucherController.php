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

}
