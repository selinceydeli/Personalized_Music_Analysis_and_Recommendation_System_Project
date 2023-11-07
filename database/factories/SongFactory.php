<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use app\Models\Performer;
use app\Models\Album;
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

        $genreTypes = [
            "Pop",
            "Rock",
            "Jazz",
            "Blues",
            "Country",
            "Electronic",
            "Hip-Hop/Rap",
            "Classical",
            "Reggae",
            "Soul/R&B",
            "Folk",
            "Metal",
            "Punk",
            "Latin",
            "Dance",
        ];

        $performerIds = Performer::pluck('id')->all();
        $albumIds = Album::pluck('id')->all();


        $songMoods = [
            "Happy",
            "Sad",
            "Energetic",
            "Calm",
            "Romantic",
            "Melancholic",
            "Uplifting",
            "Soothing",
            "Dramatic",
            "Mysterious",
            "Playful",
            "Reflective",
            "Intense",
            "Cheerful",
            "Serene",
            "Groovy",
            "Lively",
            "Moody",
            "Empowering",
            "Dreamy",
            "Fun",
            "Heartfelt",
            "Angry",
            "Epic",
            "Chill",
            "Hopeful",
            "Nostalgic",
            "Peaceful",
            "Quirky",
            "Relaxed",
            "Sentimental",
            "Sombre",
            "Sweet",
            "Tense",
            "Warm",
            "Whimsical",
            "Melodic",
            "Dark",
            "Light",
            "Funky",
            "Blissful",
            "Ambient",
            "Ethereal",
            "Jazzy",
            "Lush",
            "Majestic",
            "Pensive",
            "Vibrant",
            "Zen",
        ];

        $languages = [
            "English",
            "Spanish",
            "Mandarin Chinese",
            "Hindi",
            "Arabic",
            "Portuguese",
            "Bengali",
            "Russian",
            "Japanese",
            "Punjabi",
            "German",
            "Japanese",
            "Turkish"
        ];
        $performers = ['ids' => $this->faker->randomElements($performerIds, rand(1, 5))];

        return [
            'name' => implode(' ', $this->faker->randomElements($songWords, 2)),
            'publ_date' => $this->faker->date($format = 'Y-m-d', $max = 'now'),
            'performers' => json_encode($performers),
            'song_writer' => $this->faker->name(),
            'genre' => $this->faker->randomElement($genreTypes),
            'recording_type' => $this->faker->randomElement(['live', 'studio', 'radio']),
            'song_length_seconds' => $this->faker->numberBetween($min = 40, $max = 350),
            'danceability' => $this->faker->numberBetween($int1 = 0, $int2 = 10),
            'energy' => $this->faker->numberBetween($$int1 = 0, $int2 = 10),
            'tempo' => $this->faker->numberBetween($int1 = 60, $int2 = 170),
            'loudness' => $this->faker->numberBetween($$int1 = 0, $int2 = 10),
            'speechiness' => $this->faker->numberBetween($$int1 = 0, $int2 = 10),
            'acousticness' => $this->faker->numberBetween($$int1 = 0, $int2 = 10),
            'instrumentalness' => $this->faker->numberBetween($$int1 = 0, $int2 = 10),
            'liveness' => $this->faker->numberBetween($$int1 = 0, $int2 = 10),
            'valence' => $this->faker->numberBetween($$int1 = 0, $int2 = 10),
            'key' => $this->faker->numberBetween($int1 = 0, $int2 = 10),
            'mood' => $this->faker->randomElement($songMoods),
            'language' => $this->faker->randomElement($languages),
            'system_entry_date' => now(),
            'album_id' => $this->faker->randomElement($albumIds),
        ];
    }
}
