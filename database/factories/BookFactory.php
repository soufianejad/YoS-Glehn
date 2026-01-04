<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class BookFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->unique()->sentence(4);

        return [
            'title' => $title,
            'slug' => Str::slug($title),
            'description' => fake()->paragraphs(3, true),
            'author_id' => User::factory(), // Will be overridden in the seeder
            'category_id' => Category::factory(), // Will be overridden in the seeder
            'pdf_pages' => fake()->numberBetween(50, 500),
            'audio_duration' => fake()->numberBetween(3600, 18000), // in seconds
            'published_year' => fake()->year(),
            'language' => 'fr',
            'space' => 'public', // default, will be overridden
            'content_type' => fake()->randomElement(['free', 'premium']),
            'pdf_price' => 3000,
            'audio_price' => 3500,
            'status' => 'published',
            'has_quiz' => false,
        ];
    }
}
