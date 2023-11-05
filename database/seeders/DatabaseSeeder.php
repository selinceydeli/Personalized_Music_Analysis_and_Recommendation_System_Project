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
        
        Album::factory(10)->create();
        Performer::factory(5)->create();
        Song::factory(50)->create();
        User::factory(70)->create();
        SongRating::factory(100)->create();
        PerformerRating::factory(80)->create();
        AlbumRating::factory(80)->create();
    }
}
