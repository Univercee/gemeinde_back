<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Events extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('events', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('location_id')->unsigned();
            $table->integer('service_id')->unsigned();
            $table->integer('external_id')->unsigned()->nullable();
            $table->timestamp('valid_from')->nullable();
            $table->timestamp('valid_until')->nullable();
            $table->string('title_en');
            $table->text('text_en');
            $table->string('title_de');
            $table->text('text_de');
          
            $table->foreign('location_id')
              ->references('id')
              ->on('locations');
            $table->foreign('service_id')
              ->references('id')
              ->on('services');
          });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::dropIfExists('events');
    }
}
