<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTCourseTimeAssociateForeignKeyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \Illuminate\Support\Facades\Schema::table('t_course_time_associate', function ($table){
            $table->foreign('course_id')->references('course_id')->on('t_course');
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
        \Illuminate\Support\Facades\Schema::table('t_course_time_associate', function ($table){
            $table->dropForeign(['course_id']);
            $table->dropForeign(['class_id']);
        });
    }
}
