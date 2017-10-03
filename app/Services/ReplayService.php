<?php

namespace App\Services;

use App\Ban;
use App\Player;
use App\PlayerTalent;
use App\Replay;
use App\Score;
use Storage;

class ReplayService
{
    /**
     * @var ParserService
     */
    private $parser;

    /**
     * ReplayService constructor.
     *
     * @param ParserService $parser
     */
    public function __construct(ParserService $parser)
    {
        $this->parser = $parser;
    }

    /**
     * Store replay file
     *
     * @param \Illuminate\Http\File|\Illuminate\Http\UploadedFile $file
     * @param bool $uploadToHotslogs
     * @return \stdClass
     */
    public function store($file, $uploadToHotslogs = false)
    {
        $parseResult = $this->parser->analyze($file->getRealPath());

        if ($parseResult->status == ParserService::STATUS_SUCCESS || (env('ALLOW_BROKEN_REPLAYS', false) && $parseResult->status == ParserService::STATUS_UPLOAD_ERROR && isset($parseResult->data))) {
            $disk = Storage::cloud();
            $filename = $parseResult->data['fingerprint']; // we already checked that this is unique among other replays
            $disk->putFileAs('', $file, "$filename.StormReplay", 'public');

            $replay = new Replay($parseResult->data);
            $replay->filename = $filename;
            $replay->size = $file->getSize();
            // todo fix replay encodings
            $replay->save();

            // bulk insert to increase performance
            $toInsert = [];
            foreach ($parseResult->data['players'] as $playerData) {
                $toInsert [] = [
                    'replay_id' => $replay->id,
                    'battletag_name' => $playerData['battletag_name'],
                    'hero_id' => $playerData['hero_id'],
                    'hero_level' => $playerData['hero_level'],
                    'team' => $playerData['team'],
                    'winner' => $playerData['winner'],
                    'blizz_id' => $playerData['blizz_id'],
                ];
            }
            Player::insert($toInsert);

            $parseResult->replay = $replay;
        }

        if (isset($parseResult->replay) && $uploadToHotslogs) {
            HotslogsUploader::queueForUpload($parseResult->replay);
        }

        return $parseResult;
    }

    /**
     * @param $filename
     * @param $replay
     */
    public function parseReplayExtended($filename, Replay $replay)
    {
        $data = $this->parser->analyzeExtended($filename, $replay);
        if ($data['talents']) {
            PlayerTalent::insertOnDuplicateKey($data['talents']);
        }
        if ($data['scores']) {
            Score::insertOnDuplicateKey($data['scores']);
        }
        if ($data['bans']) {
            Ban::insertOnDuplicateKey($data['bans']);
        }
        if ($data['players']) {
            Player::insertOnDuplicateKey($data['players']);
        }
        $replay->processed = 1;
        $replay->save();
    }

    /**
     * Remove replay and all associated entities from DB and cloud storage
     *
     * @param Replay $replay
     */
    public function delete(Replay $replay)
    {
        Storage::cloud()->delete("$replay->filename.StormReplay");
        $replay->delete();
    }
}
