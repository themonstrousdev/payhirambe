<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTransferChargesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transfer_charges', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('type');
            $table->decimal('min_amount', 8, 2);
            $table->decimal('max_amount', 8, 2);
            $table->decimal('charge', 8, 2);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transfer_charges');
    }
}
