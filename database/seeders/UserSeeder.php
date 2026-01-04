<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create the main Admin User
        User::updateOrCreate(['email' => 'admin@plateforme.com'], [
            'first_name' => 'Admin',
            'last_name' => 'User',
            'email' => 'admin@plateforme.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // Create Authors
        User::factory(5)->create(['role' => 'author']);

        // Create Readers
        User::factory(10)->create(['role' => 'reader']);
    }
}
