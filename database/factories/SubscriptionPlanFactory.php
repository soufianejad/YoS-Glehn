<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class SubscriptionPlanFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->words(3, true);

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'description' => fake()->sentence(),
            'type' => 'individual',
            'price' => fake()->randomElement([7000, 50000]),
            'duration_days' => 30,
            'max_students' => null,
            'pdf_access' => true,
            'audio_access' => true,
            'download_access' => true,
            'quiz_access' => false,
            'is_active' => true,
            'order' => 0,
        ];
    }
}
