<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRequestLocationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('request_locations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('request_id');
            $table->string('longitude');
            $table->string('latitude');
            $table->string('route');
            $table->string('locality')->nullable();
            $table->string('region')->nullable();
            $table->string('country');
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
        Schema::dropIfExists('request_locations');
    }
}
