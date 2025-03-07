<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionWallet extends Model
{
    use HasFactory;

    protected $fillable = [
        'wallet_id',
        'transaction_code',
        'amount',
        'balance',
        'type',
        'status',
        'bank_name',
        'bank_nameUser',
        'bank_number',
        'qr_image',
        'transaction_date'
    ];

    public function wallet()
    {
        return $this->belongsTo(Wallet::class, 'wallet_id');
    }
}
