<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\AdminBaseController;
use App\Models\Voucher;
use App\Models\VoucherUse;
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

    public function destroy($id)
    {
        $item = $this->model::findOrFail($id);

        VoucherUse::where('voucher_id', $item->id)->delete();

        $item->delete();

        return redirect()->route($this->routePath)->with('success', 'Xóa thành công!');
    }

    protected function storeValidate()
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'max:20', Rule::unique('vouchers')],
            'description' => ['required', 'max:255'],
            'type' => ['required', Rule::in(['percent', 'fix_amount'])], // Validate type
            'start_time' => ['required', 'date', 'after_or_equal:now'],
            'end_time' => ['required', 'date', 'after:start_time'],
            'count' => ['required', 'integer', 'min:0', 'max:9999'],
            'is_active' => ['required']
        ];

        // Conditional validation for discount_price
        if (request()->input('type') === 'percent') {
            $rules['discount_price'] = ['required', 'integer', 'min:1', 'max:100']; // Max 100 for percent
        } elseif (request()->input('type') === 'fix_amount') {
            $rules['discount_price'] = ['required', 'integer', 'min:1', 'max:10000000']; // No max limit for fix_amount
        } else {
            $rules['discount_price'] = ['nullable']; // if type not selected set discount_price nullable
        }

        return $rules;
    }

    public function storeMessage()
    {
        $message = [
            'name.required' => 'Tên phiếu giảm giá không được để trống.',
            'name.string' => 'Tên phiếu giảm giá phải là chuỗi ký tự.',
            'name.max' => 'Tên phiếu giảm giá không được vượt quá 255 ký tự.',

            'code.required' => 'Mã giảm giá không được để trống.',
            'code.max' => 'Mã giảm giá không được vượt quá 20 ký tự.',
            'code.unique' => 'Mã giảm giá đã tồn tại.',

            'description.required' => 'Mô tả không được để trống.',
            'description.max' => 'Mô tả không được vượt quá 255 ký tự.',

            'type.required' => 'Loại giảm giá không được để trống.',
            'type.in' => 'Loại giảm giá không hợp lệ.',

            'discount_price.required' => 'Giá trị giảm giá không được để trống.',
            'discount_price.integer' => 'Giá trị giảm giá phải là số nguyên.',
            'discount_price.min' => 'Giá trị giảm giá phải lớn hơn 0.',

            'start_time.required' => 'Thời gian bắt đầu không được để trống.',
            'start_time.after_or_equal' => 'Thời gian bắt đầu phải từ thời điểm hiện tại trở đi.',

            'end_time.required' => 'Thời gian kết thúc không được để trống.',
            'end_time.after' => 'Thời gian kết thúc phải sau thời gian bắt đầu.',

            'count.required' => 'Số lượng mã giảm giá không được để trống.',
            'count.min' => 'Số lượng mã giảm giá phải lớn hơn hoặc bằng 0.',

            'is_active.required' => 'Trạng thái kích hoạt không được để trống.'
        ];
        if (request()->input('type') === 'percent') {
            $message['discount_price.max'] = 'Giá trị giảm giá không được vượt quá 100 (cho phần trăm).';
        } elseif (request()->input('type') === 'fix_amount') {
            $message['discount_price.max'] = 'Giá trị giảm giá (số tiền) không được vượt quá 10,000,000.';
        }
        return $message;
    }

    protected function updateValidate($id)
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'max:20', Rule::unique('vouchers')->ignore($id)],
            'description' => ['required', 'max:255'],
            'type' => ['required'],
            'start_time' => ['required', 'date', 'after_or_equal:now'],
            'end_time' => ['required', 'date', 'after:start_time'],
            'count' => ['required', 'integer', 'min:0', 'max:9999'],
            'is_active' => ['required']
        ];
        if (request()->input('type') === 'percent') {
            $rules['discount_price'] = ['required', 'integer', 'min:1', 'max:100']; // Max 100 for percent
        } elseif (request()->input('type') === 'fix_amount') {
            $rules['discount_price'] = ['required', 'integer', 'min:1', 'max:10000000']; // No max limit for fix_amount
        } else {
            $rules['discount_price'] = ['nullable']; // if type not selected set discount_price nullable
        }
        return $rules;
    }

    public function updateMessage()
    {
        $message = [
            'name.required' => 'Tên phiếu giảm giá không được để trống.',
            'name.string' => 'Tên phiếu giảm giá phải là chuỗi ký tự.',
            'name.max' => 'Tên phiếu giảm giá không được vượt quá 255 ký tự.',

            'code.required' => 'Mã giảm giá không được để trống.',
            'code.max' => 'Mã giảm giá không được vượt quá 20 ký tự.',
            'code.unique' => 'Mã giảm giá đã tồn tại.',

            'description.required' => 'Mô tả không được để trống.',
            'description.max' => 'Mô tả không được vượt quá 255 ký tự.',

            'type.required' => 'Loại giảm giá không được để trống.',
            'discount_price.min' => 'Giá trị giảm giá phải lớn hơn 0.',

            'start_time.required' => 'Thời gian bắt đầu không được để trống.',
            'start_time.after_or_equal' => 'Thời gian bắt đầu phải từ thời điểm hiện tại trở đi.',

            'end_time.required' => 'Thời gian kết thúc không được để trống.',
            'end_time.after' => 'Thời gian kết thúc phải sau thời gian bắt đầu.',

            'count.required' => 'Số lượng mã giảm giá không được để trống.',
            'count.min' => 'Số lượng mã giảm giá phải lớn hơn hoặc bằng 0.',

            'is_active.required' => 'Trạng thái kích hoạt không được để trống.'
        ];
        if (request()->input('type') === 'percent') {
            $message['discount_price.max'] = 'Giá trị giảm giá không được vượt quá 100 (cho phần trăm).';
        } elseif (request()->input('type') === 'fix_amount') {
            $message['discount_price.max'] = 'Giá trị giảm giá (số tiền) không được vượt quá 10,000,000.';
        }
        return $message;
    }
}
