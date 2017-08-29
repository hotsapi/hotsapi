<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class HeroTranslations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hero_translations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('hero_id')->unsigned();
            $table->string('name')->unique()->collation('utf8_bin');

            $table->foreign('hero_id')->references('id')->on('heroes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('hero_translations');
    }
}
