<?php

namespace Database\Factories;

use App\Enum\StateType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Rate>
 */
class RateFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'percent' => fake()->randomFloat(2, 0, 100),
            'fee' => fake()->randomFloat(2, 0, 1000),
            'state' => fake()->randomElement(StateType::cases()),
        ];
    }
}
