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
     * @var ParserService
     */
    private $parser;

    /**
     * Create a new command instance.
     *
     * @param ParserService $replay
     */
    public function __construct(ParserService $replay)
    {
        parent::__construct();
        $this->parser = $replay;
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
        DB::table('replays')->where('processed', true)->where('deleted', false)->where('game_type', '!=', 'HeroLeague')->where('created_at', '<', Carbon::createFromDate(2018, 1, 1))->orderBy('id')->chunk(1000, function ($rows, $size) {
            foreach ($rows as $replay) {
                //Storage::cloud()->delete("$replay->filename.StormReplay");
                //DB::table('replays')->where('id', $replay->id)->update(['deleted' => false]);
                $size += $replay->size;
            }
        });
        $this->info("Total size: " . ($size / 1024 / 1024 / 1024 / 1024));
    }
}
