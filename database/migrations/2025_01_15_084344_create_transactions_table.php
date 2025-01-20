<?php

use App\Models\Course;
use App\Models\Lecturer;
use App\Models\Student;
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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();

            $table->foreignIdFor(Student::class)->constrained()->onDelete('cascade');
            $table->foreignIdFor(Course::class)->constrained()->onDelete('cascade');
            $table->foreignIdFor(Lecturer::class)->constrained()->onDelete('cascade');
            $table->decimal('amount',10, 2);
            $table->enum('type', ['purchase','refund']);
            $table->enum('status', ['pending','completed','failed','refunded']);
            $table->enum('payment_method', ['credit_card','paypal','bank_transfer','wallet']);
            $table->datetime('transaction_date');
            $table->string('reference_id')->nullable()->default(NULL);
        
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
