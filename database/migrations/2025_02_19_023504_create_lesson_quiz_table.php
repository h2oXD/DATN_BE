<?php

use App\Models\Lesson;
use App\Models\Quiz;
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
        Schema::create('lesson_quiz', function (Blueprint $table) {
            $table->foreignIdFor(Lesson::class)->constrained()->onDelete('cascade');
            $table->foreignIdFor(Quiz::class)->constrained()->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lesson_quiz');
    }
};
