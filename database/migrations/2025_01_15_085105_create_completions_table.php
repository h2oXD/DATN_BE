<?php

use App\Models\Course;
use App\Models\Lesson;
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
        Schema::create('completions', function (Blueprint $table) {
            $table->id();
            
            $table->foreignIdFor(User::class)->constrained()->onDelete('cascade');
            $table->foreignUuid('course_id')->constrained('courses')->onDelete('cascade');
            $table->foreignUuid('lesson_id')->constrained('lessons')->onDelete('cascade');
            $table->enum('status', ['in_progress','completed']);
            $table->timestamp('completed_at')->nullable()->default(NULL);
            $table->timestamp('created_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('completions');
    }
};
