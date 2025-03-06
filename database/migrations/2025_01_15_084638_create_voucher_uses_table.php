<?php

use App\Models\Course;
use App\Models\User;
use App\Models\Voucher;
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
        Schema::create('voucher_uses', function (Blueprint $table) {
            $table->id();

            $table->foreignIdFor(Voucher::class)->constrained()->onDelete('cascade')->comment('ID phiếu giảm giá được dùng');
            $table->foreignIdFor(User::class)->constrained()->onDelete('cascade')->comment('ID người sử dụng');
            $table->foreignUuid('course_id')->constrained('courses')->onDelete('cascade')->comment('ID khóa học được sử dụng');
            $table->timestamp('time_used')->comment('Thời gian sử dụng');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('voucher_uses');
    }
};
