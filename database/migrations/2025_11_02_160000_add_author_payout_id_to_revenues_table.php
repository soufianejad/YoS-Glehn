<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAuthorPayoutIdToRevenuesTable extends Migration
{
    public function up()
    {
        Schema::table('revenues', function (Blueprint $table) {
            $table->foreignId('author_payout_id')->nullable()->constrained('author_payouts')->onDelete('set null')->after('payment_id');
        });
    }

    public function down()
    {
        Schema::table('revenues', function (Blueprint $table) {
            $table->dropConstrainedForeignId('author_payout_id');
            $table->dropColumn('author_payout_id');
        });
    }
}
