<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMoreStats extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('scores', function (Blueprint $table) {
            $table->integer('damage_soaked')->unsigned()->nullable();
            $table->integer('physical_damage')->unsigned()->nullable();
            $table->integer('spell_damage')->unsigned()->nullable();
            $table->integer('protection_given_to_allies')->unsigned()->nullable();
            $table->integer('teamfight_damage_taken')->unsigned()->nullable();
            $table->tinyInteger('teamfight_escapes_performed')->unsigned()->nullable();
            $table->integer('teamfight_healing_done')->unsigned()->nullable();
            $table->integer('teamfight_hero_damage')->unsigned()->nullable();
            $table->integer('time_rooting_enemy_heroes')->unsigned()->nullable();
            $table->integer('time_silencing_enemy_heroes')->unsigned()->nullable();
            $table->integer('time_stunning_enemy_heroes')->unsigned()->nullable();
            $table->tinyInteger('multikill')->unsigned()->nullable();
            $table->tinyInteger('outnumbered_deaths')->unsigned()->nullable();
            $table->tinyInteger('vengeances_performed')->unsigned()->nullable();
            $table->tinyInteger('escapes_performed')->unsigned()->nullable();
            $table->tinyInteger('clutch_heals_performed')->unsigned()->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('scores', function (Blueprint $table) {
            $table->dropColumn('damage_soaked');
            $table->dropColumn('physical_damage');
            $table->dropColumn('spell_damage');
            $table->dropColumn('protection_given_to_allies');
            $table->dropColumn('teamfight_damage_taken');
            $table->dropColumn('teamfight_escapes_performed');
            $table->dropColumn('teamfight_healing_done');
            $table->dropColumn('teamfight_hero_damage');
            $table->dropColumn('time_rooting_enemy_heroes');
            $table->dropColumn('time_silencing_enemy_heroes');
            $table->dropColumn('time_stunning_enemy_heroes');
            $table->dropColumn('multikill');
            $table->dropColumn('outnumbered_deaths');
            $table->dropColumn('vengeances_performed');
            $table->dropColumn('escapes_performed');
            $table->dropColumn('clutch_heals_performed');
        });
    }
}
