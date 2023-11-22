<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Performer;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PerformerRating>
 */
class PerformerRatingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    
    public function definition(): array
    {
        $startDate = now()->subYears(1);
        $usernames = User::pluck('username')->all();
        $performerIds = Performer::pluck('artist_id')->all();
        return [
            'rating' => $this->faker->randomFloat(1, 0, 50) / 10,
            'username' => $this->faker->randomElement($usernames),
            'artist_id' => $this->faker->randomElement($performerIds),
            'date_rated' => $this->faker->dateTimeBetween($startDate = '-1 years', $endDate = 'now', $timezone = null),
        ];
    }
}
