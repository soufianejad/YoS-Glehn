<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentsTable extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('transaction_id')->unique();

            // Type de paiement
            $table->enum('payment_type', ['subscription', 'book_pdf', 'book_audio'])->default('subscription');
            $table->foreignId('subscription_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('book_id')->nullable()->constrained()->onDelete('set null');

            // Montant
            $table->decimal('amount', 10, 2);
            $table->string('currency')->default('XOF'); // FCFA

            // Méthode de paiement
            $table->enum('payment_method', ['mobile_money', 'card', 'bank_transfer'])->default('mobile_money');
            $table->string('payment_provider')->nullable(); // Orange Money, MTN, Wave, etc.

            // Statut
            $table->enum('status', ['pending', 'completed', 'failed', 'refunded'])->default('pending');
            $table->text('payment_details')->nullable(); // JSON avec détails

            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            $table->index('user_id');
            $table->index('transaction_id');
            $table->index('status');
            $table->index('payment_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
}
