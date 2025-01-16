<?php

use App\Models\Lesson;
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
        Schema::create('codings', function (Blueprint $table) {
            $table->id();

            $table->foreignIdFor(Lesson::class)->constrained()->onDelete('cascade');
            $table->string('language', 50);
            $table->string('problem_title');
            $table->text('problem_description')->nullable()->default(Null);
            $table->text('starter_code');
            $table->text('solution_code');
            $table->json('test_cases');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('codings');
    }
};
