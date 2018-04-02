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
            $table->string('user_job_id', 255)->unique()->comment('(ѧ��/����)');
            $table->string('email', 255)->unique()->nullable()->comment('[����]');
            $table->string('phone_number', 20)->unique()->nullable()->comment('[�ֻ�����]');
            $table->string('qq_number', 20)->unique()->nullable()->comment('[QQ����]');
            $table->string('wechart_name', 20)->unique()->nullable()->comment('[΢����]');
            $table->string('name', 255)->nullable(false)->comment('(����)');
            $table->integer('college_id')->unsigned()->nullable()->comment('[ѧԺID]');
            $table->integer('class_id')->unsigned()->nullable()->comment('[�༶ID]');
            $table->string('password', 255);
            $table->integer('role_id')->unsigned()->comment('��ɫID');
            $table->string('wx_open_id', 255)->unique()->nullable()->comment('[΢��open_id]');
            
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
