<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('course_approval_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->enum('status', ['approved', 'rejected']);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('course_approval_histories');
    }
};