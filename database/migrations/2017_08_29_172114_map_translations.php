<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MapTranslations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('map_translations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('map_id')->unsigned();
            $table->string('name')->unique()->collation('utf8_bin');

            $table->foreign('map_id')->references('id')->on('maps')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('map_translations');
    }
}
