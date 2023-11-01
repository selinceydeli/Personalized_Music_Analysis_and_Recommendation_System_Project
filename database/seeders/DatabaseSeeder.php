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
class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        
        Album::factory(100)->create();
        Performer::factory(25)->create();
        Song::factory(500)->create();
        User::factory(100)->create();
        SongRating::factory(1000)->create();
        PerformerRating::factory(500)->create();
        AlbumRating::factory(100)->create();
    }
}
