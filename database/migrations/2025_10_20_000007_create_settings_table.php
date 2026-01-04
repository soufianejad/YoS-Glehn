<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSettingsTable extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique(); // site_name, contact_email, etc.
            $table->text('value')->nullable();
            $table->string('type')->default('text'); // text, number, boolean, json
            $table->string('group')->default('general'); // general, payment, email, etc.
            $table->timestamps();

            $table->index('key');
            $table->index('group');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
}
