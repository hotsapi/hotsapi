<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDefaultValueForDeleted extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        DB::statement('ALTER TABLE `hotsapi`.`replays` CHANGE COLUMN `deleted` `deleted` TINYINT(1) NOT NULL DEFAULT \'0\'');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('ALTER TABLE `hotsapi`.`replays` CHANGE COLUMN `deleted` `deleted` TINYINT(1) NOT NULL');
    }
}
