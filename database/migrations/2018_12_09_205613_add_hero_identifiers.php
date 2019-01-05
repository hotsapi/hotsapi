<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddHeroIdentifiers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('heroes', function (Blueprint $table) {
           $table->string('c_hero_id', 32)->index()->nullable();
           $table->string('c_unit_id', 32)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('heroes', function (Blueprint $table) {
            $table->dropColumn('c_hero_id');
            $table->dropColumn('c_unit_id');
        });
    }
}
