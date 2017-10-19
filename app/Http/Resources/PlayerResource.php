<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

/**
 * Class PlayerResource
 *
 * @package App\Http\Resources
 * @mixin \App\Player
 */
class PlayerResource extends Resource
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
            'hero' => optional($this->hero)->name,
            'hero_level' => $this->hero_level,
            'team' => $this->team,
            'winner' => isset($this->winner) ? (bool)$this->winner : null,
            'blizz_id' => $this->blizz_id,
            'party' => $this->party,
            'silenced' => isset($this->silenced) ? (bool)$this->silenced : null,
            'battletag' => $this->battletag,
        ];
        if ($this->relationLoaded('talents')) {
            $result['talents'] = count($this->talents) ? $this->talents->mapWithKeys(function($x) { return [$x->pivot->level => $x->name]; }) : null;
        }
        if ($this->relationLoaded('score')) {
            $result['score'] = $this->score;
        }
        return $result;
    }
}
