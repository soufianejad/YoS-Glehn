<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            // Core data without dependencies
            SettingSeeder::class,
            SubscriptionPlanSeeder::class,
            CategorySeeder::class,
            BadgeSeeder::class,

            // Users, Schools, and related data
            UserSeeder::class, // Creates Admins, Authors, Readers
            SchoolSeeder::class, // Creates Schools, School Admins, Classes, Students

            // Content
            BookSeeder::class, // Depends on Authors and Categories

            // Interactions and relationships
            InteractionSeeder::class,
        ]);
    }
}
