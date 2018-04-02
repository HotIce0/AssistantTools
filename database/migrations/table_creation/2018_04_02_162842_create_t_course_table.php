<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTCourseTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t_course', function (Blueprint $table) {
            $table->increments('course_id');
            $table->string('course_name', 255)->comment('�γ�����');
            $table->string('teacher_name', 255)->comment('�ڿν�ʦ');
            $table->integer('major_id')->unsigned()->comment('רҵID');

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
        Schema::dropIfExists('t_course');
    }
}
