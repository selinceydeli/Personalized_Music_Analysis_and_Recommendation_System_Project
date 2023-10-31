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
        $performerNames = [
            "Velvet Vortex",
            "Sonic Mirage",
            "Neon Nomad",
            "Midnight Maven",
            "Celestial Strum",
            "Cosmic Cadence",
            "Rhythm Rogue",
            "Harmonic Haze",
            "Electric Enigma",
            "Melodic Monsoon",
            "Lunar Lyricist",
            "Starlight Serenade",
            "Phantom Phonic",
            "Galactic Groove",
            "Ethereal Echo",
            "Solar Songbird",
            "Mystic Melody",
            "Aurora Anthem",
            "Tempest Tune",
            "Radiant Riff",
            "Quantum Quasar",
            "Nebula Nomad",
            "Vibe Voyager",
            "Siren of Sound",
            "Pulse Prodigy",
            "Stardust Sonata",
            "Eclipse Empress",
            "Sonic Sylph",
            "Lyric Luminary",
            "Resonant Rebel",
            "Vortex Virtuoso",
            "Inferno Idol",
            "Celestial Crooner",
            "Melody Maverick",
            "Luminous Lyrist",
            "Acoustic Alchemy",
            "Rhapsody Rogue",
            "Harmony Havoc",
            "Serenade Shaman",
            "Cadence Comet",
            "Soundwave Sorcerer",
            "Echo Enchantress",
            "Melodic Muse",
            "Riff Raider",
            "Sonic Siren",
            "Vibe Virtuoso",
            "Symphony Spirit",
            "Resonance Ranger",
            "Audio Astronaut",
            "Harmony Hero",
        ];
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
            'name' => $this->faker->randomElement($performerNames, $count = 2),
            'nationality' => $this->faker->randomElement($nationalities),
        ];
    }
}
