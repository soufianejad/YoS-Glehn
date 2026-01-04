<?php

namespace Database\Factories;

use App\Models\Book;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReadingProgressFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $currentPage = fake()->numberBetween(1, 100);
        $totalPages = $currentPage + fake()->numberBetween(5, 100);

        return [
            'user_id' => User::factory(), // Will be overridden
            'book_id' => Book::factory(), // Will be overridden
            'current_page' => $currentPage,
            'total_pages' => $totalPages,
            'progress_percentage' => ($currentPage / $totalPages) * 100,
            'time_spent' => fake()->numberBetween(60, 3600), // seconds
            'last_read_at' => now(),
        ];
    }
}
