<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UserLocations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::create('user_locations', function (Blueprint $table) {
        $table->increments('id');
        $table->integer('user_id')->unsigned();
        $table->integer('location_id')->unsigned();

        $table->foreign('user_id')
          ->references('id')
          ->on('users');

        $table->foreign('location_id')
          ->references('id')
          ->on('locations');

        $table->string('title')->nullable();

        $table->string('street_name')->nullable();
        $table->string('street_number')->nullable();
      });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
      Schema::dropIfExists('user_locations');
    }
}
