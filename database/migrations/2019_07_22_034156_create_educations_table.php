<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEducationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::create('educations', function (Blueprint $table) {
        $table->bigIncrements('id');
        $table->bigInteger('account_id');
        $table->string('school');
        $table->string('degree');
        $table->string('field_of_study');
        $table->integer('year_started')->nullable();
        $table->integer('year_ended')->nullable();
        $table->string('month_started')->nullable();
        $table->string('month_ended')->nullable();
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
        Schema::dropIfExists('educations');
    }
}
