<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'balance',
        'transaction_history',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function transaction_wallet()
    {
        return $this->hasMany(TransactionWallet::class);
    }
}
