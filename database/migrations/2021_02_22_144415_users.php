<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Users extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('email',255)->nullable()->unique()->index();
            $table->string('telegram_id',255)->nullable()->unique()->index();
            $table->string('first_name',255)->nullable();
            $table->string('last_name',255)->nullable();
            $table->string('language',255)->default('en');
            $table->string('username',255)->nullable();
            $table->string('avatar',255)->nullable();
            $table->timestamp('registered_at')->nullable();
            $table->string('secretkey', 255)->nullable();
            $table->timestamp('key_until')->nullable();
            $table->string('auth_type',2)->nullable();
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
        Schema::dropIfExists('users');
    }
}
