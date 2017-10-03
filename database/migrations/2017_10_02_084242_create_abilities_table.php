<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAbilitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('abilities', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('hero_id');
            $table->string('owner')->nullable();
            $table->string('name', 128);
            $table->string('title', 128)->nullable();
            $table->text('description')->nullable();
            $table->string('icon', 128)->nullable();
            $table->string('hotkey', 8)->nullable();
            $table->integer('cooldown')->nullable();
            $table->integer('mana_cost')->nullable();
            $table->boolean('trait')->default(0);

            $table->unique(['hero_id', 'name']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('abilities');
    }
}
