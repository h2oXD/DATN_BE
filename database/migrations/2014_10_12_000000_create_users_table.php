<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id(); 
            $table->string('email', 50)->unique();
            $table->string('password', 255);
            $table->string('name', 50);
            $table->string('phone_number', 20)->nullable();
            $table->string('profile_picture', 255)->nullable();
            $table->text('bio')->nullable();
            $table->unsignedBigInteger('google_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
