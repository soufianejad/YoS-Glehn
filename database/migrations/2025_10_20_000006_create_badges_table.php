<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBadgesTable extends Migration
{
    public function up(): void
    {
        Schema::create('badges', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Lecteur du mois, Curieux des contes, etc.
            $table->string('slug')->unique();
            $table->text('description');
            $table->string('icon'); // URL ou nom du fichier
            $table->string('color')->default('#3b82f6');

            // Conditions pour obtenir le badge
            $table->integer('books_required')->nullable(); // Nombre de livres à lire
            $table->integer('minutes_required')->nullable(); // Minutes de lecture/écoute
            $table->integer('quizzes_required')->nullable(); // Nombre de quiz réussis

            $table->integer('points')->default(10); // Points accordés
            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('badges');
    }
}
