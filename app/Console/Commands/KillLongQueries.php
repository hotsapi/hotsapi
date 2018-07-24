<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\QueryException;

class KillLongQueries extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hotsapi:kill-long-queries';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $processes = \DB::connection('mysql_slave')->select("SELECT id FROM INFORMATION_SCHEMA.PROCESSLIST WHERE user = ? AND command = 'execute' AND time > 60", [env('DB_USERNAME')]);
        foreach (collect($processes)->pluck('id') as $process) {
            try {
                \DB::connection('mysql_slave')->statement("KILL ?", [$process]);
            } catch (QueryException $e) {
                // thread don't exist, do nothing
            }
        }
        $this->info("Killed " . count($processes) . " queries");

        $processes = \DB::select("SELECT id FROM INFORMATION_SCHEMA.PROCESSLIST WHERE user = ? AND command = 'execute' AND time > 60", [env('DB_USERNAME')]);
        foreach (collect($processes)->pluck('id') as $process) {
            try {
                \DB::statement("KILL ?", [$process]);
            } catch (QueryException $e) {
                // thread don't exist, do nothing
            }
        }
        $this->info("Killed " . count($processes) . " queries");
    }
}
