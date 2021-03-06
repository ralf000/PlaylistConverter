<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeChannelGroupsTable4 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('channel_groups', function (Blueprint $table) {
            $table->tinyInteger('sort')->after('hidden')->unsigned()->default(0);
            $table->tinyInteger('own')->unsigned()->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('channel_groups', function (Blueprint $table) {
            $table->dropColumn('own');
            $table->dropColumn('sort');
        });
    }
}
