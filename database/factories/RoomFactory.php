<?php

namespace Database\Factories;

use App\Models\Room;
use Illuminate\Database\Eloquent\Factories\Factory;

class RoomFactory extends Factory
{
    protected $model = Room::class;

    public function definition()
    {
        return [
            'number' => $this->faker->unique()->randomNumber(3, TRUE),
            'type' => $this->faker->randomElement(['Single', 'Double', 'Apartment']),
            'price_per_night' => $this->faker->randomFloat(2, 50, 200),
            'status' => 'available',
        ];
    }
}
