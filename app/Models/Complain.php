<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Complain extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_wallet_id',
        'status',
        'description',
        'proof_img',
        'request_date',
        'feedback_by_admin',
        'feedback_date'
    ];

    public function transaction_wallets()
    {
        return $this->belongsTo(TransactionWallet::class, 'transaction_wallet_id');
    }
}
