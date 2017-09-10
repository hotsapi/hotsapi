<?php

namespace App\Services;

use App\Player;
use App\Replay;
use Illuminate\Http\UploadedFile;
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
     * @param UploadedFile $file
     * @return \stdClass
     */
    public function store(UploadedFile $file, $uploadToHotslogs)
    {
        $parseResult = $this->parser->analyze($file->getRealPath());

        if ($parseResult->status == ParserService::STATUS_SUCCESS || (env('ALLOW_BROKEN_REPLAYS', false) && $parseResult->status == ParserService::STATUS_UPLOAD_ERROR && isset($parseResult->data))) {
            $disk = Storage::cloud();
            $filename = $parseResult->data['fingerprint']; // we already checked that this is unique among other replays
            $disk->putFileAs('', $file, "$filename.StormReplay", 'public');

            $replay = new Replay($parseResult->data);
            $replay->filename = $filename;
            $replay->size = $file->getSize();
            $replay->region = $parseResult->region;
            // todo fix replay encodings
            $replay->save();

            // bulk insert to increase performance
            $toInsert = [];
            foreach ($parseResult->data['players'] as $playerData) {
                $toInsert [] = [
                    'replay_id' => $replay->id,
                    'battletag' => $playerData['battletag'],
                    'hero' => $playerData['hero'],
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
