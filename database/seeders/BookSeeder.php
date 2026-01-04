<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Seeder;

class BookSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $authors = User::where('role', 'author')->pluck('id');
        if ($authors->isEmpty()) {
            $this->command->warn('No authors found. Skipping BookSeeder.');

            return;
        }

        $categories = Category::all()->groupBy('space');
        if ($categories->isEmpty()) {
            $this->command->warn('No categories found. Skipping BookSeeder.');

            return;
        }

        // Create Public Books
        if ($categories->has('public')) {
            Book::factory(20)->make()->each(function ($book) use ($authors, $categories) {
                $book->author_id = $authors->random();
                $book->category_id = $categories['public']->random()->id;
                $book->space = 'public';
                $book->save();
            });
        }

        // Create Educational Books
        if ($categories->has('educational')) {
            Book::factory(15)->make()->each(function ($book) use ($authors, $categories) {
                $book->author_id = $authors->random();
                $book->category_id = $categories['educational']->random()->id;
                $book->space = 'educational';
                $book->save();
            });
        }

        // Create Adult Books
        if ($categories->has('adult')) {
            Book::factory(10)->make()->each(function ($book) use ($authors, $categories) {
                $book->author_id = $authors->random();
                $book->category_id = $categories['adult']->random()->id;
                $book->space = 'adult';
                $book->save();
            });
        }
    }
}
