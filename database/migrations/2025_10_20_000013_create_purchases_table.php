<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchasesTable extends Migration
{
    public function up(): void
    {
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('book_id')->constrained()->onDelete('cascade');
            $table->foreignId('payment_id')->constrained()->onDelete('cascade');

            $table->enum('purchase_type', ['pdf', 'audio'])->default('pdf');
            $table->decimal('price', 10, 2);

            $table->timestamp('access_until')->nullable(); // Accès limité dans le temps
            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->index('user_id');
            $table->index('book_id');
            $table->unique(['user_id', 'book_id', 'purchase_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchases');
    }
}
