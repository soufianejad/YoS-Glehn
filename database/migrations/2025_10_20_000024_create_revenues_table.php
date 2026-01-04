<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRevenuesTable extends Migration
{
    public function up(): void
    {
        Schema::create('revenues', function (Blueprint $table) {
            $table->id();
            $table->foreignId('author_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('book_id')->constrained()->onDelete('cascade');
            $table->foreignId('payment_id')->constrained()->onDelete('cascade');

            $table->decimal('total_amount', 10, 2); // Montant total de la vente
            $table->decimal('author_amount', 10, 2); // Part de l'auteur (60% ou 80%)
            $table->decimal('platform_amount', 10, 2); // Part de la plateforme

            $table->integer('author_percentage'); // 60 ou 80
            $table->enum('revenue_type', ['subscription', 'pdf_sale', 'audio_sale'])->default('subscription');

            $table->enum('status', ['pending', 'approved', 'paid'])->default('pending');
            $table->timestamp('paid_at')->nullable();

            $table->timestamps();

            $table->index('author_id');
            $table->index('book_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('revenues');
    }
}
