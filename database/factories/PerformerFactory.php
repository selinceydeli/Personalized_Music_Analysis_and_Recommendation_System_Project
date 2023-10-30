<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Performer>
 */
class PerformerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $names = ['Berenay', 'Ozan', 'Oktay', 'Canberk', 'Selin', 'Şimal', 'Onur'];
        $surnames = ['Yiğit', 'Çelik', 'Sezen', 'Yücel', 'Çelebi', 'Ceydeli', 'Tahıl'];
        $nationalities = [
            "American",
            "Brazilian",
            "Canadian",
            "Dutch",
            "Egyptian",
            "French",
            "German",
            "Hungarian",
            "Indian",
            "Japanese",
            "Turkish"
        ];
        return [
            'name' => $this->faker->randomElement($names),
            'surname' => $this->faker->randomElement($surnames),
            'nationality' => $this->faker->randomElement($nationalities),
        ];
    }
}
