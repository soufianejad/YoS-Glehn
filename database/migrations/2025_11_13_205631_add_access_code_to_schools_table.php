<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Do nothing. The column was added manually or in a previous conflicting migration.
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // We don't want to drop the column on rollback
        // as we are not sure about the initial state.
        // Schema::table('schools', function (Blueprint $table) {
        //     $table->dropColumn('access_code');
        // });
    }
};
