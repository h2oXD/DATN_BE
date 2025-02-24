<?php

use App\Models\Wallet;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transaction_wallets', function (Blueprint $table) {
            $table->id();

            $table->foreignIdFor(Wallet::class)->constrained()->onDelete('cascade');
            $table->string('transaction_code')->unique()->comment('Mã giao dịch');
            $table->decimal('amount', 15, 2)->comment('Số tiền thanh toán');
            $table->decimal('balance', 15, 2)->comment('Số dư trong ví của người dùng');
            $table->enum('type', ['deposit', 'withdraw', 'profit', 'payment'])->comment('Nạp tiền, rút tiền, lợi nhuận, thanh toán');
            $table->enum('status', ['success', 'fail'])->comment('Trạng thái giao dịch');
            $table->timestamp('transaction_date')->comment('Ngày giao dịch');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaction_wallets');
    }
};
