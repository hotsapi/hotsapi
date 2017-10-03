<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

/**
 * Class BanResource
 *
 * @package App\Http\Resources
 * @mixin \App\Ban
 */
class BanResource extends Resource
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
            'team' => $this->team,
            'index' => $this->index,
            'hero' => $this->hero_name,
        ];
    }
}
