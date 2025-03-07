<?php

use App\Models\Course;
use App\Models\Lecturer;
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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class)->constrained()->onDelete('cascade')->comment('id của Người mua');
            $table->foreignUuid('course_id')->constrained('courses')->onDelete('cascade')->comment('id của Khoá học');
            $table->decimal('amount',10, 2);
            $table->enum('payment_method', ['credit_card','paypal','bank_transfer','wallet'])->comment('Phương thức thanh toán');
            $table->enum('status', ['pending', 'success', 'failed'])->default('pending')->comment('Trạng thái giao dịch');
            $table->datetime('transaction_date');
        
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
