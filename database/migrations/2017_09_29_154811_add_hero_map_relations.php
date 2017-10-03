<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddHeroMapRelations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('players', function (Blueprint $table) {
            $table->integer('hero_id')->unsigned()->nullable()->after('replay_id');
            $table->foreign('hero_id')->references('id')->on('heroes');
        });

        Schema::table('replays', function (Blueprint $table) {
            $table->integer('game_map_id')->unsigned()->nullable()->after('game_length');
            $table->foreign('game_map_id')->references('id')->on('maps');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('players', function (Blueprint $table) {
            //
        });
    }
}
