<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTSessionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t_session', function (Blueprint $table) {
            $table->increments('session_id');
            $table->string('open_id', 100)->unique()->nullable(false);
            $table->string('uuid', 100)->nullable(false);
            $table->string('skey', 100)->unique()->nullable(false)->comment('小程序session_key');
            $table->string('wx_session_id', 100)->nullable(false)->comment('微信认证服务器session_id');
            $table->string('user_info', 2048)->nullable(false);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('t_session');
    }
}
