<?php

namespace App\Http\Resources\BigQuery;

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
            'parsed_id' => $this->parsed_id,
            'created_at' => optional($this->created_at)->toDateTimeString(),
            'filename' => $this->filename,
            'size' => $this->size,
            'fingerprint' => $this->fingerprint,
            'game_type' => $this->game_type,
            'game_date' => optional($this->game_date)->toDateTimeString(),
            'game_map' => optional($this->game_map)->name,
            'game_length' => $this->game_length,
            'game_version' => $this->game_version,
            'region' => $this->region,
        ];
        if ($this->relationLoaded('bans')) {
            $result['bans'] = BanResource::collection($this->bans);
        }
        if ($this->relationLoaded('players')) {
            $result['players'] = PlayerResource::collection($this->players);
        }
        if ($this->relationLoaded('teams')) {
            $result['teams'] = $this->teams;
        }
        return $result;
    }
}
