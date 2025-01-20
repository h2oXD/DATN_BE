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
        Schema::create('vouchers', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('code');
            $table->text('description');
            $table->string('type');
            $table->decimal('discount', 10, 0);
            $table->datetime('start_time');
            $table->datetime('end_time');
            $table->integer('count');
            $table->integer('used_course');
            $table->boolean('is_active')->default('1');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vouchers');
    }
};
