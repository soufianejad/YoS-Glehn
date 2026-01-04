<?php

namespace Database\Factories;

use App\Models\School;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class SchoolFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->company();

        return [
            'user_id' => User::factory(), // Placeholder, will be replaced
            'name' => $name,
            'slug' => Str::slug($name),
            'address' => fake()->address(),
            'city' => fake()->city(),
            'country' => fake()->countryCode(),
            'phone' => fake()->phoneNumber(),
            'email' => fake()->unique()->companyEmail(),
            'access_code' => strtoupper(Str::random(8)),
            'status' => 'approved',
        ];
    }

    /**
     * Configure the model factory.
     */
    public function configure(): static
    {
        return $this->afterCreating(function (School $school) {
            // Create a dedicated user for the school
            $user = User::factory()->create([
                'role' => 'school',
                'school_id' => $school->id,
            ]);

            // Link the user to the school
            $school->user_id = $user->id;
            $school->save();
        });
    }
}
