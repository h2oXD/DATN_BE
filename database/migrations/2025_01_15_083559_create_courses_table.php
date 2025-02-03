<?php

use App\Models\Category;
use App\Models\Lecturer;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Lecturer::class)->constrained()->onDelete('cascade');
            $table->foreignIdFor(Category::class)->constrained()->onDelete('cascade');

            $table->integer('price');
            $table->integer('price_sale');

            $table->string('title');
            $table->string('thumbnail')->nullable();
            $table->text('description')->nullable()->comment("Mô tả");
            $table->string('primary_content')->nullable()->comment("Nội dung chính trong khoá học này là");
            
            $table->enum('status', ['draft', 'published']);
            $table->tinyInteger('is_show_home')->nullable();

            $table->text('target_students')->nullable();
            $table->json('learning_outcomes')->nullable()->comment("Học viên sẽ học được gì trong khóa học của bạn?");
            $table->text('prerequisites')->nullable()->comment("Yêu cầu hoặc điều kiện tiên quyết để tham gia khóa học của bạn là gì?");
            $table->text('who_is_this_for')->nullable()->comment("Khóa học này dành cho đối tượng nào?");

            $table->string('language')->nullable()->comment("Ngôn ngữ trong khoá học");
            $table->string('level')->nullable()->comment("Trình độ");

            $table->decimal('admin_commission_rate', 5, 2)->default(0);
            
            $table->timestamps();

            $table->timestamp('submited_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
