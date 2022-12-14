<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class Locations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('locations', function (Blueprint $table) {
            $table->id();
            $table->string('zipcode',4);
            $table->string('name_en',255)->nullable();
            $table->string("name_de",255)->nullable();
            $table->string('region',2);
            $table->double("lat");
            $table->double('lng');
            $table->string('language',2);
            $table->integer('elevation');
            $table->point('position');
            $table->spatialIndex('position');
        });        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('locations');
    }
}
