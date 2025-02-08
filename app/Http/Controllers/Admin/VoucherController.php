<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\AdminBaseController;
use App\Models\Voucher;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

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
        $request->validate($this->updateValidate($id),$this->updateMessage());
        $data = $request->all();

        if ($request->has('discount_percent') && $request->input('discount_percent') !== null) {
            $data['discount_amount'] = null;
        }elseif ($request->has('discount_amount') && $request->input('discount_amount') !== null) {
            $data['discount_percent'] = null;
        }

        $item->update($data);

        return redirect()->route($this->routePath)->with('success', 'Cập nhật thành công!');
    }

    protected function storeValidate(){
        return[
            'name'                  => ['required', 'string', 'max:255'],
            'code'                  => ['required', 'max:20', Rule::unique('vouchers')],
            'description'           => ['required', 'max:255'],
            'type'                  => ['required'],
            'discount_percent'      => ['nullable', 'integer', 'min:1', 'max:100'],
            'discount_amount'       => ['nullable', 'integer', 'min:1', 'max:99999999'],
            'start_time'            => ['required', 'date', 'after_or_equal:now'],
            'end_time'              => ['required', 'date', 'after:start_time'],
            'count'                 => ['required', 'integer', 'min:0', 'max:9999'],
            'is_active'             => ['required']
        ];
    }

    public function storeMessage()
    {
        return [
            'name.required'               => 'Tên phiếu giảm giá không được để trống.',
            'name.string'                 => 'Tên phiếu giảm giá phải là chuỗi ký tự.',
            'name.max'                    => 'Tên phiếu giảm giá không được vượt quá 255 ký tự.',

            'code.required'               => 'Mã giảm giá không được để trống.',
            'code.max'                    => 'Mã giảm giá không được vượt quá 20 ký tự.',
            'code.unique'                 => 'Mã giảm giá đã tồn tại.',

            'description.required'        => 'Mô tả không được để trống.',
            'description.max'             => 'Mô tả không được vượt quá 255 ký tự.',

            'type.required'               => 'Loại giảm giá không được để trống.',

            'discount_percent.integer'    => 'Phần trăm giảm giá phải là số nguyên.',
            'discount_percent.min'        => 'Phần trăm giảm giá phải lớn hơn hoặc bằng 1.',
            'discount_percent.max'        => 'Phần trăm giảm giá không được vượt quá 100.',

            'discount_amount.integer'     => 'Số tiền giảm giá phải là số nguyên.',
            'discount_amount.min'         => 'Số tiền giảm giá phải lớn hơn hoặc bằng 1 VND.',
            'discount_amount.max'         => 'Số tiền giảm giá không được vượt quá 99.999.999 VND.',

            'start_time.required'         => 'Thời gian bắt đầu không được để trống.',
            'start_time.after_or_equal'   => 'Thời gian bắt đầu phải từ thời điểm hiện tại trở đi.',

            'end_time.required'           => 'Thời gian kết thúc không được để trống.',
            'end_time.after'              => 'Thời gian kết thúc phải sau thời gian bắt đầu.',

            'count.required'              => 'Số lượng mã giảm giá không được để trống.',
            'count.min'                   => 'Số lượng mã giảm giá phải lớn hơn hoặc bằng 0.',

            'is_active.required'          => 'Trạng thái kích hoạt không được để trống.'
        ];
    }

    protected function updateValidate($id){
        return[
            'name'                  => ['required', 'string', 'max:255'],
            'code'                  => ['required', 'max:20', Rule::unique('vouchers')->ignore($id)],
            'description'           => ['required', 'max:255'],
            'type'                  => ['required'],
            'discount_percent'      => ['nullable', 'integer', 'min:1', 'max:100'],
            'discount_amount'       => ['nullable', 'integer', 'min:1', 'max:99999999'],
            'start_time'            => ['required', 'date', 'after_or_equal:now'],
            'end_time'              => ['required', 'date', 'after:start_time'],
            'count'                 => ['required', 'integer', 'min:0', 'max:9999'],
            'is_active'             => ['required']
        ];
    }
    
    public function updateMessage()
    {
        return [
            'name.required'               => 'Tên phiếu giảm giá không được để trống.',
            'name.string'                 => 'Tên phiếu giảm giá phải là chuỗi ký tự.',
            'name.max'                    => 'Tên phiếu giảm giá không được vượt quá 255 ký tự.',

            'code.required'               => 'Mã giảm giá không được để trống.',
            'code.max'                    => 'Mã giảm giá không được vượt quá 20 ký tự.',
            'code.unique'                 => 'Mã giảm giá đã tồn tại.',

            'description.required'        => 'Mô tả không được để trống.',
            'description.max'             => 'Mô tả không được vượt quá 255 ký tự.',

            'type.required'               => 'Loại giảm giá không được để trống.',

            'discount_percent.integer'    => 'Phần trăm giảm giá phải là số nguyên.',
            'discount_percent.min'        => 'Phần trăm giảm giá phải lớn hơn hoặc bằng 1.',
            'discount_percent.max'        => 'Phần trăm giảm giá không được vượt quá 100.',

            'discount_amount.integer'     => 'Số tiền giảm giá phải là số nguyên.',
            'discount_amount.min'         => 'Số tiền giảm giá phải lớn hơn hoặc bằng 1 VND.',
            'discount_amount.max'         => 'Số tiền giảm giá không được vượt quá 99.999.999 VND.',

            'start_time.required'         => 'Thời gian bắt đầu không được để trống.',
            'start_time.after_or_equal'   => 'Thời gian bắt đầu phải từ thời điểm hiện tại trở đi.',

            'end_time.required'           => 'Thời gian kết thúc không được để trống.',
            'end_time.after'              => 'Thời gian kết thúc phải sau thời gian bắt đầu.',

            'count.required'              => 'Số lượng mã giảm giá không được để trống.',
            'count.min'                   => 'Số lượng mã giảm giá phải lớn hơn hoặc bằng 0.',

            'is_active.required'          => 'Trạng thái kích hoạt không được để trống.'
        ];
    }
}
