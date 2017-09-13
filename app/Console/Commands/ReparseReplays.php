<?php

namespace App\Console\Commands;

use App\Player;
use App\Replay;
use App\Services\ParserService;
use Illuminate\Console\Command;

class ReparseReplays extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hotsapi:reparse {min_id=0} {max_id=1000000000}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Redo parsing for replays';

    /**
     * @var ParserService
     */
    private $parser;

    /**
     * Create a new command instance.
     *
     * @param ParserService $parser
     */
    public function __construct(ParserService $parser)
    {
        parent::__construct();
        $this->parser = $parser;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $min_id = $this->argument('min_id');
        $max_id = $this->argument('max_id');
        $this->info("Reparsing replays, id from $min_id to $max_id");
        Replay::where('id', '>=', $min_id)->where('id', '<=', $max_id)->with('players')->chunk(100, function ($x) { return $this->reparse($x); });
    }

    /**
     * Reparse collection of replays
     *
     * @param $replays
     */
    public function reparse($replays)
    {
        foreach ($replays as $replay) {
            $this->info("Parsing replay id=$replay->id, file=$replay->filename");
            $tmpFile = tempnam('', 'replay_');
            try {
                $content = \Storage::cloud()->get("$replay->filename.StormReplay");
                file_put_contents($tmpFile, $content);
                $parseResult = $this->parser->analyze($tmpFile, true);
                if ($parseResult->status != ParserService::STATUS_SUCCESS) {
                    $this->error("Error parsing file id=$replay->id, file=$replay->filename. Status: $parseResult->status");
                    continue;
                }
                $replay->fill($parseResult->data)->save();
                foreach ($parseResult->data['players'] as $playerData) {
                    $player = $replay->players->where('blizz_id', $playerData['blizz_id'])->first();
                    if ($player) {
                        $player->fill($playerData)->save();
                    } else {
                        // apparently `create` doesn't automatically add model to attribute array
                        $replay->players []= $replay->players()->create($playerData);
                    }
                }
                if (count($replay->players) != 10) {
                    $this->error("Wrong player count " . count($replay->players) . ", replay id=$replay->id, file=$replay->filename");
                }
            } catch (\Exception $e) {
                $this->error("Error parsing file id=$replay->id, file=$replay->filename: $e");
            } finally {
                unlink($tmpFile);
            }
        }
    }
}
