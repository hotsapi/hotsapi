<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;
use stdClass;

/**
 * Class ReplayResource
 *
 * @package App\Http\Resources
 * @mixin \App\Replay
 */
class ReplayResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        $result = [
            'id' => $this->id,
            'parsed_id' => $this->parsed_id,
            'filename' => $this->filename,
            'size' => $this->size,
            'game_type' => $this->game_type,
            'game_date' => optional($this->game_date)->toDateTimeString(),
            'game_map' => optional($this->game_map)->name,
            'game_length' => $this->game_length,
            'game_version' => $this->game_version,
            'fingerprint' => $this->fingerprint,
            'region' => $this->region,
            'processed' => $this->processed == 1,
            'deleted' => $this->deleted == 1,
            'url' => $this->url,
            'created_at' => optional($this->created_at)->toDateTimeString(),
            'updated_at' => optional($this->updated_at)->toDateTimeString(),
        ];
        if ($this->relationLoaded('bans')) {
            $result['bans'] = count($this->bans) ? new BanResourceCollection($this->bans) : null;
        }
        if ($this->relationLoaded('players')) {
            $result['players'] = new stdClass();
            foreach ($this->players as $index => $player) {
                $result['players']->{$index} = new PlayerResource($player);
            }
        }
        if ($this->relationLoaded('teams')) {
            $result['teams'] = TeamResource::collection($this->teams->sortBy('index'));
        }
        return $result;
    }
}
