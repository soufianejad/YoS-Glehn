<?php

namespace Database\Seeders;

use App\Models\ClassModel;
use App\Models\School;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\User;
use Illuminate\Database\Seeder;

class SchoolSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create 5 schools, each with 3 classes and 20 students attached to those classes
        School::factory()
            ->count(5)
            ->has(ClassModel::factory()->count(3), 'classes')
            ->create()
            ->each(function ($school) {
                // Create 20 students for each school
                $students = User::factory()
                    ->count(20)
                    ->state([
                        'role' => 'student',
                        'school_id' => $school->id,
                    ])
                    ->create();

                // For each student, attach them to 1 or 2 random classes within the school
                foreach ($students as $student) {
                    $student->classes()->attach(
                        $school->classes->random(rand(1, 2))->pluck('id')->mapWithKeys(function ($classId) {
                            return [$classId => ['enrolled_at' => now()]];
                        })->toArray()
                    );
                }

                // Create an active subscription for the school
                $schoolPlan = SubscriptionPlan::where('type', 'school')->inRandomOrder()->first();
                if ($schoolPlan) {
                    Subscription::create([
                        'user_id' => $school->user_id,
                        'subscription_plan_id' => $schoolPlan->id,
                        'start_date' => now()->subDays(rand(1, 30)),
                        'end_date' => now()->addDays($schoolPlan->duration_days - rand(1, 30)),
                        'status' => 'active',
                    ]);
                }
            });
    }
}
