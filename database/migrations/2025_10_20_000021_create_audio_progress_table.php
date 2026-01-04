<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAudioProgressTable extends Migration
{
    public function up(): void
    {
        Schema::create('audio_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('book_id')->constrained()->onDelete('cascade');

            $table->integer('current_position')->default(0); // Position en secondes
            $table->integer('total_duration'); // Durée totale en secondes
            $table->decimal('progress_percentage', 5, 2)->default(0);
            $table->decimal('playback_speed', 3, 2)->default(1.00); // Vitesse de lecture (0.5x - 2x)

            $table->timestamp('last_listened_at')->nullable();
            $table->timestamp('completed_at')->nullable();

            $table->timestamps();

            $table->index('user_id');
            $table->index('book_id');
            $table->unique(['user_id', 'book_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audio_progress');
    }
}
