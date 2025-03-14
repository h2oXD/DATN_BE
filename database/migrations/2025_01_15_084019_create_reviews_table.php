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
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();

            // Người đánh giá (Reviewer - thường là sinh viên)
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // Đối tượng được đánh giá (Khóa học hoặc Giảng viên)
            $table->string('reviewable_type'); // Chuỗi cho loại model
            $table->uuid('reviewable_id');   // Chuỗi cho UUID (thay vì bigint)

            $table->integer('rating'); // Điểm đánh giá
            $table->text('review_text')->nullable(); // Nội dung đánh giá
            $table->timestamps(); // created_at & updated_at

            // Chỉ số cho quan hệ đa hình
            $table->index(['reviewable_type', 'reviewable_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};