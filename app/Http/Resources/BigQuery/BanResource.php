<?php

namespace App\Http\Resources\BigQuery;

use Illuminate\Http\Resources\Json\Resource;
use Illuminate\Support\Collection;

/**
 * Class PlayerResource
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
            'hero' => optional($this->hero)->name,
            'team' => $this->team,
            'index' => $this->index,
        ];
    }
}
