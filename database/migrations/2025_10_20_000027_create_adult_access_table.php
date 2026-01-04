<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdultAccessTable extends Migration
{
    public function up(): void
    {
        Schema::create('adult_access', function (Blueprint $table) {
            $table->id();
            $table->string('access_token')->unique(); // Token d'invitation unique
            $table->string('email')->nullable(); // Email de l'invité (optionnel)

            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade'); // Une fois inscrit
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade'); // Admin qui a créé l'invitation

            $table->enum('status', ['pending', 'used', 'expired'])->default('pending');
            $table->integer('max_uses')->default(1); // Nombre d'utilisations possibles
            $table->integer('uses_count')->default(0);

            $table->timestamp('expires_at')->nullable();
            $table->timestamp('used_at')->nullable();

            $table->timestamps();

            $table->index('access_token');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('adult_access');
    }
}
