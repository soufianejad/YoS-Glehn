<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // En MySQL, on modifie l'ENUM via une commande ALTER TABLE
        if (config('database.default') !== 'sqlite') {
        if (config('database.default') !== 'sqlite') {
            // En MySQL, on modifie l'ENUM via une commande ALTER TABLE
            DB::statement("ALTER TABLE adult_access MODIFY COLUMN status ENUM('pending', 'used', 'expired', 'revoked') NOT NULL DEFAULT 'pending'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // On revient à l'état initial (attention : les lignes avec 'revoked' pourraient poser problème)
        if (config('database.default') !== 'sqlite') {
        if (config('database.default') !== 'sqlite') {
            // On revient à l'état initial (attention : les lignes avec 'revoked' pourraient poser problème)
            DB::statement("ALTER TABLE adult_access MODIFY COLUMN status ENUM('pending', 'used', 'expired') NOT NULL DEFAULT 'pending'");
        }
    }
};
