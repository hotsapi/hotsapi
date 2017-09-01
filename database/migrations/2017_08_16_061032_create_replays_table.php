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
            $table->string('filename', 37)->unique();
            $table->integer('size');
            $table->string('game_type', 32)->nullable()->index();
            $table->dateTime('game_date')->nullable()->index();
            $table->integer('game_length')->nullable();
            $table->string('game_map', 32)->nullable()->index();
            $table->string('game_version', 32)->nullable();
            $table->string('fingerprint', 32)->unique();
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
