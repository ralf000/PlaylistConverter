<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableChannels extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('channels', function (Blueprint $table) {
            $table->increments('id');
            $table->string('original_name', 200);
            $table->string('new_name', 200);
            $table->tinyInteger('hidden')->unsigned()->default(0);
            $table->tinyInteger('own')->unsigned()->default(0);
            $table->tinyInteger('sort')->unsigned()->default(0);
            $table->integer('group_id')->unsigned();

            $table->foreign('group_id')->references('id')->on('channel_groups')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('channels', function (Blueprint $table) {
            $table->dropForeign('channels_group_id_foreign');
            $table->dropIfExists();
        });
    }
}
