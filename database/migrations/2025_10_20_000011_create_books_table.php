<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBooksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->foreignId('author_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('category_id')->constrained('categories')->onDelete('cascade');
            $table->string('cover_image')->nullable();
            $table->string('pdf_file')->nullable();
            $table->string('audio_file')->nullable();
            $table->integer('pdf_pages')->nullable();
            $table->integer('audio_duration')->nullable();
            $table->string('isbn')->nullable();
            $table->integer('published_year')->nullable();
            $table->string('language')->nullable();
            $table->string('space')->nullable();
            $table->string('content_type')->nullable();
            $table->float('pdf_price')->nullable();
            $table->float('audio_price')->nullable();
            $table->string('status')->default('draft');
            $table->timestamps();
        });

        // Création de la table pivot pour la relation many-to-many avec les tags
        Schema::create('book_tag', function (Blueprint $table) {
            $table->id();
            $table->foreignId('book_id')->constrained('books')->onDelete('cascade');
            $table->foreignId('tag_id')->constrained('tags')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('book_tag');
        Schema::dropIfExists('books');
    }
}
