<?php

namespace Database\Factories;

use App\Enum\GenderType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Customer>
 */
class CustomerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => substr(fake()->phoneNumber(), 0, 12), // Adjust 15 to match your column length
            'address' => fake()->address(),
            'salary' => fake()->randomFloat(2, 1000, 10000),
            'age' => fake()->numberBetween(18, 65),
            'gender' => fake()->randomElement(GenderType::cases()),
            'avatar' => fake()->imageUrl(),
            'identification' => fake()->unique()->numerify('##########'),
        ];
    }
}
