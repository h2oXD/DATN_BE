<?php

use App\Models\Question;
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
        Schema::create('answers', function (Blueprint $table) {
            $table->id();

            $table->foreignIdFor(Question::class)->constrained()->onDelete('cascade');
            $table->text('answer_text')->comment('Đáp án');
            $table->tinyInteger('is_correct');
            $table->string('note')->nullable()->comment('Chú thích hoặc giải thích cho đáp án');
            $table->integer('order');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('answers');
    }
};
