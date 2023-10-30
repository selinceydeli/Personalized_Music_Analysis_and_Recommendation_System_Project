<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Song>
 */
class SongFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {

        $songWords = [
            "Love", "Heart", "Baby", "Night", "Day", "Time", "Light", "Dark",
            "Star", "Moon", "Sun", "Dream", "Life", "Soul", "Fire", "Eyes",
            "World", "Mind", "Rain", "Tears", "Smile", "Kiss", "Dance", "Magic",
            "Angel", "Heaven", "Stars", "Cry", "Laugh", "Scream", "Sing", "Fly",
            "Falling", "Rising", "Breaking", "Healing", "Running", "Waiting",
            "Feeling", "Touch", "Breath", "Whisper", "Shout", "Silence", "Loud",
            "Close", "Far", "Lost", "Found", "Chase", "Flee", "Rise", "Fall",
            "Stand", "Crawl", "Fight", "Surrender", "Win", "Lose", "Remember",
            "Forget", "Begin", "End", "Live", "Die", "Laugh", "Cry", "Love",
            "Hate", "Embrace", "Reject", "Desire", "Fear", "Hope", "Despair"
        ];
        $performers = Performer::pluck('name')->all();
        return [
            'name' => $this->faker->randomElement($songWords, $count = 2),
            'publ_date' => $this->faker->date($format = 'd-m-Y', $max = 'now'),
            'performers' => $this->faker->
            $table->string('name');
            $table->date('publ_date'); // stores the date in YYYY-MM-DD format
            $table->json('performers'); // stored as JSON field
            $table->string('song_writer');
            $table->string('genre');
            $table->string('recording_type'); // live/studio/radio
            $table->unsignedInteger('song_length_seconds'); // song length is stored in seconds 
            $table->decimal('tempo'); // in bpm unit
            $table->string('key');
            $table->string('mood');
            $table->string('language');
            $table->timestamp('system_entry_date'); // stores both date and time
            $table->foreignId('album_id')->constrained('albums')->cascadeOnDelete(); // Foreign key referencing songs
            $table->timestamps();
        ];
    }
}
