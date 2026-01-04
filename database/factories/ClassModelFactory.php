<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ClassModelFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = \App\Models\ClassModel::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $baseName = fake()->randomElement(['6ème', 'Terminale', 'Licence 1', 'Master 2', 'CM2']);
        $name = $baseName.' '.fake()->unique()->randomNumber(2);

        return [
            // school_id will be provided by the SchoolSeeder
            'name' => $name,
            'slug' => Str::slug($name),
            'description' => fake()->sentence(),
            'level' => fake()->randomElement(['primaire', 'collège', 'lycée', 'université']),
            'is_active' => true,
        ];
    }
}
