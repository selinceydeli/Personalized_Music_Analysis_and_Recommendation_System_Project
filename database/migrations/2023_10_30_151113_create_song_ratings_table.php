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
        Schema::create('song_ratings', function (Blueprint $table) {
            $table->id();
            $table->decimal('rating', 2, 1); // ratings are between 0 - 5.0
            $table->string('username');
            $table->foreign('username')->references('username')->on('users')->cascadeOnDelete();
            $table->foreignId('song_id')->constrained('songs')->cascadeOnDelete(); // Foreign key referencing songs
            $table->date('date_rated');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('song_ratings', function (Blueprint $table) {
            $table->dropForeign(['username']); // You should specify the actual constraint name if it's not the default
            $table->dropConstrainedForeignId('song_id');
        });

        Schema::dropIfExists('song_ratings');
    }

};
