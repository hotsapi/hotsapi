<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

/**
 * Class MapResource
 *
 * @package App\Http\Resources
 * @mixin \App\Map
 */
class MapResource extends Resource
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
            'translations' => $this->translations->pluck('name')
        ];
    }
}
