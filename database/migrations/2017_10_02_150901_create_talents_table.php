<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTalentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('talents', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 128)->unique();
            $table->string('title', 128)->nullable();
            $table->text('description')->nullable();
            $table->string('icon', 128)->nullable();
            $table->tinyInteger('level')->nullable();
            $table->string('ability_id', 128)->nullable();
            $table->tinyInteger('sort')->nullable();
            $table->integer('cooldown')->nullable();
            $table->integer('mana_cost')->nullable();
        });

        Schema::create('player_talent', function (Blueprint $table) {
            $table->integer('player_id')->unsigned();
            $table->integer('talent_id')->unsigned();
            $table->tinyInteger('level');
            $table->primary(['player_id', 'level']);
            $table->foreign('player_id')->references('id')->on('players')->onDelete('cascade');
            $table->foreign('talent_id')->references('id')->on('talents');
        });

        Schema::create('hero_talent', function (Blueprint $table) {
            $table->integer('hero_id')->unsigned();
            $table->integer('talent_id')->unsigned();
            $table->primary(['hero_id', 'talent_id']);
            $table->foreign('hero_id')->references('id')->on('heroes');
            $table->foreign('talent_id')->references('id')->on('talents');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('talents');
        Schema::dropIfExists('player_talent');
    }
}
