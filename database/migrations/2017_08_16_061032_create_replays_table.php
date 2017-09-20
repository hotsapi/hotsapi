<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReplaysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('replays', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->string('filename', 36)->unique();
            $table->integer('size')->unsigned();
            $table->enum('game_type', ['QuickMatch', 'UnrankedDraft', 'HeroLeague', 'TeamLeague', 'Brawl'])->nullable()->index();
            $table->dateTime('game_date')->nullable()->index();
            $table->smallInteger('game_length')->nullable()->unsigned();
            $table->string('game_map', 32)->nullable()->index();
            $table->string('game_version', 32)->nullable();
            $table->string('fingerprint', 36)->unique();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('replays');
    }
}
