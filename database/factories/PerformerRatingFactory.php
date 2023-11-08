<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use app\Models\Performer;
use app\Models\User;

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
        $performerIds = Performer::pluck('id')->all();
        return [
            'rating' => $this->faker->randomFloat(1, 0, 50) / 10,
            'username' => $this->faker->randomElement($usernames),
            'perf_id' => $this->faker->randomElement($performerIds),
            'date_rated' => $this->faker->dateTimeBetween($startDate = '-1 years', $endDate = 'now', $timezone = null)->format('Y-m-d'),
        ];
    }
}