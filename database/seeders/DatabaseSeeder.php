<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Album;
use App\Models\Performer;
use App\Models\Song;
use App\Models\User;
use App\Models\SongRating;
use App\Models\Friendship;
use App\Models\Block;
use App\Models\PerformerRating;
use App\Models\AlbumRating;
class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        //User::factory(200)->create();
        SongRating::factory(1000)->create();
        PerformerRating::factory(200)->create();
        AlbumRating::factory(200)->create();
        Friendship::factory(200)->create();
        Block::factory(10)->create();
    }
}
