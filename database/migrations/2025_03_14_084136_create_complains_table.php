<?php

use App\Models\TransactionWallet;
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
        Schema::create('complains', function (Blueprint $table) {
            $table->id();

            $table->foreignIdFor(TransactionWallet::class)->constrained()->onDelete('cascade');

            $table->enum('status', ['resolved', 'rejected', 'pending', 'canceled'])->comment('Trạng thái khiếu nại');

            $table->string('description')->comment('Nội dung khiếu nại');
            $table->string('proof_img')->comment('Ảnh bằng chứng');
            $table->timestamp('request_date')->comment('Ngày khiếu nại');

            $table->string('feedback_by_admin')->nullable()->comment('Phản hồi của admin');
            $table->timestamp('feedback_date')->nullable()->comment('Ngày phản hồi');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('complains');
    }
};
