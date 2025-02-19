<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('wallets', function (Blueprint $table) {
            $table->id();

            $table->foreignIdFor(User::class)->constrained()->onDelete('cascade');
            $table->integer('balance')->comment('Số dư trong ví của người dùng');
            $table->json('transaction_history')->nullable()->comment('Trường này sẽ lưu trữ lịch sử giao dịch của ví dưới dạng JSON. Mỗi phần tử trong JSON array sẽ đại diện cho một giao dịch');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wallets');
    }
};
