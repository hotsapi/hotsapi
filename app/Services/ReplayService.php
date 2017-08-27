<?php

namespace App\Services;

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

        if ($parseResult->status == ParserService::STATUS_SUCCESS || (env('ALLOW_BROKEN_REPLAYS', false) && $parseResult->status == ParserService::STATUS_PARSE_ERROR && isset($parseResult->data))) {
            $disk = Storage::cloud();
            do {
                $filename = uniqid();
            } while ($disk->exists($filename));
            $disk->putFileAs('', $file, "$filename.StormReplay");

            $replay = new Replay($parseResult->data);
            $replay->filename = $filename;
            $replay->size = $file->getSize();
            $replay->save();
            foreach ($parseResult->data['players'] as $playerData) {
                $replay->players()->save(new Player($playerData));
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
