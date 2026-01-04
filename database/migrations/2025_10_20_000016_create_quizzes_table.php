<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuizzesTable extends Migration
{
    public function up(): void
    {
        Schema::create('quizzes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('book_id')->constrained()->onDelete('cascade');

            $table->string('title');
            $table->text('description')->nullable();
            $table->integer('questions_count')->default(10);
            $table->integer('pass_score')->default(60); // Score minimum pour réussir (%)
            $table->integer('time_limit')->nullable(); // En minutes, null = pas de limite

            $table->boolean('is_active')->default(true);
            $table->boolean('show_correct_answers')->default(true); // Afficher les réponses après
            $table->boolean('randomize_questions')->default(true);

            $table->timestamps();

            $table->index('book_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quizzes');
    }
}
