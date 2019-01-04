<?php

namespace App\Http\Resources\BigQuery;

use Illuminate\Http\Resources\Json\Resource;
use Illuminate\Support\Collection;

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
            'battletag_name' => $this->battletag_name,
            'battletag_id' => $this->battletag_id,
            'hero_level' => $this->hero_level,
            'team' => $this->team,
            'winner' => isset($this->winner) ? (bool)$this->winner : null,
            'blizz_id' => $this->blizz_id,
            'party' => $this->party,
            'silenced' => isset($this->silenced) ? (bool)$this->silenced : null,
        ];
        if ($this->relationLoaded('talents')) {
            $result['talents'] = $this->talentsToObject($this->talents);
        }
        if ($this->relationLoaded('score')) {
            $result['score'] = $this->score;
        }
        return $result;
    }

    function talentsToObject(Collection $talentsArray)
    {
        $result = [];
        foreach ([1, 4, 7, 10, 13, 16, 20] as $level) {
            $result["_". $level] = optional($talentsArray->where('pivot.level', $level)->first())->name;
        }
        return (object)$result;
    }
}
