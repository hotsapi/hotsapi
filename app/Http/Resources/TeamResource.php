<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

/**
 * Class TalentResource
 *
 * @package App\Http\Resources
 * @mixin \App\Team
 */
class TeamResource extends Resource
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
            'first_pick' => $this->first_pick == 1,
            'winner' => $this->winner == 1,
            'team_level' => $this->team_level,
            'structure_xp' => $this->structure_xp,
            'creep_xp' => $this->creep_xp,
            'hero_xp' => $this->hero_xp,
            'minion_xp' => $this->minion_xp,
            'trickle_xp' => $this->trickle_xp,
            'total_xp' => $this->total_xp,
        ];
        return $result;
    }
}

