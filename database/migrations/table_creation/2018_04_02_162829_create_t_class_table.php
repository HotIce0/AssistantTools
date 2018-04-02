<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTClassTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t_class', function (Blueprint $table) {
            $table->increments('class_id');
            $table->string('class_identifier', 20)->unique()->comment('�༶���');
            $table->string('class_name', 50)->comment('�༶����');
            $table->integer('major_id')->unsigned()->comment('����רҵ');

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
        Schema::dropIfExists('t_class');
    }
}
