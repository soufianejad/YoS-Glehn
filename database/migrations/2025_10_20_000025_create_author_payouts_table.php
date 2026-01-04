<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAuthorPayoutsTable extends Migration
{
    public function up(): void
    {
        Schema::create('author_payouts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('author_id')->constrained('users')->onDelete('cascade');

            $table->string('payout_reference')->unique();
            $table->decimal('amount', 10, 2);
            $table->string('currency')->default('XOF');

            $table->enum('payment_method', ['mobile_money', 'bank_transfer'])->default('mobile_money');
            $table->string('payment_details'); // Numéro ou compte bancaire

            $table->date('period_start'); // Période couverte
            $table->date('period_end');

            $table->enum('status', ['pending', 'processing', 'completed', 'failed'])->default('pending');
            $table->text('notes')->nullable();

            $table->timestamp('processed_at')->nullable();
            $table->timestamps();

            $table->index('author_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('author_payouts');
    }
}
