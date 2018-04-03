<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCourseTimeAssociateAttendanceRecordForeignKey extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \Illuminate\Support\Facades\Schema::table('t_attendance_record', function ($table){
            $table->foreign('course_time_associate_id')->references('course_time_associate_id')->on('t_course_time_associate');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        \Illuminate\Support\Facades\Schema::table('t_attendance_record', function ($table){
            $table->dropForeign(['course_time_associate_id']);
        });
    }
}
