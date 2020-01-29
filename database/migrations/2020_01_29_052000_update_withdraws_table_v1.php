<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateWithdrawsTableV1 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('withdraws', function (Blueprint $table) {
            $table->dropColumn(['payload', 'payload_value', 'otp_code']);
            $table->string('currency')->default('PHP')->after('account_id');
            $table->string('bank')->after('charge');
            $table->string('acount_name')->after('bank');
            $table->string('acount_number')->after('acount_name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('withdraws', function (Blueprint $table) {
            //
        });
    }
}
