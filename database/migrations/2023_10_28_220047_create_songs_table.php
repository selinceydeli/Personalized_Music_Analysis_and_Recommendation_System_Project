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
            $table->id();
            $table->string('name');
            $table->date('publ_date'); // stores the date in YYYY-MM-DD format
            $table->json('performers'); // stored as JSON field
            $table->string('song_writer');
            $table->string('genre');
            $table->string('recording_type'); // live/studio/radio
            $table->decimal('length', 8, 2); // 8: total number of digits, 2: number of digits after decimal point
            $table->string('tempo'); // grave/lento/largo/adagio/adante/moderato/allegretto/allegro/vivace/presto/prestissimo
            $table->string('key');
            $table->string('mood');
            $table->string('language');
            $table->timestamp('system_entry_date'); // stores both date and time
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
