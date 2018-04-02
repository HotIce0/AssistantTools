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
            $table->integer('class_id')->unsigned()->comment('�༶ID');
            $table->integer('course_id')->unsigned()->nullable()->comment('�γ�ID');
            $table->string('course_type', 10)->nullable(false)->comment('�γ�����');
            $table->integer('leavers_num')->unsigned()->nullable(false)->comment('�������');
            $table->string('leave_detail', 512)->nullable()->comment('������');
            $table->integer('course_time')->unsigned()->nullable(false)->comment('�γ�ʱ��');
            $table->integer('mobile_num')->nullable(false)->comment('�ֻ��������');
            $table->string('mobile_detail_picture_file_name', 512)->nullable()->comment('�ֻ�������ͼƬ�ļ���');

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
