<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class EmailQueue extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('email_queue', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->integer('event_id')->unsigned();
            $table->string('email');
            $table->timestamp('deliver_at')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->string('title');
            $table->text('body');
            $table->integer('template_id');
          
            $table->foreign('user_id')
              ->references('id')
              ->on('users')->onDelete('cascade');
            $table->foreign('event_id')
              ->references('id')
              ->on('events')->onDelete('cascade');
            $table->unique(['user_id', 'event_id']);
          });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('email_queue');
    }
}
