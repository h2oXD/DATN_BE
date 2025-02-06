<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

class Voucher extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
        'type',
        'discount_percent',
        'discount_amount',
        'start_time',
        'end_time',
        'count',
        'is_active',
    ];

    public static function rules($id = null) {
        return[
            'name'                  => 'required|max:255',
            'code'                  => 'required|max:255|unique:vouchers,code,'.$id,
            'description'           => 'required|max:255',
            'type'                  => 'required',
            'discount_percent'      => 'nullable',
            'discount_amount'       => 'nullable',
            'start_time'            => 'required',
            'end_time'              => 'required',
            'count'                 => 'required',
            'is_active'             => 'required',
        ];
    }
}
