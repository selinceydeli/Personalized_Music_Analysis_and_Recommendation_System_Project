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
        Schema::create('friendships', function (Blueprint $table) {
            $table->id();
            $table->string('requester');
            $table->string('user_requested');
            $table->tinyInteger('status');  // 0 = pending, 1 = accepted, etc.
            $table->timestamps();

            $table->foreign('requester')->references('username')->on('users')->onDelete('cascade');
            $table->foreign('user_requested')->references('username')->on('users')->onDelete('cascade');

            // Optional: Add a unique constraint to prevent duplicate entries
            $table->unique(['requester', 'user_requested']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('friendships');
    }
};
