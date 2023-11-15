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
        Schema::create('albums', function (Blueprint $table) {
            $table->string('album_id')->primary(); // primary key of the albums table
            $table->string('name');
            $table->string('album_type');
            $table->string('image_url');
            $table->string('artist_id');
            $table->string('label');
            $table->string('copyright');
            $table->date('release_date');
            $table->unsignedTinyInteger('total_tracks');
            $table->unsignedTinyInteger('popularity');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('albums');
    }
};
