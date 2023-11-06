<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Performer;

class PerformerSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Performer::factory(5)->create();
    }
}
