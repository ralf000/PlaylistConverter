<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeGroupsOrderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('groups_order', function (Blueprint $table) {
            $table->dropForeign('groups_order_group_id_foreign');
            $table->dropIfExists();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::create('groups_order', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('group_id')->unsigned();
            $table->tinyInteger('order')->nullable()->default(0);

            $table->foreign('group_id')->references('id')->on('channel_groups')->onDelete('cascade');
        });
    }
}
