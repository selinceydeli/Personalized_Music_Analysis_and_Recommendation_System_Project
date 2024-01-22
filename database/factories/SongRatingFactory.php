<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Song;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SongRating>
 */
class SongRatingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {

        $usernames = User::pluck('username')->all();
        $songIds = Song::pluck('song_id')->all();
        return [
            'rating' => $this->faker->randomFloat(1, 00, 50) / 10,
            'username' => $this->faker->randomElement($usernames),
            'song_id' => $this->faker->randomElement($songIds),
            'date_rated' => $this->faker->dateTimeBetween($startDate = '-1 month', $endDate = 'now', $timezone = null),
        ];
    }
}
