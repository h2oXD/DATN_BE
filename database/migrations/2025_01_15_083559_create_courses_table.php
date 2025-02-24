<?php

use App\Models\Category;
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
            Schema::create('courses', function (Blueprint $table) {
                $table->id();
                $table->foreignIdFor(User::class)->constrained()->onDelete('cascade');
                $table->foreignIdFor(Category::class)->nullable()->constrained()->onDelete('cascade');

                $table->integer('price_regular')->nullable();
                $table->integer('price_sale')->nullable();

                $table->string('title',50);
                $table->string('thumbnail')->nullable();
                $table->string('video_preview')->nullable();
                $table->text('description')->nullable()->comment("Mô tả");
                $table->string('primary_content')->nullable()->comment("Nội dung chính trong khoá học này là");
                
                $table->enum('status', ['draft', 'pending', 'published']);
                $table->tinyInteger('is_show_home')->default(1)->comment('Có hiển thị ở trang chủ hay không?');

                $table->json('target_students')->nullable();
                $table->json('learning_outcomes')->nullable()->comment("Học viên sẽ học được gì trong khóa học của bạn?");
                $table->json('prerequisites')->nullable()->comment("Yêu cầu hoặc điều kiện tiên quyết để tham gia khóa học của bạn là gì?");
                $table->text('who_is_this_for')->nullable()->comment("Khóa học này dành cho đối tượng nào?");
                $table->boolean('is_free')->default(false)->comment("Khoá học này có miễn phí hay không?"); 

                $table->string('language')->nullable()->comment("Ngôn ngữ trong khoá học");
                $table->string('level')->nullable()->comment("Trình độ");

                $table->decimal('admin_commission_rate', 5, 2)->default(0);

                $table->timestamps();

                $table->timestamp('submited_at')->nullable()->comment('Thời gian gửi yêu cầu kiểm duyệt');
                $table->timestamp('censored_at')->nullable()->comment('Thời gian được phê duyệt');
                $table->text('admin_comment')->nullable()->comment('Phản hồi của quản trị viên');
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
