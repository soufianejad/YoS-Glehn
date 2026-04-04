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
        Schema::table('notifications', function (Blueprint $table) {
            if (config('database.default') !== 'sqlite') {
                // In MySQL, we modify the ENUM via an ALTER TABLE command or change it to string
                DB::statement("ALTER TABLE notifications MODIFY COLUMN type VARCHAR(255) NOT NULL DEFAULT 'info'");
            } else {
                $table->string('type')->default('info')->change();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            if (config('database.default') !== 'sqlite') {
                DB::statement("ALTER TABLE notifications MODIFY COLUMN type ENUM('info', 'success', 'warning', 'danger') NOT NULL DEFAULT 'info'");
            } else {
                $table->enum('type', ['info', 'success', 'warning', 'danger'])->default('info')->change();
            }
        });
    }
};
