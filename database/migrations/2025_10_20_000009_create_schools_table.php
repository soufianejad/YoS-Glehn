<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSchoolsTable extends Migration
{
    public function up(): void
    {
        Schema::create('schools', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Compte administrateur école

            $table->string('name');
            $table->string('slug')->unique();
            $table->text('address');
            $table->string('city');
            $table->string('country')->default('CI'); // Côte d'Ivoire par défaut
            $table->string('phone');
            $table->string('email');

            // Code d'accès unique pour les élèves
            $table->string('access_code')->unique(); // Ex: LYCEE-2410
            $table->string('qr_code_path')->nullable(); // QR Code pour inscription

            // Abonnement
            $table->foreignId('subscription_id')->nullable()->constrained()->onDelete('set null');
            $table->integer('max_students')->default(200);
            $table->integer('current_students')->default(0);

            // Logo et personnalisation
            $table->string('logo')->nullable();
            $table->string('primary_color')->default('#1e40af');

            $table->boolean('is_active')->default(true);
            $table->enum('status', ['pending', 'approved', 'suspended'])->default('pending');

            $table->timestamps();
            $table->softDeletes();

            $table->index('access_code');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('schools');
    }
}
