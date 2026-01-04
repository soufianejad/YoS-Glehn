<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class QuestionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $options = [
            fake()->sentence(3),
            fake()->sentence(3),
            fake()->sentence(3),
            fake()->sentence(3),
        ];

        return [
            // quiz_id will be provided by the seeder
            'question_text' => fake()->sentence().'?',
            'question_type' => 'multiple_choice',
            'options' => $options,
            'correct_answer' => fake()->numberBetween(0, 3),
            'explanation' => fake()->sentence(),
            'points' => 1,
            'order' => 0, // will be handled by the seeder
        ];
    }
}
