<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('id_card_no', 18)->unique()->nullable(false)->comment('身份证号码');
            $table->string('user_job_id', 255)->unique()->comment('(学号/工号)');
            $table->string('email', 255)->unique()->nullable()->comment('[邮箱]');
            $table->string('phone_number', 20)->unique()->nullable()->comment('[手机号码]');
            $table->string('qq_number', 20)->unique()->nullable()->comment('[QQ号码]');
            $table->string('wechart_name', 20)->unique()->nullable()->comment('[微信名]');
            $table->string('name', 255)->nullable(false)->comment('(姓名)');
            $table->integer('college_id')->unsigned()->nullable()->comment('[学院ID]');
            $table->integer('class_id')->unsigned()->nullable()->comment('[班级ID]');
            $table->string('password', 255);
            $table->integer('role_id')->unsigned()->comment('角色ID');
            $table->integer('session_id')->unsigned()->unique()->nullable()->comment('[会话ID]');
            
            $table->rememberToken();

            $table->string('creator', 20)->nullable();
            $table->string('updater', 20)->nullable();
            $table->string('deleter', 20)->nullable();
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
        Schema::dropIfExists('users');
    }
}
