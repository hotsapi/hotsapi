<?php

namespace App\Services;

use App\Hero;
use App\HeroTranslation;
use App\MapTranslation;
use App\Player;
use App\Replay;
use Exception;
use Illuminate\Http\UploadedFile;
use Log;
use Storage;
use Symfony\Component\Process\Process;

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
    public function store(UploadedFile $file)
    {
        $parseResult = $this->parser->analyze($file->getRealPath());

        if ($parseResult->status == ParserService::STATUS_SUCCESS || (env('ALLOW_BROKEN_REPLAYS', false) && $parseResult->status == ParserService::STATUS_UPLOAD_ERROR && isset($parseResult->data))) {
            $disk = Storage::cloud();
            $filename = $parseResult->data['fingerprint']; // we already checked that this is unique among other replays
            $disk->putFileAs('', $file, "$filename.StormReplay", 'public');

            $this->translateNames($parseResult->data);

            $replay = new Replay($parseResult->data);
            $replay->filename = $filename;
            $replay->size = $file->getSize();
            $replay->region = $parseResult->region;
            // todo fix replay encodings
            $replay->save();
            foreach ($parseResult->data['players'] as $playerData) {
                $replay->players()->save(new Player($playerData));
            }

            $parseResult->replay = $replay;
        }

        return $parseResult;
    }

    private function translateNames(&$replayData)
    {
        $mapTranslation = MapTranslation::where('name', $replayData['game_map'])->with('map')->first();
        if (!$mapTranslation) {
            Log::error("Error translating map: " . $replayData['game_map']);
            $replayData['game_map'] = null;
        } else {
            $replayData['game_map'] = $mapTranslation->map->name;
        }

        $heroTranslations = HeroTranslation::whereIn('name', collect($replayData['players'])->pluck('hero'))->with('hero')->get();
        foreach ($replayData['players'] as &$player) {
            $heroTranslation = $heroTranslations->where('name', $player['hero'])->first();
            if (!$heroTranslation) {
                Log::error("Error translating hero: " . $player['hero']);
                $player['hero'] = null;
            } else {
                $player['hero'] = $heroTranslation->hero->name;
            }
        }
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
