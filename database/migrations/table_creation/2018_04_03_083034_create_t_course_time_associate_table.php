<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTCourseTimeAssociateTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t_course_time_associate', function (Blueprint $table) {
            $table->increments('course_time_associate_id');
            $table->integer('course_id')->unsigned()->nullable(false)->comment('课程ID');
            $table->integer('class_id')->unsigned()->nullable(false)->comment('班级ID');
            $table->integer('weekth_start')->unsigned()->nullable(false)->comment('开始周次');
            $table->integer('weekth_end')->unsigned()->nullable(false)->comment('结束周次');
            $table->integer('week')->unsigned()->nullable(false)->comment('周几');
            $table->integer('section')->unsigned()->nullable(false)->comment('节次');
            $table->string('weekth_type', 10)->nullable(false)->comment('周次类型');
            $table->string('position', 255)->nullable(false)->comment('地点');
            
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
        Schema::dropIfExists('t_course_time_associate');
    }
}
