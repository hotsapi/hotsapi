<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

/**
 * Class AbilityResource
 *
 * @package App\Http\Resources
 * @mixin \App\Ability
 */
class AbilityResource extends Resource
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
            'owner' => $this->owner,
            'name' => $this->name,
            'title' => $this->title,
            'description' => $this->description,
            'icon' => $this->icon,
            'hotkey' => $this->hotkey,
            'cooldown' => $this->cooldown,
            'mana_cost' => $this->mana_cost,
            'trait' => $this->trait,
        ];
    }
}
