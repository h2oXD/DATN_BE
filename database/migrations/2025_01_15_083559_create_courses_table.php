<?php

use App\Models\Category;
use App\Models\Lecturer;
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
        Schema::create('courses', function (Blueprint $table) {
            $table->id();

            $table->string('title');
            $table->text('description')->nullable();
            $table->text('target_students')->nullable();
            $table->json('learning_outcomes')->nullable();
            $table->text('prerequisites')->nullable();
            $table->text('who_is_this_for')->nullable();
            $table->integer('price');
            $table->integer('price_sale');
            $table->decimal('admin_commission_rate', 5, 2)->default(0);
            $table->enum('status', ['draft','published','archived']);
            $table->tinyInteger('is_show_home')->nullable();
            $table->foreignIdFor(Lecturer::class)->constrained()->onDelete('cascade');
            $table->foreignIdFor(Category::class)->constrained()->onDelete('cascade');
            $table->string('thumbnail')->nullable();
            $table->string('language')->nullable();
            $table->string('level')->nullable();
            $table->string('primary_content')->nullable();

            $table->timestamps();
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
