<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubscriptionsTable extends Migration
{
    public function up(): void
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('subscription_plan_id')->constrained()->onDelete('cascade');

            $table->date('start_date');
            $table->date('end_date');
            $table->enum('status', ['active', 'expired', 'cancelled', 'pending'])->default('pending');

            $table->boolean('auto_renew')->default(false);
            $table->timestamp('cancelled_at')->nullable();

            $table->timestamps();

            $table->index('user_id');
            $table->index('status');
            $table->index('end_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
}
