<?php

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
        Schema::create('lecturers', function (Blueprint $table) {
            $table->id();
            
            $table->foreignIdFor(User::class)->constrained()->onDelete('cascade');
            $table->string('expertise')->nullable()->default(NULL);
            $table->decimal('rating', 2, 1)->default(0);
            $table->text('achievements')->nullable()->default(NULL);
            $table->text('certifications')->nullable()->default(NULL);
            $table->string('linkedin_url')->nullable()->default(NULL);
            $table->string('website_url')->nullable()->default(NULL);
            $table->integer('total_reviews')->default(0);
            $table->integer('total_courses')->default(0);
            $table->integer('total_students')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lecturers');
    }
};
