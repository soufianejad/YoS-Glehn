<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migrations: Subscription Plans, Subscriptions, Payments
 */

// 1. Plans d'abonnement
class CreateSubscriptionPlansTable extends Migration
{
    public function up(): void
    {
        Schema::create('subscription_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Mensuel, Annuel, École 200, etc.
            $table->string('slug')->unique();
            $table->text('description');
            $table->enum('type', ['individual', 'school'])->default('individual');
            $table->decimal('price', 10, 2);
            $table->integer('duration_days'); // 30, 365, etc.

            // Pour les abonnements scolaires
            $table->integer('max_students')->nullable(); // 200, 450, null = illimité

            // Avantages
            $table->boolean('pdf_access')->default(true);
            $table->boolean('audio_access')->default(true);
            $table->boolean('download_access')->default(true);
            $table->boolean('quiz_access')->default(false);

            $table->boolean('is_active')->default(true);
            $table->integer('order')->default(0);
            $table->timestamps();

            $table->index('type');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscription_plans');
    }
}
