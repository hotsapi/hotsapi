<?php

namespace App\Console\Commands;

use App\Ban;
use App\Hero;
use App\Map;
use App\Player;
use App\PlayerTalent;
use App\Replay;
use App\Score;
use App\Services\ParserService;
use App\Services\ReplayService;
use App\Talent;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use DB;
use Illuminate\Console\Command;
use Storage;

class Parse extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hotsapi:parse';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';
    /**
     * @var ParserService
     */
    private $replayService;

    /**
     * Create a new command instance.
     * @param ReplayService $replayService
     */
    public function __construct(ReplayService $replayService)
    {
        parent::__construct();
        $this->replayService = $replayService;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->parse([Replay::with('players')->orderBy('id', 'desc')->first()]);
    }

    /**
     * @param Replay[] $replays
     */
    public function parse($replays)
    {
        foreach ($replays as $replay) {
            $this->info("Parsing replay id=$replay->id, file=$replay->filename");
            $tmpFile = tempnam('', 'replay_');
            try {
                $content = Storage::cloud()->get("$replay->filename.StormReplay");
                file_put_contents($tmpFile, $content);

                $this->replayService->parseReplayExtended($tmpFile, $replay);
            } catch (\Exception $e) {
                $this->error("Error parsing file id=$replay->id, file=$replay->filename: $e");
            } finally {
                unlink($tmpFile);
            }
        }
    }


}
