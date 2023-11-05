<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Album>
 */
class AlbumFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

    public function definition(): array
    {
        $albumWords = [
            "Dreams", "Echoes", "Paradise", "Mystery", "Reflections",
            "Shadows", "Whispers", "Revolution", "Memories", "Horizon",
            "Serenade", "Euphoria", "Voyage", "Illusion", "Harmony",
            "Odyssey", "Labyrinth", "Phoenix", "Nebula", "Eclipse",
            "Mirage", "Sanctuary", "Destiny", "Inferno", "Celestial",
            "Radiance", "Abyss", "Rhapsody", "Symphony", "Maelstrom",
            "Ascent", "Spectrum", "Paradox", "Elysium", "Vortex",
            "Solitude", "Galaxy", "Utopia", "Zenith", "Nirvana",
            "Enigma", "Oasis", "Pulse", "Ethereal", "Astral",
            "Infinity", "Quasar", "Dystopia", "Epiphany", "Silhouette"
        ];

        return [
            'name' => implode(' ', $this->faker->randomElements($albumWords, $count = 2)),
            'is_single' => $this->faker->boolean,
            'image_url' => $this->faker->imageUrl($width = 640, $height = 480),
        ];
    }
}
