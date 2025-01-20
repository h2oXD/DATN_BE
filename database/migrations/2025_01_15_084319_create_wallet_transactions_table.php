<?php

use App\Models\User;
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
        Schema::create('wallet_transactions', function (Blueprint $table) {
            $table->id();
            
            $table->foreignIdFor(User::class)->constrained()->onDelete('cascade');
            $table->decimal('amount', 10,2);
            $table->enum('type', ['deposit','withdrawal','refund'])->comment('Loại giao dịch: deposit->nạp tiền vào ví, withdrawal->rút tiền, refund->hoàn trả tiền vào ví');
            $table->integer('reference_id');
            $table->integer('description')->nullable()->default(NULL);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wallet_transactions');
    }
};
