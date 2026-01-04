<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// 7. Attribution de livres aux classes
class CreateBookAssignmentsTable extends Migration
{
    public function up(): void
    {
        Schema::create('book_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('book_id')->constrained()->onDelete('cascade');
            $table->foreignId('class_id')->constrained()->onDelete('cascade');
            $table->foreignId('school_id')->constrained()->onDelete('cascade');

            $table->date('assigned_at');
            $table->date('due_date')->nullable(); // Date limite de lecture

            $table->boolean('is_mandatory')->default(false);
            $table->text('notes')->nullable(); // Notes du professeur

            $table->timestamps();

            $table->index('class_id');
            $table->index('school_id');
            $table->unique(['book_id', 'class_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('book_assignments');
    }
}
