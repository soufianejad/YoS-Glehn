<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReviewsTable extends Migration
{
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('book_id')->constrained()->onDelete('cascade');

            $table->integer('rating'); // 1 à 5 étoiles
            $table->text('comment')->nullable();

            $table->boolean('is_verified_purchase')->default(false);
            $table->boolean('is_approved')->default(true);

            $table->timestamps();

            $table->index('book_id');
            $table->index('user_id');
            $table->unique(['user_id', 'book_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
}
