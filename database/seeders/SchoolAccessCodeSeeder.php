<?php

namespace Database\Seeders;

use App\Models\School;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SchoolAccessCodeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $schools = School::whereNull('access_code')->get();

        foreach ($schools as $school) {
            $school->access_code = strtoupper(Str::random(8));
            $school->save();
        }
    }
}
