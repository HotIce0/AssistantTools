<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTItemSetInfoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t_item_set_info', function (Blueprint $table) {
            $table->increments('item_id')->comment('ѡ��ID');
            $table->integer('item_no')->unsigned()->comment('ѡ����');
            $table->string('item_content_id', 10)->comment('ѡ������ID');
            $table->string('item_content', 200)->comment('ѡ������');
            $table->tinyInteger('sort_id')->unsigned()->comment('����ID');
            $table->unique(['item_no','item_content_id']);

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
        Schema::dropIfExists('t_item_set_info');
    }
}
