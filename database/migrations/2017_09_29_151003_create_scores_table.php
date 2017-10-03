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
            $table->integer('id')->unsigned()->unique();
            $table->primary('id');
            $table->foreign('id')->references('id')->on('players')->onDelete('cascade');

            $table->integer('level')->nullable();
            $table->integer('kills')->nullable();
            $table->integer('assists')->nullable();
            $table->integer('takedowns')->nullable();
            $table->integer('deaths')->nullable();
            $table->integer('highest_kill_streak')->nullable();
            $table->integer('hero_damage')->nullable();
            $table->integer('siege_damage')->nullable();
            $table->integer('structure_damage')->nullable();
            $table->integer('minion_damage')->nullable();
            $table->integer('creep_damage')->nullable();
            $table->integer('summon_damage')->nullable();
            $table->integer('time_cc_enemy_heroes')->nullable();
            $table->integer('healing')->nullable();
            $table->integer('self_healing')->nullable();
            $table->integer('damage_taken')->nullable();
            $table->integer('experience_contribution')->nullable();
            $table->integer('town_kills')->nullable();
            $table->integer('time_spent_dead')->nullable();
            $table->integer('merc_camp_captures')->nullable();
            $table->integer('watch_tower_captures')->nullable();
            $table->integer('meta_experience')->nullable();
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
