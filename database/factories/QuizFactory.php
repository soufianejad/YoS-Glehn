<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class QuizFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            // book_id will be provided by the seeder
            'title' => 'Quiz de Compréhension',
            'description' => 'Testez vos connaissances sur le livre.',
            'questions_count' => 10,
            'pass_score' => 60,
            'time_limit' => 30, // minutes
            'is_active' => true,
        ];
    }
}
