<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VoucherUse extends Model
{
    use HasFactory;

    protected $fillable = [
        'voucher_id',
        'user_id',
        'applied_at',
        'expired_at',
        'is_used',
    ];
}
