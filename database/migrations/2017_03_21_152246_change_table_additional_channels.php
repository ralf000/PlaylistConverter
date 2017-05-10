<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeTableAdditionalChannels extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('additional_channels', function (Blueprint $table) {
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
        Schema::table('additional_channels', function (Blueprint $table){
            $table->dropForeign('additional_channels_group_id_foreign');
        });
    }
}
