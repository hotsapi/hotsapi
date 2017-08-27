<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePlayersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('players', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('replay_id')->unsigned();
            $table->string('battletag', 32)->index();
            $table->string('hero', 32)->nullable();
            $table->smallInteger('hero_level')->nullable();
            $table->tinyInteger('team')->nullable();
            $table->boolean('winner')->nullable();
            $table->smallInteger('region')->nullable();
            $table->integer('blizz_id')->nullable();

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
        Schema::dropIfExists('players');
    }
}
