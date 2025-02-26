<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('notes', function (Blueprint $table) {
            // Xóa foreign key lesson_id
            // $table->dropForeign(['lesson_id']);
            $table->dropColumn('lesson_id');

            // Thêm foreign key video_id
            // $table->foreignId('video_id')->constrained()->onDelete('cascade')->comment('id của video ghi chú');

            // Đổi tên duration thành timestamp
            $table->renameColumn('duration', 'timestamp');
        });
    }

    public function down(): void
    {
        Schema::table('notes', function (Blueprint $table) {
            $table->dropForeign(['video_id']);
            $table->dropColumn('video_id');

            $table->foreignIdFor(\App\Models\Lesson::class)->comment('id của bài học ghi chú');
            $table->renameColumn('timestamp', 'duration');
        });
    }
};
