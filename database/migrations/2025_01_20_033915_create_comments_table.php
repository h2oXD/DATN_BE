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
        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class)->constrained()->onDelete('cascade')->comment('id của người bình luận');
            $table->text('content')->comment('Nội dung');
            $table->bigInteger('parent_id')->nullable()->comment('id của bình luận cha');
            $table->string('commentable_type')->comment('đối tượng bình luận');
            $table->bigInteger('commentable_id')->comment('id đối tượng bình luận');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comments');
    }
};
