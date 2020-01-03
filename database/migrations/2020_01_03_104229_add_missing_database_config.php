<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMissingDatabaseConfig extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::getDoctrineSchemaManager()->getDatabasePlatform()->registerDoctrineTypeMapping('enum', 'string');
        Schema::table('abilities', function (Blueprint $table) {
            $table->string('owner', 191)->change();
        });

        Schema::table('bans', function (Blueprint $table) {
            $table->index('hero_name');
        });

        Schema::table('hotslogs_uploads', function(Blueprint $table)
        {
            $table->dropColumn("status");
            $table->dropColumn("result");
        });

        Schema::table('hotslogs_uploads', function(Blueprint $table)
        {
            $table->string('status', 32)->nullable()->after("replay_id");
            $table->string('result', 32)->nullable()->after("status");
            $table->index('status');
        });

        Schema::table('players', function (Blueprint $table) {
            $table->dropIndex('players_battletag_index');
            $table->index('replay_id');
            $table->index(['battletag_name', 'battletag_id']);
        });

        Schema::table('replays', function(Blueprint $table)
        {
            $table->dropColumn("fingerprint");
            $table->dropUnique('replays_parsed_id_unique');
            $table->dropIndex('replays_processed_index');
        });

        Schema::table('replays', function(Blueprint $table)
        {
            $table->string('fingerprint', 36)->after("region");
            $table->boolean('deleted')->default(0)->change();
            $table->unique('fingerprint');
            $table->unique('parsed_id');
            $table->index(['processed', 'deleted']);
            $table->index('created_at');
        });

        Schema::table('scores', function (Blueprint $table) {
            $table->integer('level')->nullable()->change();
            $table->integer('kills')->nullable()->change();
            $table->integer('assists')->nullable()->change();
            $table->integer('takedowns')->nullable()->change();
            $table->integer('deaths')->nullable()->change();
            $table->integer('highest_kill_streak')->nullable()->change();
            $table->integer('hero_damage')->nullable()->change();
            $table->integer('siege_damage')->nullable()->change();
            $table->integer('structure_damage')->nullable()->change();
            $table->integer('minion_damage')->nullable()->change();
            $table->integer('creep_damage')->nullable()->change();
            $table->integer('summon_damage')->nullable()->change();
            $table->integer('time_cc_enemy_heroes')->nullable()->change();
            $table->integer('healing')->nullable()->change();
            $table->integer('self_healing')->nullable()->change();
            $table->integer('damage_taken')->nullable()->change();
            $table->integer('experience_contribution')->nullable()->change();
            $table->integer('town_kills')->nullable()->change();
            $table->integer('time_spent_dead')->nullable()->change();
            $table->integer('merc_camp_captures')->nullable()->change();
            $table->integer('watch_tower_captures')->nullable()->change();
            $table->integer('meta_experience')->nullable()->change();

            $table->unique('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
