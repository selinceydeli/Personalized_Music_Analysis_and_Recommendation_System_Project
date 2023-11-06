<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Album;

class AlbumSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Album::factory(10)->create();
    }
}
