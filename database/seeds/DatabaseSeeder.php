<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::insert("INSERT IGNORE INTO counters (name, value) SELECT 'parsed_id', IFNULL(MAX(parsed_id), 0) FROM replays");
    }
}
