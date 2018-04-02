<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTAttendanceRecordTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t_attendance_record', function (Blueprint $table) {
            $table->increments('attendance_record_id');
            $table->integer('class_id')->unsigned()->comment('班级ID');
            $table->integer('course_id')->unsigned()->nullable()->comment('课程ID');
            $table->string('course_type', 10)->nullable(false)->comment('课程类型');
            $table->integer('leavers_num')->unsigned()->nullable(false)->comment('请假人数');
            $table->string('leave_detail', 512)->nullable()->comment('请假情况');
            $table->integer('course_time')->unsigned()->nullable(false)->comment('课程时间');
            $table->integer('mobile_num')->nullable(false)->comment('手机入袋数量');
            $table->string('mobile_detail_picture_file_name', 512)->nullable()->comment('手机入袋情况图片文件名');

            $table->string('creator', 20)->nullable();
            $table->string('updater', 20)->nullable();
            $table->string('deleter', 20)->nullable();
            $table->softDeletes();
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
        Schema::dropIfExists('t_attendance_record');
    }
}
