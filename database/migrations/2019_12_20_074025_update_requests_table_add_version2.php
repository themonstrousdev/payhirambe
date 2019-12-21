<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateRequestsTableAddVersion2 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('requests', function (Blueprint $table) {
            $table->integer('type')->after('account_id');
            $table->string('money_type')->after('type');
            $table->integer('interest')->nullable()->change();
            $table->integer('months_payable')->nullable()->change();
            $table->date('needed_on')->nullable()->change();
            $table->integer('billing_per_month')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('requests', function (Blueprint $table) {
            //
        });
    }
}
