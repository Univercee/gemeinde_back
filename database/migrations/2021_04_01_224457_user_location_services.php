<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UserLocationServices extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::create('user_location_services', function (Blueprint $table) {
        $table->increments('id');
        $table->integer('user_location_id')->unsigned();
        $table->integer('service_id')->unsigned();

        $table->foreign('user_location_id')
          ->references('id')
          ->on('user_locations')
          ->onDelete('cascade');

        $table->foreign('service_id')
          ->references('id')
          ->on('services');


        $table->string('channel')->nullable();
        $table->string('frequency')->nullable();
      });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
      Schema::dropIfExists('user_location_services');
    }
}
