<?php

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
        Schema::create('vouchers', function (Blueprint $table) {
            $table->id();

            $table->string('name')->comment('Tên phiếu giảm giá');
            $table->string('code')->comment('Mã giảm giá');
            $table->text('description')->comment('Nội dung phiếu giảm giá');
            $table->enum('type', ['percent', 'fix_amount'])->comment('Cho phép giảm theo phần trăm hoặc giá cụ thể');
            $table->decimal('discount_percent', 10, 0)->nullable()->comment('Giảm bao nhiêu phần trăm');
            $table->integer('discount_amount')->nullable()->comment('Giảm theo số tiền cụ thể');
            $table->datetime('start_time')->comment('Ngày bắt đầu giảm giá');
            $table->datetime('end_time')->comment('Ngày kết thúc giảm giá');
            $table->integer('count')->comment('Số phiếu giảm giá còn lại');
            $table->boolean('is_active')->default('1')->comment('Trạng thái phiếu giảm giá');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vouchers');
    }
};
