<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHotslogsUploadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hotslogs_uploads', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->integer('replay_id')->unsigned();
            $table->enum('status', ['success', 'error', 'skipped'])->nullable()->index();
            $table->enum('result', ['prealphawipe', 'computerplayerfound', 'trymemode', 'duplicate', 'duplicate-upload', 'exception', 'unexpectedresult'])->nullable();

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
        Schema::dropIfExists('hotslogs_uploads');
    }
}
