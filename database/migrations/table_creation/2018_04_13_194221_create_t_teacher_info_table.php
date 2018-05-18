<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTTeacherInfoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t_teacher_info', function (Blueprint $table) {
            $table->increments('teacher_info_id');
            $table->string('name', 20)->nullable(false)->comment('教师姓名');
            $table->string('user_job_id', 20)->unique()->nullable(false)->comment('教师工号');
            $table->integer('college_id')->unsigned()->nullable(false)->comment('学院ID');
            $table->string('id_card_no', 18)->unique()->nullable(false)->comment('身份证号码');
            $table->string('email', 80)->unique()->nullable(true)->comment('邮箱');
            $table->string('sex', 1)->nullable(false)->comment('性别');
            $table->string('phone_number', 20)->unique()->nullable(true)->comment('手机号码');
            $table->string('school_site_password_bkjw', 255)->nullable(true)->comment('教学一体化平台登陆密码');

            $table->string('creator', 20)->nullable();
            $table->string('updater', 20)->nullable();
            $table->string('deleter', 20)->nullable();
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
        Schema::dropIfExists('t_teacher_info');
    }
}
