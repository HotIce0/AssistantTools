<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTCourseAttendanceRecordTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t_course_attendance_record', function (Blueprint $table) {
            $table->increments('course_id');
            $table->string('course_name', 255)->comment('课程名称');
            $table->string('teacher_name', 20)->comment('教师名称');
            $table->string('position', 255)->comment('地点');

            $table->string('school_year', 10)->comment('学年');
            $table->string('school_term', 10)->comment('学期');
            $table->integer('weekth')->unsigned()->comment('周次');
            $table->integer('week')->unsigned()->comment('星期');
            $table->integer('section')->unsigned()->comment('节次');

            $table->integer('user_id')->unsigned()->comment('用户ID');

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
        Schema::dropIfExists('t_course_attendance_record');
    }
}
