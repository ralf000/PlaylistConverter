<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExcludeGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('exclude_groups', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title', 200);
            $table->integer('group_id')->unsigned();
        });

        Schema::table('exclude_groups', function (Blueprint $table) {
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
        Schema::table('exclude_groups', function (Blueprint $table){
            $table->dropForeign('exclude_groups_group_id_foreign');
            $table->dropIfExists();
        });
    }
}
