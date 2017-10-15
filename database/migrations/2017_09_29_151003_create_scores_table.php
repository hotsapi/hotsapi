<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateScoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('scores', function (Blueprint $table) {
            $table->integer('id')->unsigned()->primary();
            $table->foreign('id')->references('id')->on('players')->onDelete('cascade');

            $table->tinyInteger('level')->unsigned()->nullable();
            $table->tinyInteger('kills')->unsigned()->nullable();
            $table->tinyInteger('assists')->unsigned()->nullable();
            $table->tinyInteger('takedowns')->unsigned()->nullable();
            $table->tinyInteger('deaths')->unsigned()->nullable();
            $table->tinyInteger('highest_kill_streak')->unsigned()->nullable();
            $table->integer('hero_damage')->unsigned()->nullable();
            $table->integer('siege_damage')->unsigned()->nullable();
            $table->integer('structure_damage')->unsigned()->nullable();
            $table->integer('minion_damage')->unsigned()->nullable();
            $table->integer('creep_damage')->unsigned()->nullable();
            $table->integer('summon_damage')->unsigned()->nullable();
            $table->integer('time_cc_enemy_heroes')->unsigned()->nullable();
            $table->integer('healing')->unsigned()->nullable();
            $table->integer('self_healing')->unsigned()->nullable();
            $table->integer('damage_taken')->unsigned()->nullable();
            $table->integer('experience_contribution')->unsigned()->nullable();
            $table->tinyInteger('town_kills')->unsigned()->nullable();
            $table->smallInteger('time_spent_dead')->unsigned()->nullable();
            $table->tinyInteger('merc_camp_captures')->unsigned()->nullable();
            $table->tinyInteger('watch_tower_captures')->unsigned()->nullable();
            $table->integer('meta_experience')->unsigned()->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('scores');
    }
}
