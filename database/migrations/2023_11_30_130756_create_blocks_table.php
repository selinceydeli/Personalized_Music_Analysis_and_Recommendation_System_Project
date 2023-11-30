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
        Schema::create('blocks', function (Blueprint $table) {
            $table->id();
            $table->string('blocker_username');
            $table->string('blocked_username');
            $table->timestamps();

            $table->foreign('blocker_username')->references('username')->on('users')->onDelete('cascade');
            $table->foreign('blocked_username')->references('username')->on('users')->onDelete('cascade');
            $table->unique(['blocker_username', 'blocked_username']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blocks');
    }
};
