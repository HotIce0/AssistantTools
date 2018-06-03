<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTCourseAttendanceRecordForeignKey extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \Illuminate\Support\Facades\Schema::table('t_course_attendance_record', function ($table){
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('class_id')->references('class_id')->on('t_class');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        \Illuminate\Support\Facades\Schema::table('t_course_attendance_record', function ($table){
            $table->dropForeign(['user_id']);
            $table->dropForeign(['class']);
        });
    }
}
