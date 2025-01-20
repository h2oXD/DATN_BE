<?php

use App\Models\Lesson;
use App\Models\Student;
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
        Schema::create('notes', function (Blueprint $table) {
            $table->id();

            $table->foreignIdFor(Student::class)->constrained()->onDelete('cascade')->comment('id của học viên tạo ghi chú');
            $table->foreignIdFor(Lesson::class)->comment('id của bài học ghi chú');
            $table->string('content')->comment('Nội dung ghi chú');
            $table->integer('duration')->comment('Thời gian đánh dấu lúc tạo ghi chú');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notes');
    }
};
