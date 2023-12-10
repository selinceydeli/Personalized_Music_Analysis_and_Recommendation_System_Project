<?php

namespace Database\Factories;

use App\Models\Block;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class BlockFactory extends Factory
{
    protected $model = Block::class;
    
    public function definition()
    {
        return [
            'blocker_username' => function() {
                // Create a new user and return the username
                return User::inRandomOrder()->first()->username;
            },
            'blocked_username' => function() {
                // Create another new user and return the username
                return User::inRandomOrder()->first()->username;
            },
        ];
    }
}
