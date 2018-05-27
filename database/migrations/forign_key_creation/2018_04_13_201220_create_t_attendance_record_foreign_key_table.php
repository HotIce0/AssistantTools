<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTAttendanceRecordForeignKeyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \Illuminate\Support\Facades\Schema::table('t_attendance_record', function ($table){
            $table->foreign('course_id')->references('course_id')->on('t_course_attendance_record')->onDelete('cascade');//设置级联删除
            $table->foreign('user_id')->references('id')->on('users');
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
            $table->dropForeign(['course_id']);
            $table->dropForeign(['user_id']);
        });
    }
}
