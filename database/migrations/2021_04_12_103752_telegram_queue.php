<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TelegramQueue extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('telegram_queue', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->timestamp('send_at');
            $table->string('subject');
            $table->text('body');
            $table->string('template');
            $table->string('lang',2);
            $table->string('email');
            $table->timestamp('delivered_at')->nullable();
            $table->foreign('user_id')
              ->references('id')
              ->on('users')->onDelete('cascade');
          });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('telegram_queue');
    }
}
