<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCountersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('counters', function (Blueprint $table) {
            $table->string('name', 32)->unique();
            $table->integer('value');
        });

        #$max = DB::selectOne("SELECT MAX(parsed_id) AS max FROM replays")->max;
        #DB::insert("INSERT INTO counters VALUES ('parsed_id', ?)", [$max]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('counters');
    }
}
