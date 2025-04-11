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

            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();

            $table->string('phone_number', 20)->nullable();
            $table->string('profile_picture')->nullable();
            $table->text('bio')->nullable();
            $table->string('google_id')->nullable();
            $table->tinyInteger('status')->default(0)->comment('0: hoạt động, 1: khóa giảng viên, 2: khóa cả giảng viên và học viên');

            $table->string('country')->nullable();
            $table->string('province')->nullable();
            $table->date('birth_date')->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->nullable();

            $table->string('linkedin_url')->nullable()->default(NULL);
            $table->string('website_url')->nullable()->default(NULL);

            $table->string('certificate_file')->nullable();

            $table->string('bank_name')->nullable()->comment('Tên ngân hàng');
            $table->string('bank_nameUser')->nullable()->comment('Tên người dùng');
            $table->string('bank_number')->nullable()->comment('Số tài khoản');

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
