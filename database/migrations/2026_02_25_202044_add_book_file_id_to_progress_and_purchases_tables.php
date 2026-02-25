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
        Schema::table('reading_progress', function (Blueprint $table) {
            $table->foreignId('book_file_id')->nullable()->constrained('book_files')->onDelete('cascade');
        });

        Schema::table('audio_progress', function (Blueprint $table) {
            $table->foreignId('book_file_id')->nullable()->constrained('book_files')->onDelete('cascade');
        });

        Schema::table('purchases', function (Blueprint $table) {
            $table->foreignId('book_file_id')->nullable()->constrained('book_files')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reading_progress', function (Blueprint $table) {
            $table->dropForeign(['book_file_id']);
            $table->dropColumn('book_file_id');
        });

        Schema::table('audio_progress', function (Blueprint $table) {
            $table->dropForeign(['book_file_id']);
            $table->dropColumn('book_file_id');
        });

        Schema::table('purchases', function (Blueprint $table) {
            $table->dropForeign(['book_file_id']);
            $table->dropColumn('book_file_id');
        });
    }
};
