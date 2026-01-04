<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuestionsTable extends Migration
{
    public function up(): void
    {
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_id')->constrained()->onDelete('cascade');

            $table->text('question_text');
            $table->enum('question_type', ['multiple_choice', 'true_false', 'short_answer'])->default('multiple_choice');

            // Options pour les questions à choix multiples (JSON)
            $table->json('options')->nullable(); // ["Option A", "Option B", "Option C", "Option D"]
            $table->string('correct_answer'); // Index de la bonne réponse ou texte

            $table->text('explanation')->nullable(); // Explication de la réponse
            $table->integer('points')->default(1);
            $table->integer('order')->default(0);

            $table->timestamps();

            $table->index('quiz_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
}
