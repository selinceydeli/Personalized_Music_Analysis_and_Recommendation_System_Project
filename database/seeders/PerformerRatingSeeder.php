<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Album;
use App\Models\Performer;
use App\Models\Song;
use App\Models\User;
use App\Models\SongRating;
use App\Models\PerformerRating;
use App\Models\AlbumRating;
class PerformerRatingSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        PerformerRating::factory(80)->create();
    }
}
