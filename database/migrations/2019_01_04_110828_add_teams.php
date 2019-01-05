<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTeams extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('teams', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('replay_id')->unsigned();
            $table->tinyInteger('index')->unsigned();
            $table->boolean('winner')->nullable();
            $table->tinyInteger('team_level')->default(0);
            $table->integer('structure_xp')->unsigned()->default(0);
            $table->integer('creep_xp')->unsigned()->default(0);
            $table->integer('hero_xp')->unsigned()->default(0);
            $table->integer('minion_xp')->unsigned()->default(0);
            $table->integer('trickle_xp')->unsigned()->default(0);
            $table->integer('total_xp')->unsigned()->default(0);

            $table->foreign('replay_id')->references('id')->on('replays')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('teams');
    }
}
