<?php

namespace Database\Factories;

use App\Models\Friendship;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Friendship>
 */
class FriendshipFactory extends Factory
{
    protected $model = Friendship::class;

    public function definition()
    {

        return [
            'requester' => function() {
                return User::inRandomOrder()->first()->username;
            },
            'user_requested' => function() {
                return User::inRandomOrder()->first()->username;
            },
            'status' => $this->faker->randomElement([0, 1]), // 0 for pending, 1 for accepted
        ];
    }
}
