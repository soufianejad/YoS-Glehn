<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('badges')->where('slug', 'premier-livre-lu')->update([
            'books_required' => 1,
            'points' => 10,
        ]);

        DB::table('badges')->where('slug', 'lecteur-assidu')->update([
            'books_required' => 10,
            'points' => 50,
        ]);

        DB::table('badges')->where('slug', 'maitre-des-quiz')->update([
            'quizzes_required' => 5,
            'points' => 50,
        ]);

        DB::table('badges')->where('slug', 'lecteur-du-mois')->update([
            'points' => 100,
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No down migration needed
    }
};
