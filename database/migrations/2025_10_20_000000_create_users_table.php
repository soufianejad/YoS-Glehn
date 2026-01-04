<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: Create users table
 * Description: Table principale pour tous les utilisateurs (multi-rôles)
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->string('password');
            $table->enum('role', ['admin', 'author', 'school', 'student', 'reader', 'adult_reader'])->default('reader');
            $table->string('avatar')->nullable();
            $table->string('language')->default('fr'); // fr, en, baoulé, etc.
            $table->boolean('is_active')->default(true);
            $table->boolean('is_verified')->default(false);
            $table->timestamp('email_verified_at')->nullable();
            $table->foreignId('school_id')->nullable(); // Pour les étudiants
            $table->string('school_code')->nullable(); // Code d'accès école
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();

            // Index pour améliorer les performances
            $table->index('role');
            $table->index('school_id');
            $table->index('school_code');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
