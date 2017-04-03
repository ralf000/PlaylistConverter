<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeChannelGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('channel_groups', function (Blueprint $table) {
            $table->renameColumn('name', 'original_name');
            $table->string('new_name', 200);
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
            $table->dropColumn('new_name');
            $table->renameColumn('original_name', 'name');
        });
    }
}
