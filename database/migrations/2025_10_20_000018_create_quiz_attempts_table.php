<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuizAttemptsTable extends Migration
{
    public function up(): void
    {
        Schema::create('quiz_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Étudiant

            $table->integer('score')->default(0); // Score obtenu
            $table->integer('total_questions');
            $table->integer('correct_answers')->default(0);
            $table->decimal('percentage', 5, 2)->default(0); // Pourcentage de réussite

            $table->json('answers')->nullable(); // Réponses données par l'élève (JSON)

            $table->timestamp('started_at');
            $table->timestamp('completed_at')->nullable();
            $table->integer('time_spent')->nullable(); // En secondes

            $table->boolean('is_passed')->default(false);

            $table->timestamps();

            $table->index('quiz_id');
            $table->index('user_id');
            $table->index(['user_id', 'quiz_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quiz_attempts');
    }
}
