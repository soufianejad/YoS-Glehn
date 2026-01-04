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
        Schema::table('categories', function (Blueprint $table) {
            if (! Schema::hasColumn('categories', 'space')) {
                $table->string('space')->default('public')->after('description');
            }
            if (! Schema::hasColumn('categories', 'icon')) {
                $table->string('icon')->nullable()->after('space');
            }
            if (! Schema::hasColumn('categories', 'order')) {
                $table->integer('order')->default(0)->after('icon');
            }
            if (! Schema::hasColumn('categories', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('order');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            if (Schema::hasColumn('categories', 'space')) {
                $table->dropColumn('space');
            }
            if (Schema::hasColumn('categories', 'icon')) {
                $table->dropColumn('icon');
            }
            if (Schema::hasColumn('categories', 'order')) {
                $table->dropColumn('order');
            }
            if (Schema::hasColumn('categories', 'is_active')) {
                $table->dropColumn('is_active');
            }
        });
    }
};
