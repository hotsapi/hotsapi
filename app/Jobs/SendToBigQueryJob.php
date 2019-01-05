<?php

namespace App\Jobs;

use App\Services\BigQuery;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SendToBigQueryJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $replayId;

    /**
     * Create a new job instance.
     *
     * @param $replayId
     */
    public function __construct($replayId)
    {
        $this->replayId = $replayId;
    }

    /**
     * Execute the job.
     *
     * @param BigQuery $bigQuery
     * @return void
     * @throws \Exception
     */
    public function handle(BigQuery $bigQuery)
    {
        $replay = \App\Replay::with('game_map', 'bans', 'bans.hero', 'players', 'players.hero', 'players.talents', 'players.score')
            ->find($this->replayId);
        $bigQuery->insertRow($replay);
    }
}
