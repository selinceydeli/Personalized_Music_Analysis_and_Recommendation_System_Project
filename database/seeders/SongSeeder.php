<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Song;
use App\Models\Performer;
use App\Models\Album;

class SongSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Song::factory(50)->create();
    }
}
