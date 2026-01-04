<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class BadgeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->words(2, true);

        return [
            'name' => ucfirst($name),
            'slug' => Str::slug($name),
            'description' => fake()->sentence(),
            'icon' => 'fas fa-medal',
            'color' => fake()->hexColor(),
            'points' => fake()->numberBetween(5, 50),
            'is_active' => true,
        ];
    }
}
