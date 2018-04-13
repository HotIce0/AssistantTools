<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTStudentInfoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t_student_info', function (Blueprint $table) {
            $table->increments('student_info_id');
            $table->string('name', 20)->nullable(false)->comment('学生姓名');
            $table->string('user_job_id', 20)->unique()->nullable(false)->comment('学号');
            $table->integer('class_id')->unsigned()->nullable(false)->comment('班级ID');
            $table->string('id_card_no', 18)->unique()->nullable(false)->comment('身份证号码');
            $table->string('email', 80)->unique()->nullable(true)->comment('邮箱');
            $table->string('phone_number', 20)->unique()->nullable(true)->comment('手机号码');

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
        Schema::dropIfExists('t_student_info');
    }
}
