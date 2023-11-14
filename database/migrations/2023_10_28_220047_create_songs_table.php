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
        Schema::create('songs', function (Blueprint $table) {
            $table->string('song_id')->primary(); // primary key of the songs table
            $table->string('name');
            $table->date('publ_date'); // stores the date in YYYY-MM-DD format
            $table->json('performers'); // stored as JSON field
            $table->string('album_id');
            $table->unsignedInteger('duration'); // song length is stored in seconds 
            $table->decimal('tempo'); // in bpm unit
            $table->string('key');
            $table->boolean('explicit');
            $table->timestamp('system_entry_date'); // stores both date and time
            $table->foreign('album_id')->references('album_id')->on('albums')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('songs');
    }
};
