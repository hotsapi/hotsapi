<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bans', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('replay_id')->unsigned();
            $table->integer('hero_id')->unsigned()->nullable();
            $table->char('hero_name', 4)->nullable();
            $table->tinyInteger('team');
            $table->tinyInteger('index');

            $table->unique(['replay_id', 'team', 'index']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bans');
    }
}
