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
        Schema::create('performer_ratings', function (Blueprint $table) {
            $table->id();
            $table->decimal('rating', 2, 1); // ratings are between 0 - 5.0
            $table->string('username');
            $table->foreign('username')->references('username')->on('users')->cascadeOnDelete();
            $table->foreignId('perf_id')->constrained('performers')->cascadeOnDelete(); // Foreign key referencing performers
            $table->date('date_rated');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('performer_ratings', function (Blueprint $table) {
            $table->dropForeign(['username']);
            $table->dropForeign(['perf_id']);
        });
        
        Schema::dropIfExists('performer_ratings');
    }
};
