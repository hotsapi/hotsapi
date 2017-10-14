<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

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
            'filename' => $this->filename,
            'size' => $this->size,
            'game_type' => $this->game_type,
            'game_date' => optional($this->game_date)->toDateTimeString(),
            'game_map' => optional($this->game_map)->name,
            'game_length' => $this->game_length,
            'game_version' => $this->game_version,
            'fingerprint' => $this->fingerprint,
            'region' => $this->region,
            'processed' => (bool)$this->processed,
            'url' => $this->url,
            'created_at' => optional($this->created_at)->toDateTimeString(),
            'updated_at' => optional($this->updated_at)->toDateTimeString(),
        ];
        if ($this->relationLoaded('bans')) {
            $result['bans'] = new BanResourceCollection($this->bans);
        }
        if ($this->relationLoaded('players')) {
            $result['players'] = PlayerResource::collection($this->players);
        }
        return $result;
    }
}
