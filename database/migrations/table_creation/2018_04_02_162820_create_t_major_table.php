<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTMajorTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t_major', function (Blueprint $table) {
            $table->increments('major_id');
            $table->string('major_identifier', 30)->unique()->comment('רҵ���');
            $table->string('major_name', 50)->comment('רҵ����');
            $table->integer('college_id')->unsigned()->comment('����ѧԺ');                //���

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
        Schema::dropIfExists('t_major');
    }
}
