<?php

namespace App\Console\Commands;

use App\Player;
use App\Replay;
use App\Services\ParserService;
use DB;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Storage;

class Prune extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hotsapi:prune';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete old replays';

    /**
     * Create a new command instance.
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
        $size = 0;
        $this->info("Pruning replays");
        $bar = $this->output->createProgressBar(100); // don't know chunk count
        // possible bug -- if chunk uses offsets, it will miss replays because updated files no longer fit where clause
        $this->info("Query: " . DB::table('replays')->select('id, size, filename')->where('processed', true)->where('deleted', false)->where('game_type', '!=', 'HeroLeague')->where('created_at', '<', Carbon::createFromDate(2018, 1, 1))->orderBy('id')->toSql());        DB::table('replays')->select('id, size, filename')->where('processed', true)->where('deleted', false)->where('game_type', '!=', 'HeroLeague')->where('created_at', '<', Carbon::createFromDate(2018, 1, 1))->orderBy('id')->chunk(100000, function ($rows) use ($size, $bar) {
            foreach ($rows as $replay) {
                //Storage::cloud()->delete("$replay->filename.StormReplay");
                //DB::table('replays')->where('id', $replay->id)->update(['deleted' => false]);
                $size += $replay->size;
            }
            $bar->advance();
        });
        $bar->finish();
        $this->info("Total size: " . ($size / 1024 / 1024 / 1024 / 1024));
    }
}
