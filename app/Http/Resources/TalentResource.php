<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

/**
 * Class TalentResource
 *
 * @package App\Http\Resources
 * @mixin \App\Talent
 */
class TalentResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'name' => $this->name,
            'title' => $this->title,
            'description' => $this->description,
            'icon' => $this->icon,
            'ability' => $this->ability_id,
            'sort' => $this->sort,
            'cooldown' => $this->cooldown,
            'mana_cost' => $this->mana_cost,
            'level' => $this->level,
        ];
    }
}
