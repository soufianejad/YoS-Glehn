<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClassesTable extends Migration
{
    public function up(): void
    {
        Schema::create('classes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->onDelete('cascade');

            $table->string('name'); // 6ème, Terminale, Licence 1, etc.
            $table->string('slug');
            $table->text('description')->nullable();
            $table->string('level'); // primaire, collège, lycée, université

            $table->integer('students_count')->default(0);

            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('school_id');
            $table->unique(['school_id', 'slug']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('classes');
    }
}
