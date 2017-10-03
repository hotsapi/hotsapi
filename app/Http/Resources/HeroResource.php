<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

/**
 * Class Hero
 *
 * @package App\Http\Resources
 * @mixin \App\Hero
 */
class HeroResource extends Resource
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
            'translations' => $this->translations->pluck('name'),
            'role' => $this->role,
            'type' => $this->type,
            'release_date' => $this->release_date,
            'icon_url' => $this->icon_url,
            'abilities' => AbilityResource::collection($this->abilities),
            'talents' => TalentResource::collection($this->talents),
        ];
    }
}
