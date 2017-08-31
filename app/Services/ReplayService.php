<?php

namespace App\Services;

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
    public function store(UploadedFile $file)
    {
        $parseResult = $this->parser->analyze($file->getRealPath());

        if ($parseResult->status == ParserService::STATUS_SUCCESS || (env('ALLOW_BROKEN_REPLAYS', false) && $parseResult->status == ParserService::STATUS_UPLOAD_ERROR && isset($parseResult->data))) {
            $disk = Storage::cloud();
            $filename = $parseResult->data['fingerprint_v2']; // we already checked that this is unique among other replays
            $disk->putFileAs('', $file, "$filename.StormReplay", 'public');

            $replay = new Replay($parseResult->data);
            $replay->filename = $filename;
            $replay->size = $file->getSize();
            // todo fix replay encodings
            $replay->save();
            foreach ($parseResult->data['players'] as $playerData) {
                $replay->players()->create($playerData);
            }

            $parseResult->replay = $replay;
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
