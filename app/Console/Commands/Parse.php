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

    private $run = true;

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
        declare(ticks=1);
        pcntl_signal(SIGINT, [$this, 'shutdown']);
        pcntl_signal(SIGTERM, [$this, 'shutdown']);

        $this->info('Parser started...');
        while($this->run) {
            try {
                DB::statement("SET @update_id := 0;");
                DB::update("UPDATE replays SET processed = -1, id = (SELECT @update_id := id) WHERE processed = 0 LIMIT 1;");
                $id = DB::select("SELECT @update_id as id;")[0]->id;
            } catch (\Exception $e) {
                $this->warn("Error getting id, retrying: $e");
                continue;
            }
            if (!$id) {
                sleep(3);
                continue;
            }
            $this->parse(Replay::with('players')->find($id));
        }
    }

    /**
     * @param Replay $replay
     */
    public function parse(Replay $replay)
    {
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

    public function shutdown()
    {
        $this->info('Gracefully stopping parser...');
        $this->run = false;
    }
}
