<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddShareColumnsToBooksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('books', function (Blueprint $table) {
            $table->integer('author_share')->nullable()->after('audio_price')->comment('Author\'s revenue share percentage');
            $table->integer('platform_share')->nullable()->after('author_share')->comment('Platform\'s revenue share percentage');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('books', function (Blueprint $table) {
            $table->dropColumn(['author_share', 'platform_share']);
        });
    }
}
