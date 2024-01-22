<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TestDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
{
    // Using the DB facade for raw queries
    DB::table('users')->insert(
    DB::table('users')->get()->toArray()
    );
    DB::table('album_ratings')->insert(
    DB::table('album_ratings')->get()->toArray()
    );

    DB::table('albums')->insert(
    DB::table('albums')->get()->toArray()
    );
    DB::table('blocks')->insert(
    DB::table('blocks')->get()->toArray()
    );

    DB::table('friendships')->insert(
    DB::table('friendships')->get()->toArray()
    );
    DB::table('performer_ratings')->insert(
    DB::table('performer_ratings')->get()->toArray()
    );

    DB::table('performers')->insert(
    DB::table('performers')->get()->toArray()
    );

    DB::table('song_ratings')->insert(
    DB::table('song_ratings')->get()->toArray()
    );

    DB::table('songs')->insert(
    DB::table('songs')->get()->toArray()
    );
}

}
