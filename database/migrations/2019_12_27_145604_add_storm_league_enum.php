<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddStormLeagueEnum extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('ALTER TABLE `hotsapi`.`replays` CHANGE COLUMN `game_type` `game_type` ENUM(\'QuickMatch\', \'UnrankedDraft\', \'HeroLeague\', \'TeamLeague\', \'Brawl\', \'StormLeague\') NULL DEFAULT NULL');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('ALTER TABLE `hotsapi`.`replays` CHANGE COLUMN `game_type` `game_type` ENUM(\'QuickMatch\', \'UnrankedDraft\', \'HeroLeague\', \'TeamLeague\', \'Brawl\') NULL DEFAULT NULL');
    }
}
