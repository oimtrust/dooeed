<?php

namespace Database\Factories;

use App\Models\InitialWealthEntry;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<InitialWealthEntry>
 */
class InitialWealthEntryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'category' => InitialWealthEntry::Cash,
            'name' => fake()->company(),
            'amount' => fake()->numberBetween(1_000, 1_000_000),
        ];
    }
}
