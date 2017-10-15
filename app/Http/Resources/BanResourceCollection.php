<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class BanResourceCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        $teams = $this->collection->groupBy('team');
        return [
            $teams[0]->sortBy('index')->pluck('hero')->pluck('name'),
            $teams[1]->sortBy('index')->pluck('hero')->pluck('name')
        ];
    }
}
