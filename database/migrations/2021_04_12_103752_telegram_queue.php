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
            $table->integer('event_id')->unsigned();
            $table->string('telegram_id');
            $table->timestamp('deliver_at')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->text('body');

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
        Schema::dropIfExists('telegram_queue');
    }
}
