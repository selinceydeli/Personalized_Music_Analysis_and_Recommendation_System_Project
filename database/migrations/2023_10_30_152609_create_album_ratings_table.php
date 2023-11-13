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
        Schema::create('album_ratings', function (Blueprint $table) {
            $table->id();
            $table->decimal('rating', 2, 1); // ratings are between 0 - 5.0
            $table->string('username');
            $table->foreign('username')->references('username')->on('users')->cascadeOnDelete();
            $table->foreignId('album_id')->constrained('albums')->cascadeOnDelete(); // Foreign key referencing albums
            $table->timestamp('date_rated');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('album_ratings', function (Blueprint $table) {
            $table->dropForeign(['album_id']);
            $table->dropForeign(['username']); // If you also want to drop this foreign key
        });
        Schema::dropIfExists('album_ratings');
    }
};
