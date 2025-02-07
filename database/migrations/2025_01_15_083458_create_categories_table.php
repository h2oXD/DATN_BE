<?php

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
        Schema::create('categories', function (Blueprint $table) {
            $table->id();

            $table->string('name', 50)->comment('Tên danh mục');
            $table->string('slug', 100)->nullable()->unique()->comment('Slug của danh mục');
            $table->string('image')->nullable()->comment('Ảnh của danh mục');

            $table->foreignId('parent_id')->nullable()->constrained('categories')->onDelete('cascade')->comment('id danh mục cha'); 
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
